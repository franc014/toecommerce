<?php

namespace App\Models;

use App\Enums\DiscountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => DiscountStatus::class,
        ];
    }

    public function scopeValid($query)
    {
        return $query->where('status', DiscountStatus::ACTIVE->value)
            ->orWhere('status', DiscountStatus::SCHEDULED->value);
    }

    public static function setStatus()
    {
        $now = now();

        foreach (Discount::all() as $discount) {
            if ($discount->start_date <= $now && $discount->end_date >= $now) {
                $discount->status = DiscountStatus::ACTIVE->value;
            } elseif ($discount->start_date > $now) {
                $discount->status = DiscountStatus::SCHEDULED->value;
            } else {
                $discount->status = DiscountStatus::INACTIVE->value;
            }
            $discount->save();
        }

    }

    public function changeStatus(DiscountStatus $status)
    {
        $this->status = $status->value;
        $this->save();
    }

    public static function getByStatus(DiscountStatus $status)
    {
        return Discount::where('status', $status->value)->get();
    }
}
