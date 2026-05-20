<?php

namespace App\Services\Slider;

use App\Models\Slider;
use Illuminate\Http\UploadedFile;

class SliderService
{
    public function create(array $data, ?UploadedFile $image = null): Slider
    {
        $slider = Slider::create($data);

        if ($image) {
            $slider->addMedia($image)->toMediaCollection('image');
        }

        return $slider;
    }

    public function update(Slider $slider, array $data, ?UploadedFile $image = null): Slider
    {
        $slider->update($data);

        if ($image) {
            $slider->addMedia($image)->toMediaCollection('image');
        }

        return $slider->fresh();
    }

    public function delete(Slider $slider): void
    {
        $slider->delete();
    }
}

