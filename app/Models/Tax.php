<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'rate',
        'type',
        'is_active',
        'created_by',
    ];

    public function taxCreater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
