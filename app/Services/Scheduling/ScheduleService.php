<?php

namespace App\Services\Scheduling;

use App\Enums\Scheduling\ConflictType;
use App\Enums\Scheduling\ScheduleStatus;
use App\Events\Scheduling\TimetablePublished;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    public function checkConflicts(array $scheduleData, ?int $ignoreScheduleId = null): array
    {
        $query = Schedule::query()->where('semester_id', $scheduleData['semester_id']);

        if ($ignoreScheduleId !== null) {
            $query->where('id', '!=', $ignoreScheduleId);
        }

        $schedules = $query
            ->where('thu', $scheduleData['thu'])
            ->where(function ($q) use ($scheduleData): void {
                $start = (int) $scheduleData['tiet_bat_dau'];
                $end = $start + (int) $scheduleData['so_tiet'] - 1;
                $q->whereRaw('(tiet_bat_dau + so_tiet - 1) >= ?', [$start])
                    ->whereRaw('tiet_bat_dau <= ?', [$end]);
            })
            ->get();

        $conflicts = [];

        foreach ($schedules as $schedule) {
            if ((int) $schedule->gv_id === (int) $scheduleData['gv_id']) {
                $conflicts[] = $this->buildConflict(ConflictType::TEACHER_CONFLICT, $schedule, 'Giảng viên đã có lịch dạy trùng giờ.');
            }

            if ((int) $schedule->room_id === (int) $scheduleData['room_id']) {
                $conflicts[] = $this->buildConflict(ConflictType::ROOM_CONFLICT, $schedule, 'Phòng học đã được đặt trùng giờ.');
            }

            if ((int) $schedule->lop_id === (int) $scheduleData['lop_id']) {
                $conflicts[] = $this->buildConflict(ConflictType::CLASS_CONFLICT, $schedule, 'Lớp học đã có môn học trùng giờ.');
            }
        }

        $teacherWeeklyLoad = Schedule::query()
            ->where('semester_id', $scheduleData['semester_id'])
            ->where('gv_id', $scheduleData['gv_id'])
            ->sum('so_tiet') + (int) $scheduleData['so_tiet'];

        if ($teacherWeeklyLoad > 20) {
            $conflicts[] = [
                'type' => ConflictType::TEACHER_OVERLOAD->value,
                'detail' => 'Giảng viên vượt ngưỡng 20 tiết/tuần (cảnh báo).',
                'conflict_schedule_id' => null,
            ];
        }

        $blockingTypes = [
            ConflictType::TEACHER_CONFLICT->value,
            ConflictType::ROOM_CONFLICT->value,
            ConflictType::CLASS_CONFLICT->value,
        ];

        return [
            'has_conflict' => collect($conflicts)->contains(fn (array $conflict): bool => in_array($conflict['type'], $blockingTypes, true)),
            'conflicts' => $conflicts,
        ];
    }

    public function suggestSlot(array $constraints): array
    {
        $suggestions = [];

        for ($day = 2; $day <= 7; $day++) {
            for ($start = 1; $start <= 8; $start++) {
                $candidate = [
                    ...$constraints,
                    'thu' => $day,
                    'tiet_bat_dau' => $start,
                ];

                $conflictCheck = $this->checkConflicts($candidate);
                if (! $conflictCheck['has_conflict']) {
                    $suggestions[] = [
                        'thu' => $day,
                        'tiet_bat_dau' => $start,
                        'room_id' => $constraints['room_id'],
                    ];
                }

                if (count($suggestions) >= 20) {
                    return $suggestions;
                }
            }
        }

        return $suggestions;
    }

    public function create(array $data): Schedule
    {
        $conflict = $this->checkConflicts($data);
        if ($conflict['has_conflict']) {
            abort(422, 'Không thể tạo lịch vì có xung đột.');
        }

        return Schedule::create($data);
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        if ($schedule->trang_thai !== ScheduleStatus::DRAFT->value) {
            abort(422, 'Chỉ lịch draft mới được chỉnh sửa.');
        }

        $conflict = $this->checkConflicts($data, $schedule->id);
        if ($conflict['has_conflict']) {
            abort(422, 'Không thể cập nhật lịch vì có xung đột.');
        }

        $schedule->update($data);

        return $schedule;
    }

    public function publish(int $semesterId, int $publishedBy): void
    {
        Schedule::query()
            ->where('semester_id', $semesterId)
            ->where('trang_thai', ScheduleStatus::DRAFT->value)
            ->update(['trang_thai' => ScheduleStatus::PUBLISHED->value]);

        event(new TimetablePublished($semesterId, $publishedBy));
    }

    public function moveSession(Schedule $schedule, array $newData, int $movedBy): Schedule
    {
        $payload = [
            'course_id' => $schedule->course_id,
            'gv_id' => $schedule->gv_id,
            'room_id' => $newData['room_id'] ?? $schedule->room_id,
            'semester_id' => $schedule->semester_id,
            'lop_id' => $schedule->lop_id,
            'thu' => $newData['thu'],
            'tiet_bat_dau' => $newData['tiet_bat_dau'],
            'so_tiet' => $newData['so_tiet'] ?? $schedule->so_tiet,
            'tuan_bat_dau' => $newData['tuan_bat_dau'] ?? $schedule->tuan_bat_dau,
            'tuan_ket_thuc' => $newData['tuan_ket_thuc'] ?? $schedule->tuan_ket_thuc,
            'trang_thai' => $schedule->trang_thai,
            'ghi_chu' => $newData['ghi_chu'] ?? $schedule->ghi_chu,
        ];

        $conflict = $this->checkConflicts($payload, $schedule->id);
        if ($conflict['has_conflict']) {
            abort(422, 'Không thể dời buổi học do xung đột.');
        }

        $schedule->update($payload);

        return $schedule;
    }

    public function roomAvailability(int $semesterId, int $thu, int $tietBatDau): array
    {
        $occupiedRoomIds = Schedule::query()
            ->where('semester_id', $semesterId)
            ->where('thu', $thu)
            ->where('tiet_bat_dau', '<=', $tietBatDau)
            ->whereRaw('(tiet_bat_dau + so_tiet - 1) >= ?', [$tietBatDau])
            ->pluck('room_id')
            ->all();

        return DB::table('rooms')
            ->whereNotIn('id', $occupiedRoomIds)
            ->where('trang_thai', 'available')
            ->get()
            ->toArray();
    }

    private function buildConflict(ConflictType $type, Schedule $schedule, string $detail): array
    {
        return [
            'type' => $type->value,
            'detail' => $detail,
            'conflict_schedule_id' => $schedule->id,
        ];
    }
}
