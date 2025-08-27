<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
        'max_uses',
        'max_uses_per_customer',
        'min_order_amount',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products()
    {
        return $this->hasMany(PromotionProduct::class, 'promotion_id');
    }
}
