<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'location_id',
        'po_number',
        'status',
        'expected_delivery_date',
        'subtotal',
        'tax_total',
        'total',
        'notes',
        'created_by',
        
        // Status tracking fields
        'submitted_at',
        'submitted_by',
        'approved_at', 
        'approved_by',
        'sent_at',
        'sent_by',
        'cancelled_at',
        'cancelled_by',
        'received_at',
        'received_by',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = 'draft';
            }
        });
    }

    // 👇 Accessors for monetary fields
    public function getSubtotalAttribute($value)
    {
        return formatCurrency($value);
    }

    public function getTaxTotalAttribute($value)
    {
        return formatCurrency($value);
    }

    public function getTotalAttribute($value)
    {
        return formatCurrency($value);
    }

    // 👇 Mutators for monetary fields
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = toUSD($value);
    }

    public function setTaxTotalAttribute($value)
    {
        $this->attributes['tax_total'] = toUSD($value); 
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = toUSD($value);
    }




    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    

    // 👇 Accessors for timestamp fields
    public function getSubmittedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getApprovedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getSentAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getReceivedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getCancelledAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
}