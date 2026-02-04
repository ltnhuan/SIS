<?php

namespace App\Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Room;
use App\Modules\Core\Models\Term;
use App\Modules\Core\Models\TimeSlot;

class CatalogController extends Controller
{
    public function rooms()
    {
        return response()->json([
            'message' => 'Danh sách phòng học.',
            'data' => Room::all(),
        ]);
    }

    public function terms()
    {
        return response()->json([
            'message' => 'Danh sách học kỳ.',
            'data' => Term::all(),
        ]);
    }

    public function timeSlots()
    {
        return response()->json([
            'message' => 'Danh sách ca học.',
            'data' => TimeSlot::all(),
        ]);
    }
}
