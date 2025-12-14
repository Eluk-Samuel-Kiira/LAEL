<!-- resources/views/procurement/employee-payment/edit.blade.php -->
@can('edit employee payment')
<div class="modal fade" id="editPaymentModal{{$payment->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('payments.edit_payment') }} - {{ $payment->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPaymentForm{{$payment->id}}" method="POST" action="{{ route('payment.update', $payment->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Employee -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.employee') }} *</label>
                            <select name="employee_id" class="form-select" required 
                                    @if($payment->status === 'completed') disabled @endif>
                                <option value="">{{ __('payments.select_employee') }}</option>
                                @foreach($active_employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ $payment->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($payment->status === 'completed')
                                <input type="hidden" name="employee_id" value="{{ $payment->employee_id }}">
                            @endif
                        </div>

                        <!-- Payment Date -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.payment_date') }} *</label>
                            <input type="date" name="payment_date" class="form-control" required 
                                   value="{{ $payment->payment_date->format('Y-m-d') }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>

                        <!-- Payment Type -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.payment_type') }} *</label>
                            <select name="payment_type" class="form-select" required 
                                    @if($payment->status === 'completed') disabled @endif>
                                <option value="">{{ __('payments.select_payment_type') }}</option>
                                <option value="salary" {{ $payment->payment_type == 'salary' ? 'selected' : '' }}>
                                    {{ __('payments.salary') }}
                                </option>
                                <option value="allowance" {{ $payment->payment_type == 'allowance' ? 'selected' : '' }}>
                                    {{ __('payments.allowance') }}
                                </option>
                                <option value="bonus" {{ $payment->payment_type == 'bonus' ? 'selected' : '' }}>
                                    {{ __('payments.bonus') }}
                                </option>
                                <option value="overtime" {{ $payment->payment_type == 'overtime' ? 'selected' : '' }}>
                                    {{ __('payments.overtime') }}
                                </option>
                                <option value="advance" {{ $payment->payment_type == 'advance' ? 'selected' : '' }}>
                                    {{ __('payments.advance') }}
                                </option>
                                <option value="other" {{ $payment->payment_type == 'other' ? 'selected' : '' }}>
                                    {{ __('payments.other') }}
                                </option>
                            </select>
                            @if($payment->status === 'completed')
                                <input type="hidden" name="payment_type" value="{{ $payment->payment_type }}">
                            @endif
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.payment_method') }} *</label>
                            <select name="payment_method" class="form-select" required 
                                    @if($payment->status === 'completed') disabled @endif>
                                <option value="">{{ __('payments.select_payment_method') }}</option>
                                <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>
                                    {{ __('payments.cash') }}
                                </option>
                                <option value="bank_transfer" {{ $payment->payment_method == 'bank_transfer' ? 'selected' : '' }}>
                                    {{ __('payments.bank_transfer') }}
                                </option>
                                <option value="check" {{ $payment->payment_method == 'check' ? 'selected' : '' }}>
                                    {{ __('payments.check') }}
                                </option>
                                <option value="digital_wallet" {{ $payment->payment_method == 'digital_wallet' ? 'selected' : '' }}>
                                    {{ __('payments.digital_wallet') }}
                                </option>
                            </select>
                            @if($payment->status === 'completed')
                                <input type="hidden" name="payment_method" value="{{ $payment->payment_method }}">
                            @endif
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.amount') }} *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required
                                   value="{{ $payment->amount }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.status') }} *</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>
                                    {{ __('payments.pending') }}
                                </option>
                                <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>
                                    {{ __('payments.completed') }}
                                </option>
                                <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>
                                    {{ __('payments.failed') }}
                                </option>
                                <option value="cancelled" {{ $payment->status == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('payments.cancelled') }}
                                </option>
                            </select>
                        </div>

                        <!-- Hours Worked & Hourly Rate (for overtime) -->
                        @php
                            $isOvertime = $payment->payment_type === 'overtime';
                        @endphp
                        <div class="col-md-6 overtime-fields" style="display: {{ $isOvertime ? 'block' : 'none' }};">
                            <label class="form-label">{{ __('payments.hours_worked') }}</label>
                            <input type="number" name="hours_worked" class="form-control" step="0.01" min="0"
                                   value="{{ $payment->hours_worked ?? 0 }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>
                        <div class="col-md-6 overtime-fields" style="display: {{ $isOvertime ? 'block' : 'none' }};">
                            <label class="form-label">{{ __('payments.hourly_rate') }}</label>
                            <input type="number" name="hourly_rate" class="form-control" step="0.01" min="0"
                                   value="{{ $payment->hourly_rate ?? 0 }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">{{ __('payments.description') }} *</label>
                            <textarea name="description" class="form-control" rows="2" required
                                      @if($payment->status === 'completed') readonly @endif>{{ $payment->description }}</textarea>
                        </div>

                        <!-- Pay Period -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.pay_period_start') }}</label>
                            <input type="date" name="pay_period_start" class="form-control"
                                   value="{{ $payment->pay_period_start ? $payment->pay_period_start->format('Y-m-d') : '' }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.pay_period_end') }}</label>
                            <input type="date" name="pay_period_end" class="form-control"
                                   value="{{ $payment->pay_period_end ? $payment->pay_period_end->format('Y-m-d') : '' }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>

                        <!-- Reference Number -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('payments.reference_number') }}</label>
                            <input type="text" name="reference_number" class="form-control"
                                   value="{{ $payment->reference_number }}"
                                   @if($payment->status === 'completed') readonly @endif>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label">{{ __('payments.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      @if($payment->status === 'completed') readonly @endif>{{ $payment->notes }}</textarea>
                        </div>

                        @if($payment->status === 'completed')
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    {{ __('payments.cannot_edit_completed') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('auth._cancel') }}</button>
                    <button type="submit" class="btn btn-primary" 
                            @if($payment->status === 'completed') disabled @endif>
                        {{ __('payments.update_payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endcan