<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        $sliders = Slider::query()
            ->active()
            ->withinSchedule()
            ->ordered()
            ->with('media')
            ->get();

        return response()->json([
            'message' => __('api.sliders.fetched'),
            'data' => SliderResource::collection($sliders),
        ]);
    }
}

