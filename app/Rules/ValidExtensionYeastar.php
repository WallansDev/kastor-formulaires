<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidExtensionYeastar implements ValidationRule
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

        // Autoriser explicitement le format 03XXX (préfixe 0 pour 3XXX)
        if (strlen($extension) === 5 && preg_match('/^03\d{3}$/', $extension)) {
            // OK: 03XXX accepté
            // Vérification des doublons sera faite après
        }
        // Autorisation du format 3 chiffres
        elseif (strlen($extension) === 3) {
            $num = intval($extension);
            
            // Refus de la plage 100 à 199 (réservée)
            if ($num >= 100 && $num <= 199) {
                $fail(":input est interdit (plage 100 à 199 réservée).");
                return;
            }
            
            // Autorisation de tous les autres formats 3 chiffres (0-99 et 200-999)
            // OK: format 3 chiffres accepté
        }
        // Règles spécifiques pour 4 chiffres
        elseif (strlen($extension) === 4) {
            $num = intval($extension);

            // Refus 3000–3999 avec recommandation d'utiliser 03XXX
            if ($num >= 3000 && $num <= 3999) {
                $fail(":input est interdit (plage 3000 à 3999 réservée). Pour cette plage, utilisez le format 03XXX.");
                return;
            }

            // Autorisations: 1001–2999 et 4000–5999
            $inAllowedRange = ($num >= 1001 && $num <= 2999) || ($num >= 4000 && $num <= 5999);
            if (!$inAllowedRange) {
                $fail(":input n'est pas autorisé. Plages autorisées: 1001–2999 et 4000–5999 (ou 03XXX pour la plage 3XXX).");
                return;
            }
        }
        // Format invalide si ce n'est ni 3, ni 4, ni 5 chiffres (03XXX)
        else {
            $fail(":input a un format invalide. Utilisez 3 chiffres, 4 chiffres (1001–2999 ou 4000–5999) ou 03XXX pour 3XXX.");
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
