<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    /** @use HasFactory<\Database\Factories\OrderPaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'card_last_four',
        'card_brand',
        'notes',
        'processed_at',
        'processed_by',
    ];

     // Accessor to format amount automatically
    public function getAmountAttribute($value)
    {
        return formatCurrency($value);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
