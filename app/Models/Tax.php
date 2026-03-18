<?php

namespace App\Models;

use Database\Factories\TaxFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<TaxFactory> */
    use HasFactory;
}
