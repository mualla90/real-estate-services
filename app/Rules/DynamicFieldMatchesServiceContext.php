<?php

namespace App\Rules;

use App\Models\DynamicField;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DynamicFieldMatchesServiceContext implements ValidationRule
{
    public function __construct(
        protected mixed $categoryId,
        protected mixed $subcategoryId
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->categoryId)) {
            return;
        }

        $field = DynamicField::query()
            ->where('id', $value)
            ->where('status', 'active')
            ->first();

        if (! $field) {
            $fail('The selected dynamic field is invalid or inactive.');
            return;
        }

        $matchesCategoryOnly = $field->category_id == $this->categoryId && is_null($field->subcategory_id);
        $matchesSubcategoryOnly = ! empty($this->subcategoryId)
            && is_null($field->category_id)
            && $field->subcategory_id == $this->subcategoryId;
        $matchesBoth = ! empty($this->subcategoryId)
            && $field->category_id == $this->categoryId
            && $field->subcategory_id == $this->subcategoryId;

        if (! ($matchesCategoryOnly || $matchesSubcategoryOnly || $matchesBoth)) {
            $fail('The selected dynamic field does not match the service category/subcategory.');
        }
    }
}

