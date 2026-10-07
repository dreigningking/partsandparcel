<?php

namespace App\Rules;

use App\Models\Setting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProhibitedWordsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || ! is_string($value)) {
            return;
        }

        $rawSetting = Setting::getValue('prohibited_words', 'free,win');
        if (empty($rawSetting)) {
            return;
        }

        $words = is_array($rawSetting)
            ? $rawSetting
            : array_filter(array_map('trim', explode(',', (string) $rawSetting)));

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            // Case-insensitive word boundary match
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            if (preg_match($pattern, $value)) {
                $fail("The :attribute contains prohibited word: '{$word}'.");
                return;
            }
        }
    }
}
