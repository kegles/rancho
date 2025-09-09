<?php

namespace App\Services;

class PricingService
{
    // preços base em centavos
    private int $FULL_ADULT = 5000; // R$ 50 (R/C/A)
    private int $DAY_ADULT  = 3000; // R$ 30 (um dia)

    /** Criança? Agora é só pela categoria CH */
    public function isChild(string $category): bool
    {
        return $category === 'CH';
    }

    public function badge(string $category): string
    {
        // defina como quer rotular na etiqueta do crachá:
        return match ($category) {
            'R' => 'R',       // Radioamador(a)
            'V' => 'V',       // Visitante
            'E' => 'G',       // Convidado(a) especial (Guest) – escolha a letra que preferir
            default => 'X',
        };
    }

    /**
     * Preço base da inscrição:
     * - Isentos (ORG) = 0
     * - DAY: adulto R$30; criança (CH) meia = R$15
     * - FULL: adulto R$50; criança (CH) meia = R$25
     */
    public function basePrice(string $ticketType, string $category, bool $isExempt): int
    {
        if ($isExempt || $category === 'E') { // convidado especial não paga
            return 0;
        }
        $child = $this->isChild($category);
        if ($ticketType === 'DAY') {
            return $child ? intdiv($this->DAY_ADULT, 2) : $this->DAY_ADULT;
        }
        // Adultos que pagam inscrição: Radioamador (R) e Visitante (V)
        if (in_array($category, ['R','V'], true)) {
            return $this->FULL_ADULT;
        }
        // Criança (CH)
        if ($category === 'CH') {
            return intdiv($this->FULL_ADULT, 2);
        }
        return 0;
    }



    /**
     * Sorteio: apenas R/C/A pagantes (não isentos, não crianças).
     */
    public function eligibleForDraw(string $category, bool $isExempt, int $basePrice): bool
    {
        // Participam: V e R, não isentos, com inscrição paga (>0)
        return !$isExempt
            && in_array($category, ['V','R'], true)
            && $basePrice > 0;
    }

    /**
     * Preço unitário de item extra (refeições com meia para CH)
     */
    public function adjustUnitForChild(int $unitPrice, string $category, bool $productHasChildHalf): int
    {
        return ($this->isChild($category) && $productHasChildHalf)
            ? intdiv($unitPrice, 2)
            : $unitPrice;
    }
}
