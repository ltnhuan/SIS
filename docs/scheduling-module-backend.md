# Scheduling Module Backend (TKB)

## Phạm vi đã triển khai
- Migrations: `schedules`, `exam_schedules`.
- Service: `ScheduleService` (check conflict, suggest slot, publish, move, room availability).
- API Controller: `ScheduleController`.
- Requests/Resources: chuẩn validate + JSON output.
- Event: `TimetablePublished`.

## 4 loại conflict
1. `GV_CONFLICT`
2. `ROOM_CONFLICT`
3. `CLASS_CONFLICT`
4. `TEACHER_OVERLOAD` (warning)

## API chính
- `POST /api/v1/dt/schedules/check-conflicts`
- `POST /api/v1/dt/schedules/suggest`
- `GET|POST|PUT|DELETE /api/v1/dt/schedules`
- `POST /api/v1/dt/schedules/publish/{semesterId}`
- `PUT /api/v1/dt/schedules/{schedule}/move`
- `GET /api/v1/sv/{id}/timetable`
- `GET /api/v1/gv/{id}/timetable`
- `GET /api/v1/dt/rooms/availability`
- `GET|POST /api/v1/dt/exam-schedules`
