





<!-- Suppliers and Purchases -->
<script>
    
    function submitSupplierForm(formId, submitButtonId, url, method = 'POST', discardButtonId = 'discardButton') {
        const form = document.getElementById(formId);
        const submitButton = document.getElementById(submitButtonId);

        if (!form || !submitButton) {
            console.error('Form or button not found:', formId, submitButtonId);
            return;
        }

        // Collect form data
        const formData = Object.fromEntries(new FormData(form));
        formData._method = method;
        formData.routeName = url;

        // Start loading
        LiveBlade.toggleButtonLoading(submitButton, true);

        // Pass handling + data to reusable handler
        handleFormSubmission(formData, submitButton, discardButtonId);
    }


    function updateSupplierInstance(uniqueId) {
        const submitButton = document.getElementById('editSupplierButton' + uniqueId);
        LiveBlade.toggleButtonLoading(submitButton, true);

        // Select the form and create FormData from it
        var form = document.getElementById('kt_modal_supplier_form' + uniqueId);
        var formData = new FormData(form);

        var data = Object.fromEntries(formData.entries());
        // console.log(data);

        // Set up the URL dynamically
        var updateUrl = '{{ route('suppliers.update', ['supplier' => ':id']) }}'.replace(':id', uniqueId);
        
        // Submit form data asynchronously
        handleEditResponse(data, updateUrl, uniqueId, submitButton);

    }
    
    function updateSupplierStatus(uniqueId, selectedStatus) {
        // Update label instantly
        const label = document.getElementById('supplier-label-' + uniqueId);
        if (label) {
            label.innerText = selectedStatus == 1 ? '{{ __("auth._active") }}' : '{{ __("auth._inactive") }}';
        }

        // Send update to backend
        const updateRoute = '/supplier-status/' + uniqueId;
        LiveBlade.loopUpdateStatus(updateRoute, selectedStatus);
    }

</script>
