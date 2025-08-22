<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'customer_name',
        'location_id',
        'order_number',
        'type',
        'status',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'paid_amount',
        'balance_due',
        'source',
        'notes',
        'created_by',
    ];

    // Relationships
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function orderCreater() { return $this->belongsTo(User::class, 'created_by'); }
}
