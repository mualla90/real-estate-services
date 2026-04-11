<?php

namespace App\Services\DynamicField;

use App\Models\DynamicField;

class DynamicFieldService
{
    public function create(array $data): DynamicField
    {
        return DynamicField::create($data);
    }

    public function update(DynamicField $dynamicField, array $data): DynamicField
    {
        $dynamicField->update($data);

        return $dynamicField->fresh();
    }

    public function delete(DynamicField $dynamicField): void
    {
        $dynamicField->delete();
    }
}

