 
<div class="modal fade" id="kt_modal_add_product" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_department">
                <h2 class="fw-bold"> {{__('pagination.variant_create')}} {{ $product_variants->name }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="addVariantsForm" method="POST" action="{{ route('variants.store')}}">
                    @csrf
                    <!-- <input type="hidden" name="{{ $product_variants->id }}" class="form-control" required> -->

                    <table class="table table-bordered" id="variantsTable">
                        <thead>
                            <tr>
                                <th>{{ __('pagination._variant_name') }}</th>
                                <th>{{ __('pagination._sku') }}</th>
                                <th>{{ __('pagination._barcode') }}</th>
                                <th>{{ __('pagination._price') }}</th>
                                <th>{{ __('pagination.cost_price') }}</th>
                                <th>{{ __('pagination._weight') }}</th>
                                <th>{{ __('pagination.weight_unit') }}</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="variants[0][name]" class="form-control" required></td>
                                <td><input type="text" name="variants[0][sku]" class="form-control"></td>
                                <td><input type="text" name="variants[0][barcode]" class="form-control"></td>
                                <td><input type="number" name="variants[0][price]" class="form-control" step="0.01" required></td>
                                <td><input type="number" name="variants[0][cost_price]" class="form-control" step="0.01" required></td>
                                <td><input type="number" name="variants[0][weight]" class="form-control" min="0" required></td>
                                <td>
                                    <select name="variants[0]weight_unit" class="form-select" data-control="select2" data-close-on-select="false" data-placeholder="{{__('auth._select')}}" data-allow-clear="true">
                                        <option></option>
                                        @foreach ($uoms as $umo)
                                            <option value="{{ $umo->id }}">{{ $umo->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><button type="button" class="btn btn-sm btn-danger removeVariantBtn" disabled>&times;</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addVariantBtn" class="btn btn-primary mt-3">
                        + {{ __('pagination._add_variant') }}
                    </button>

                    <button type="submit" class="btn btn-success mt-3">
                        {{ __('auth.submit') }}
                    </button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const addVariantBtn = document.getElementById('addVariantBtn');
                        const variantsTableBody = document.querySelector('#variantsTable tbody');

                        addVariantBtn.addEventListener('click', () => {
                            const rowCount = variantsTableBody.rows.length;
                            const newRow = document.createElement('tr');

                            newRow.innerHTML = `
                                <td><input type="text" name="variants[${rowCount}][name]" class="form-control" required></td>
                                <td><input type="text" name="variants[${rowCount}][sku]" class="form-control"></td>
                                <td><input type="text" name="variants[${rowCount}][barcode]" class="form-control"></td>
                                <td><input type="number" name="variants[${rowCount}][price]" class="form-control" step="0.01" required></td>
                                <td><input type="number" name="variants[${rowCount}][cost_price]" class="form-control" step="0.01" required></td>
                                <td><input type="number" name="variants[${rowCount}][weight]" class="form-control" min="0" required></td>
                                <td>
                                    <select name="variants[${rowCount}]weight_unit" class="form-select" data-control="select2" data-close-on-select="false" data-placeholder="{{__('auth._select')}}" data-allow-clear="true">
                                        <option></option>
                                        @foreach ($uoms as $umo)
                                            <option value="{{ $umo->id }}">{{ $umo->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><button type="button" class="btn btn-sm btn-danger removeVariantBtn">&times;</button></td>
                            `;

                            variantsTableBody.appendChild(newRow);
                            updateRemoveButtons();
                        });

                        variantsTableBody.addEventListener('click', function (e) {
                            if (e.target.classList.contains('removeVariantBtn')) {
                                const row = e.target.closest('tr');
                                row.remove();
                                updateRemoveButtons();
                                updateInputNames();
                            }
                        });

                        function updateRemoveButtons() {
                            const removeBtns = variantsTableBody.querySelectorAll('.removeVariantBtn');
                            removeBtns.forEach(btn => btn.disabled = variantsTableBody.rows.length === 1);
                        }

                        function updateInputNames() {
                            Array.from(variantsTableBody.rows).forEach((row, index) => {
                                row.querySelectorAll('input').forEach(input => {
                                    const newName = input.name.replace(/\d+/, index);
                                    input.name = newName;
                                });
                            });
                        }

                        updateRemoveButtons();
                    });
                </script>

            </div>
        </div>
    </div>
</div>  



