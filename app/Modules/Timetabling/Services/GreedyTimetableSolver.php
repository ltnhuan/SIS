<?php

namespace App\Modules\Timetabling\Services;

use App\Modules\Core\Models\Policy;
use App\Modules\Core\Models\Room;
use App\Modules\Core\Models\TimeSlot;
use App\Modules\Enrollment\Models\ClassSection;
use App\Modules\Timetabling\Models\TimetableAssignment;
use App\Modules\Timetabling\Models\TimetableConflict;
use App\Modules\Timetabling\Models\TimetableRun;

class GreedyTimetableSolver
{
    public function run(TimetableRun $run): array
    {
        $assignments = [];
        $conflicts = [];

        $classSections = ClassSection::where('term_id', $run->term_id)->get();
        $rooms = Room::all();
        $timeSlots = TimeSlot::where('is_enabled', true)->get();
        $blockedPolicies = Policy::where('rule_type', 'BLOCK_TIME')->where('is_enabled', true)->get();

        $occupied = [
            'room' => [],
            'teacher' => [],
        ];

        foreach ($classSections as $classSection) {
            $requiresLab = (bool) ($classSection->requires_lab_bool ?? false);
            $teacherId = $classSection->teacher_id;
            $assigned = false;

            foreach ($timeSlots as $timeSlot) {
                $isTeacherBlocked = $blockedPolicies
                    ->where('params_json.teacher_id', $teacherId)
                    ->where('params_json.weekday', $timeSlot->weekday)
                    ->isNotEmpty();

                if ($isTeacherBlocked) {
                    continue;
                }

                foreach ($rooms as $room) {
                    if ($requiresLab && empty($room->equipment_json['lab'])) {
                        continue;
                    }

                    $roomKey = $room->id . ':' . $timeSlot->id;
                    $teacherKey = $teacherId . ':' . $timeSlot->id;

                    if (isset($occupied['room'][$roomKey]) || isset($occupied['teacher'][$teacherKey])) {
                        continue;
                    }

                    $assignment = TimetableAssignment::create([
                        'timetable_run_id' => $run->id,
                        'class_section_id' => $classSection->id,
                        'room_id' => $room->id,
                        'time_slot_id' => $timeSlot->id,
                        'teacher_id' => $teacherId,
                        'status' => 'assigned',
                    ]);

                    $occupied['room'][$roomKey] = true;
                    $occupied['teacher'][$teacherKey] = true;
                    $assignments[] = $assignment;
                    $assigned = true;
                    break;
                }

                if ($assigned) {
                    break;
                }
            }

            if (! $assigned) {
                $conflicts[] = TimetableConflict::create([
                    'timetable_run_id' => $run->id,
                    'conflict_type' => 'NO_SLOT',
                    'detail_json' => [
                        'class_section_id' => $classSection->id,
                        'reason' => 'Không tìm được phòng/ca phù hợp theo chính sách.',
                    ],
                ]);
            }
        }

        return [
            'assignments' => $assignments,
            'conflicts' => $conflicts,
        ];
    }
}
