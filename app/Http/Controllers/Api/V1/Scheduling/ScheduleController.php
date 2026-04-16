<?php

namespace App\Http\Controllers\Api\V1\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\CheckScheduleConflictsRequest;
use App\Http\Requests\Scheduling\MoveScheduleRequest;
use App\Http\Requests\Scheduling\RoomAvailabilityRequest;
use App\Http\Requests\Scheduling\StoreScheduleRequest;
use App\Http\Requests\Scheduling\SuggestScheduleSlotRequest;
use App\Http\Requests\Scheduling\UpdateScheduleRequest;
use App\Http\Resources\Scheduling\ScheduleResource;
use App\Models\ExamSchedule;
use App\Models\Schedule;
use App\Services\Scheduling\ScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(private readonly ScheduleService $scheduleService)
    {
    }

    public function checkConflicts(CheckScheduleConflictsRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Kiểm tra xung đột thành công.',
            'data' => $this->scheduleService->checkConflicts($request->validated()),
        ]);
    }

    public function suggest(SuggestScheduleSlotRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Gợi ý khung giờ thành công.',
            'data' => $this->scheduleService->suggestSlot($request->validated()),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Schedule::query();

        foreach (['semester_id', 'lop_id', 'gv_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->integer($filter));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách thời khóa biểu thành công.',
            'data' => ScheduleResource::collection($query->paginate(20)),
        ]);
    }

    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $schedule = $this->scheduleService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tạo lịch học thành công.',
            'data' => new ScheduleResource($schedule),
        ], 201);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        $updated = $this->scheduleService->update($schedule, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lịch học thành công.',
            'data' => new ScheduleResource($updated),
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa lịch học thành công.',
            'data' => (object) [],
        ]);
    }

    public function publish(int $semesterId, Request $request): JsonResponse
    {
        $this->scheduleService->publish($semesterId, (int) $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã publish thời khóa biểu học kỳ.',
            'data' => (object) [],
        ]);
    }

    public function move(MoveScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        $updated = $this->scheduleService->moveSession($schedule, $request->validated(), (int) $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Dời buổi học thành công.',
            'data' => new ScheduleResource($updated),
        ]);
    }

    public function studentTimetable(int $id, Request $request): JsonResponse
    {
        $rows = Schedule::query()
            ->where('lop_id', function ($query) use ($id): void {
                $query->from('students')->where('id', $id)->select('lop_id');
            })
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->get();

        return response()->json(['success' => true, 'message' => 'Lấy lịch học sinh viên thành công.', 'data' => ScheduleResource::collection($rows)]);
    }

    public function teacherTimetable(int $id, Request $request): JsonResponse
    {
        $rows = Schedule::query()
            ->where('gv_id', $id)
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->get();

        return response()->json(['success' => true, 'message' => 'Lấy lịch dạy giảng viên thành công.', 'data' => ScheduleResource::collection($rows)]);
    }

    public function roomAvailability(RoomAvailabilityRequest $request): JsonResponse
    {
        $availableRooms = $this->scheduleService->roomAvailability(
            $request->integer('semester_id'),
            $request->integer('thu'),
            $request->integer('tiet'),
        );

        return response()->json(['success' => true, 'message' => 'Lấy danh sách phòng trống thành công.', 'data' => $availableRooms]);
    }

    public function examIndex(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Lấy lịch thi thành công.', 'data' => ExamSchedule::query()->paginate(20)]);
    }

    public function examStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'ngay_thi' => ['required', 'date'],
            'gio_bat_dau' => ['required', 'date_format:H:i'],
            'so_phut' => ['nullable', 'integer', 'between:30,240'],
            'gv_coi_thi_json' => ['required', 'array'],
            'ghi_chu' => ['nullable', 'string', 'max:200'],
        ]);

        $exam = ExamSchedule::create($validated);

        return response()->json(['success' => true, 'message' => 'Tạo lịch thi thành công.', 'data' => $exam], 201);
    }
}
