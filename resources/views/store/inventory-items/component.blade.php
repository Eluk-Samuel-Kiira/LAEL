<div class="card-body py-4" id="reloadProductComponent">
    <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div>
                    </th>
                    <th class="min-w-125px">{{__('pagination._sku')}}</th>
                    <th class="min-w-125px">{{__('auth._name')}}</th> 
                    <th class="min-w-125px">{{__('auth._department')}}</th>  
                    <th class="min-w-125px">{{__('pagination.quantity_on_hand')}}</th>
                    <th class="min-w-125px">{{__('pagination.quantity_allocated')}}</th> 
                    <th class="min-w-125px">{{__('pagination.quantity_on_order')}}</th>
                    <th class="min-w-125px">{{__('pagination.reorder_point')}}</th> 
                    <th class="min-w-125px">{{__('pagination.preferred_stock_level')}}</th>
                    <th class="min-w-125px">{{__('pagination.batch_number')}}</th> 
                    <th class="min-w-125px">{{__('pagination.expiry_date')}}</th> 
                    <th class="min-w-125px">{{__('auth._creater')}}</th> 
                    <th class="min-w-125px">{{__('auth.created_at')}}</th>
                    <th class="min-w-100px">{{__('auth._actions')}}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @if (!empty($items) && $items->count() > 0)
                    @foreach ($items as $item)
                        <tr data-role="{{ strtolower($item->name) }}">
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" />
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-light fw-bold">{{__('ID-')}}{{ $item->variant->sku }}</div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="symbol symbol-50px">
                                        <img src="{{ productImage($item->variant->image_url) }}" alt="" class="symbol-label">
                                    </a>
                                    <div class="ms-5">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{ $item->variant->name }}</a>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="badge badge-light fw-bold">{{ $item->departmentItem->name ?? __('pagination._none') }}</div>
                            </td>
                            <td class="fw-bold text-success ms-3">{{ $item->quantity_on_hand }}</td>
                            <td class="fw-bold text-warning ms-3">{{ $item->quantity_allocated }}</td>
                            <td>{{ $item->quantity_on_order }}</td>
                            <td class="badge badge-light fw-bold">{{ $item->reorder_point }}</td>
                            <td class="fw-bold text-primary ms-3">{{ $item->preferred_stock_level }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ $item->expiry_date }}</td>
                            <td>
                                <div class="badge badge-light fw-bold">{{ $item->itemCreater->name ?? __('pagination._none')}}</div>
                            </td>
                            <td>{{ $item->created_at->format('d M Y, h:i a') }}</td>
                            <td class="d-flex align-items-center gap-2 flex-column flex-sm-row">
                                @can('update variant')
                                    <button 
                                        class="btn btn-sm btn-light btn-active-color-primary d-flex align-items-center px-3 py-2" 
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProduct{{$item->id}}">
                                        <i class="bi bi-pencil-square me-1 fs-5"></i> <span>{{ __('auth._edit') }}</span>
                                    </button>
                                @endcan

                                @include('store.inventory-items.edit')
                                
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>


<script>
    function deleteItem(button) {
        const itemId = button.getAttribute('data-item-id');
        const deleteUrl = button.getAttribute('data-item-url');

        const deleteButton = document.getElementById('deleteButton' + itemId);
        LiveBlade.toggleButtonLoading(deleteButton, true);
        
        // Call the delete function to handle the deletion
        LiveBlade.deleteItemInLoop(deleteUrl)
            .then(noErrorStatus => {
                console.log(noErrorStatus)
                if (noErrorStatus) {
                    var closeButton = document.getElementById('closeDeleteModal' + itemId);
                    if (closeButton) {
                        closeButton.click();
                    }
                }
            })
            .catch(error => {
                console.error('An unexpected error occurred:', error);
                // Handle error gracefully
            })
            .finally(() => {
                // End loading state using reusable function
                LiveBlade.toggleButtonLoading(deleteButton, false);
            });
    }
</script>

<script>

    function initializeComponentScripts() {
        const tableId = '#kt_table_users';

        // Destroy the existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }

        // Reinitialize the DataTable
        $(tableId).DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: false, // Disable responsive behavior
            autoWidth: false, // Prevent automatic column resizing
            language: {
                emptyTable: "No data available",
            },
            columnDefs: [
                // Specify widths for specific columns (optional)
                { targets: 0, width: "10%" }, // Example for first column
                { targets: 1, width: "15%" },
            ],
        });

    }

    
    function setupTableSearch(inputId, tableId) {
        LiveBlade.searchTableItems(inputId, tableId)
    }
    
    
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSearch('searchInput', 'kt_table_users');
        
    });
</script>