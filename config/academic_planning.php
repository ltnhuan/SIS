<?php

return [
    'max_versions_per_plan' => env('ACADEMIC_PLAN_MAX_VERSIONS', 3),
    'max_credits_per_semester' => env('ACADEMIC_PLAN_MAX_CREDITS_PER_SEMESTER', 24),
    'notification_channels' => ['database'],
];
