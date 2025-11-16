<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_variant_id',
        'product_name',
        'sku',
        'quantity',
        'unit_cost',
        'tax_amount',
        'total_cost',
        'received_quantity',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity' => 'integer',
        'received_quantity' => 'integer',
    ];

    // 👇 Accessors for monetary fields
    public function getUnitCostAttribute($value)
    {
        return formatCurrency($value);
    }

    public function getTaxAmountAttribute($value)
    {
        return formatCurrency($value);
    }

    public function getTotalCostAttribute($value)
    {
        return formatCurrency($value);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function receiptItems(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class, 'purchase_order_item_id');
    }
}