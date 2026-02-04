<?php

namespace App\Modules\Curriculum\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumVersion;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Danh sách chương trình đào tạo.',
            'data' => Curriculum::all(),
        ]);
    }

    public function store(Request $request)
    {
        $curriculum = Curriculum::create($request->only(['tenant_id', 'program_id', 'name']));

        return response()->json([
            'message' => 'Tạo CTĐT thành công.',
            'data' => $curriculum,
        ]);
    }

    public function createVersion(Request $request, int $id)
    {
        $version = CurriculumVersion::create([
            'curriculum_id' => $id,
            'version_code' => $request->input('version_code', 'v1.0'),
            'effective_from' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Tạo phiên bản CTĐT thành công.',
            'data' => $version,
        ]);
    }
}
