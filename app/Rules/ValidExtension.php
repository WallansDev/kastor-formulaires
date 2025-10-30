<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidExtension implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, string): void  $fail
     */

    protected array $allExtensions;

    public function __construct(array $allExtensions)
    {
        $this->allExtensions = $allExtensions;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $extension = (string) $value;

        // Interdiction 2 chiffres : urgences
        if (strlen($extension) === 2 && in_array($extension, ['15', '17', '18'])) {
            $fail(":input est interdit (numéro d'urgence).");
            return;
        }

        // Interdiction 3 chiffres : 100–199
        if (strlen($extension) === 3 && intval($extension) >= 100 && intval($extension) <= 199) {
            $fail(":input est interdit (plage 100 à 199 réservée).");
            return;
        }

        // Interdiction 4 chiffres : 3000–3999
        if (strlen($extension) === 4 && intval($extension) >= 3000 && intval($extension) <= 3999) {
            $fail(":input est interdit (plage 3000 à 3999 réservée).");
            return;
        }

        // Interdictions spécifiques
        if (in_array($extension, ['116000', '2222'])) {
            $fail(":input est réservé et ne peut pas être utilisé.");
            return;
        }

        // Vérification des doublons
        $count = 0;
        foreach ($this->allExtensions as $ext) {
            if ((string) $ext['extension'] === $extension) {
                $count++;
            }
        }

        if ($count > 1) {
            $fail("Le numéro d'extension '$extension'en doublon.");
        }
    }
}
