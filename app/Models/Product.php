<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'category_id',
        'sku',
        'name',
        'description',
        'type',
        'is_taxable',
        'is_active',
        'slug',
        'created_by',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function productCreater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
