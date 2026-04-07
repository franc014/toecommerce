<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case SHIPPING = 'shipping';
    case SHIPPED = 'shipped';
    case CANCELED = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::SHIPPING => 'En camino',
            self::SHIPPED => 'Entregado',
            self::CANCELED => 'Cancelado',
        };
    }

    /**
     * Get valid transitions from current status
     *
     * @return array<OrderStatus>
     */
    public function getValidTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::SHIPPING, self::CANCELED],
            self::SHIPPING => [self::SHIPPED, self::CANCELED],
            self::SHIPPED => [],
            self::CANCELED => [],
        };
    }

    /**
     * Check if transition to target status is valid
     */
    public function canTransitionTo(OrderStatus $target): bool
    {
        return in_array($target, $this->getValidTransitions(), true);
    }
}
