<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\CurrencyFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'created_by',
        'status',
        'isBaseCurrency',
    ];

    public function currencyCreater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
