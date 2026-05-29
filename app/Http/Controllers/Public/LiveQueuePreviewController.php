<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Antrean\LiveQueuePreviewService;
use Illuminate\Http\JsonResponse;

class LiveQueuePreviewController extends Controller
{
    public function __invoke(LiveQueuePreviewService $preview): JsonResponse
    {
        return response()->json($preview->payload());
    }
}
