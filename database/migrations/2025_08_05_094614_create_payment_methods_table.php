<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            
            // Basic information
            $table->string('name'); // e.g., "Bank Account", "Cash Wallet", "PayPal"
            $table->string('type')->index(); // e.g., bank_account, digital_wallet, card, cash
            $table->string('code')->unique(); // e.g., BANK_ACC_001, PAYPAL_ACC
            $table->text('description')->nullable();
            
            // Account/Provider details
            $table->string('provider')->nullable(); // Bank name, PayPal, Stripe, etc.
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable(); // International Bank Account Number
            $table->string('swift_bic')->nullable(); // SWIFT/BIC code
            $table->string('routing_number')->nullable(); // For US banks
            
            // For cards
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_type')->nullable(); // visa, mastercard, amex
            $table->date('card_expiry_date')->nullable();
            
            // For digital wallets
            $table->string('wallet_id')->nullable();
            $table->string('wallet_email')->nullable();
            
            // Fees & Limits
            $table->decimal('transaction_fee_percentage', 5, 2)->default(0);
            $table->decimal('transaction_fee_fixed', 10, 2)->default(0);
            $table->decimal('min_transaction_amount', 15, 2)->default(0);
            $table->decimal('max_transaction_amount', 15, 2)->nullable();
            $table->decimal('daily_limit', 15, 2)->nullable();
            $table->decimal('monthly_limit', 15, 2)->nullable();
            
            // Status & Configuration
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_online')->default(true); // Online vs offline payment method
            $table->boolean('requires_verification')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('verified_at')->nullable();
            
            // Security
            $table->string('token')->nullable(); // For API tokens (encrypted)
            $table->text('api_key')->nullable(); // For API keys (encrypted)
            $table->text('secret_key')->nullable(); // For secret keys (encrypted)
            $table->text('webhook_url')->nullable();
            
            // Integration settings
            $table->json('settings')->nullable(); // For provider-specific settings
            
            // Currency support
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->json('supported_currencies')->nullable(); // If supports multiple currencies
            
            // For cash handling
            $table->foreignId('cash_handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cash_location')->nullable(); // For physical cash storage
            
            // Audit & Ownership
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'type', 'is_active']);
            $table->index(['tenant_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};