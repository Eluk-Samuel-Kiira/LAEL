<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'barcode',
        'price',
        'cost_price',
        'weight',
        'weight_unit',
        'image_url',
        'is_active',
        'created_by',
        'tenant_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    
    public function unitMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'weight_unit', 'id');
    }

    public function variantCreater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    public function inventory()
    {   
        return $this->hasOne(InventoryItems::class, 'variant_id', 'id');
    }


}
