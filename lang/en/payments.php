<?php

return [
    // Titles
    'employee_payment_index' => 'Employee Payments',
    'employee_payment_table' => 'Employee Payments',
    'add_new_payment' => 'Add New Payment',
    'edit_payment' => 'Edit Payment',
    
    // Table headers
    'payment_id' => 'ID',
    'employee' => 'Employee',
    'payment_type' => 'Type',
    'description' => 'Description',
    'amount' => 'Amount',
    'payment_date' => 'Date',
    'payment_status' => 'Status',
    'payment_method' => 'Method',
    
    // Statuses
    'pending' => 'Pending',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'cancelled' => 'Cancelled',
    
    // Payment Types
    'salary' => 'Salary',
    'allowance' => 'Allowance',
    'bonus' => 'Bonus',
    'overtime' => 'Overtime',
    'advance' => 'Advance',
    'other' => 'Other',
    
    // Payment Methods
    'cash' => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'check' => 'Check',
    'digital_wallet' => 'Digital Wallet',
    
    // Form labels
    'select_employee' => 'Select Employee',
    'select_payment_type' => 'Select Payment Type',
    'select_payment_method' => 'Select Payment Method',
    'reference_number' => 'Reference Number',
    'pay_period_start' => 'Period Start',
    'pay_period_end' => 'Period End',
    'hours_worked' => 'Hours Worked',
    'hourly_rate' => 'Hourly Rate',
    'notes' => 'Notes',
    'status' => 'Status',
    
    // Buttons
    'payment_new' => 'New Payment',
    'create_payment' => 'Create Payment',
    'update_payment' => 'Update Payment',
    
    // Messages
    'no_payments_found' => 'No payments found',
    'confirm_complete_payment' => 'Are you sure you want to mark this payment as completed?',
    'confirm_cancel_payment' => 'Are you sure you want to cancel this payment?',
    'status_update_error' => 'Error updating status',
    'cannot_edit_completed' => 'Cannot edit completed payments. Only status can be changed.',
    'cannot_delete_completed_warning' => 'Completed payments cannot be deleted.',
    
    // Validation messages
    'employee_not_found' => 'Employee not found',
    'payment_not_found' => 'Payment not found',
    'cannot_delete_completed_payment' => 'Cannot delete completed payment',
    'cannot_revert_completed_payment' => 'Cannot revert completed payment to pending',
];