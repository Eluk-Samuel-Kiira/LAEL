<?php
// app/Models/EmployeePayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tenant_id',
        'payment_date',
        'payment_type',
        'description',
        'amount',
        'payment_method',
        'reference_number',
        'status',
        'pay_period_start',
        'pay_period_end',
        'hours_worked',
        'hourly_rate',
        'breakdown',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'amount' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'breakdown' => 'array',
    ];

    /**
     * Payment type constants.
     */
    const TYPE_SALARY = 'salary';
    const TYPE_ALLOWANCE = 'allowance';
    const TYPE_BONUS = 'bonus';
    const TYPE_OVERTIME = 'overtime';
    const TYPE_ADVANCE = 'advance';
    const TYPE_OTHER = 'other';

    /**
     * Payment method constants.
     */
    const METHOD_CASH = 'cash';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CHECK = 'check';
    const METHOD_DIGITAL_WALLET = 'digital_wallet';

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the employee that owns the payment.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the tenant that owns the payment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // 👇 Accessors for monetary fields
    public function getAmountAttribute($value)
    {
        return formatCurrency($value);
    }

    public function getHourlyRateAttribute($value)
    {
        return formatCurrency($value);
    }

    // 👇 Mutators for monetary fields - Convert to USD when WRITING to database
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = toUSD($value);
    }

    public function setHourlyRateAttribute($value)
    {
        $this->attributes['hourly_rate'] = toUSD($value);
    }

    
    /**
     * Scope completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope payments by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get payment type options.
     */
    public static function getPaymentTypes(): array
    {
        return [
            self::TYPE_SALARY,
            self::TYPE_ALLOWANCE,
            self::TYPE_BONUS,
            self::TYPE_OVERTIME,
            self::TYPE_ADVANCE,
            self::TYPE_OTHER,
        ];
    }

    /**
     * Get payment method options.
     */
    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_CASH,
            self::METHOD_BANK_TRANSFER,
            self::METHOD_CHECK,
            self::METHOD_DIGITAL_WALLET,
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Scope a query to only include expenses for a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}