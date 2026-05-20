<?php

namespace App\Rules;

use App\Models\Subcategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SubcategoryBelongsToCategory implements ValidationRule
{
    public function __construct(
        protected mixed $categoryId
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->categoryId)) {
            return;
        }

        $exists = Subcategory::query()
            ->where('id', $value)
            ->where('category_id', $this->categoryId)
            ->exists();

        if (! $exists) {
            $fail('The selected subcategory does not belong to the selected category.');
        }
    }
}
