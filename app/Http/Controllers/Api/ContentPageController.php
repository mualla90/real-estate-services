<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ContentPageController extends Controller
{
    public function privacyPolicy(): JsonResponse
    {
        return $this->page('privacy_policy');
    }

    public function termsOfUse(): JsonResponse
    {
        return $this->page('terms_of_use');
    }

    protected function page(string $key): JsonResponse
    {
        return response()->json([
            'message' => __('api.content.fetched'),
            'data' => [
                'key' => $key,
                'title' => __("api.content.$key.title"),
                'body' => __("api.content.$key.body"),
            ],
        ]);
    }
}
