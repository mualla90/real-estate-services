<?php

namespace App\Support;

use App\Models\DynamicField;
use Illuminate\Validation\Validator;

class DynamicFieldInputValidator
{
    public static function validate(
        Validator $validator,
        mixed $categoryId,
        mixed $subcategoryId,
        array $inputFields
    ): void {
        if ($validator->errors()->isNotEmpty() || empty($categoryId)) {
            return;
        }

        $fields = self::fieldsForContext($categoryId, $subcategoryId);
        $input = collect($inputFields);
        $inputByFieldId = $input->keyBy(fn ($item) => (int) ($item['dynamic_field_id'] ?? 0));

        foreach ($fields->where('is_required', true) as $field) {
            $item = $inputByFieldId->get($field->id);

            if (! $item || self::isBlank($item['value'] ?? null)) {
                $validator->errors()->add(
                    'dynamic_fields',
                    __('validation.required', ['attribute' => $field->getTranslation('name', app()->getLocale())])
                );
            }
        }

        foreach ($input as $index => $item) {
            $fieldId = (int) ($item['dynamic_field_id'] ?? 0);
            $field = $fields->firstWhere('id', $fieldId);

            if (! $field || self::isBlank($item['value'] ?? null)) {
                continue;
            }

            $value = $item['value'];
            $attribute = "dynamic_fields.$index.value";

            match ($field->field_type) {
                'number' => self::validateNumber($validator, $attribute, $value),
                'select' => self::validateSelect($validator, $attribute, $field, $value),
                'boolean' => self::validateBoolean($validator, $attribute, $value),
                'date' => self::validateDate($validator, $attribute, $value),
                default => self::validateText($validator, $attribute, $value),
            };
        }
    }

    protected static function fieldsForContext(mixed $categoryId, mixed $subcategoryId)
    {
        return DynamicField::query()
            ->where('status', 'active')
            ->where(function ($query) use ($categoryId, $subcategoryId) {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->whereNull('subcategory_id');
                });

                if ($subcategoryId) {
                    $query->orWhere(function ($q) use ($subcategoryId) {
                        $q->whereNull('category_id')
                            ->where('subcategory_id', $subcategoryId);
                    });

                    $query->orWhere(function ($q) use ($categoryId, $subcategoryId) {
                        $q->where('category_id', $categoryId)
                            ->where('subcategory_id', $subcategoryId);
                    });
                }
            })
            ->get();
    }

    protected static function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    protected static function validateText(Validator $validator, string $attribute, mixed $value): void
    {
        if (! is_scalar($value)) {
            $validator->errors()->add($attribute, __('validation.string', ['attribute' => $attribute]));
        }
    }

    protected static function validateNumber(Validator $validator, string $attribute, mixed $value): void
    {
        if (! is_numeric($value)) {
            $validator->errors()->add($attribute, __('validation.numeric', ['attribute' => $attribute]));
        }
    }

    protected static function validateSelect(
        Validator $validator,
        string $attribute,
        DynamicField $field,
        mixed $value
    ): void {
        if (! is_scalar($value) || ! in_array((string) $value, $field->options ?? [], true)) {
            $validator->errors()->add($attribute, __('validation.in', ['attribute' => $attribute]));
        }
    }

    protected static function validateBoolean(Validator $validator, string $attribute, mixed $value): void
    {
        $allowed = [true, false, 0, 1, '0', '1', 'true', 'false'];

        if (! in_array($value, $allowed, true)) {
            $validator->errors()->add($attribute, __('validation.boolean', ['attribute' => $attribute]));
        }
    }

    protected static function validateDate(Validator $validator, string $attribute, mixed $value): void
    {
        if (! is_scalar($value) || strtotime((string) $value) === false) {
            $validator->errors()->add($attribute, __('validation.date', ['attribute' => $attribute]));
        }
    }
}
