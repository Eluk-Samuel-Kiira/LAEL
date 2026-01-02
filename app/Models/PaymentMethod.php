<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'code',
        'description',
        'provider',
        'account_name',
        'account_number',
        'iban',
        'swift_bic',
        'routing_number',
        'card_last_four',
        'card_type',
        'card_expiry_date',
        'wallet_id',
        'wallet_email',
        'transaction_fee_percentage',
        'transaction_fee_fixed',
        'min_transaction_amount',
        'max_transaction_amount',
        'daily_limit',
        'monthly_limit',
        'is_active',
        'is_default',
        'is_online',
        'requires_verification',
        'is_verified',
        'verified_at',
        'token',
        'api_key',
        'secret_key',
        'webhook_url',
        'settings',
        'currency_id',
        'supported_currencies',
        'cash_handler_id',
        'cash_location',
        'created_by',
        'tenant_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_online' => 'boolean',
        'requires_verification' => 'boolean',
        'is_verified' => 'boolean',
        'transaction_fee_percentage' => 'decimal:2',
        'transaction_fee_fixed' => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
        'max_transaction_amount' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'monthly_limit' => 'decimal:2',
        'verified_at' => 'date',
        'card_expiry_date' => 'date',
        'settings' => 'array',
        'supported_currencies' => 'array',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function cashHandler()
    {
        return $this->belongsTo(User::class, 'cash_handler_id');
    }

    public function payments()
    {
        return $this->hasMany(EmployeePayment::class, 'payment_method_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function getFormattedAccountNumberAttribute()
    {
        if (!$this->account_number) {
            return null;
        }

        // Show only last 4 digits for security
        return '****' . substr($this->account_number, -4);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->type === 'bank_account') {
            return $this->provider . ' - ' . $this->account_name . ' (' . $this->getFormattedAccountNumberAttribute() . ')';
        } elseif ($this->type === 'card') {
            return ucfirst($this->card_type) . ' ending in ' . $this->card_last_four;
        } elseif ($this->type === 'digital_wallet') {
            return $this->provider . ' - ' . $this->wallet_email;
        }

        return $this->name;
    }

    public function calculateFee($amount)
    {
        $percentageFee = ($amount * $this->transaction_fee_percentage) / 100;
        return $percentageFee + $this->transaction_fee_fixed;
    }

    public function isWithinLimits($amount)
    {
        if ($this->min_transaction_amount && $amount < $this->min_transaction_amount) {
            return false;
        }

        if ($this->max_transaction_amount && $amount > $this->max_transaction_amount) {
            return false;
        }

        return true;
    }

    public static function getDefaultForCurrentTenant(): ?PaymentMethod
    {
        $tenantId = current_tenant_id() ?? Auth::user()->tenant_id ?? null;
        
        if (!$tenantId) {
            return null;
        }
        
        return self::getDefaultForTenant($tenantId);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
}