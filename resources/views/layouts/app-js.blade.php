<!-- important imports for laravel liveblades -->
<script src="{{ asset('blade-live/forms/forms.min.js') }}" type="module"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<style>
    body {
        font-family: Arial, sans-serif;
    }
    nav {
        margin-bottom: 10px;
    }
    a {
        margin-right: 10px;
        text-decoration: none;
        color: blue;
    }
    a:hover {
        text-decoration: underline;
    }
    #content {
        padding: 20px;
        border: 1px solid #ddd;
        margin-top: 10px;
        min-height: 100px;
        position: relative;
    }
    #loader {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-top: 4px solid blue;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>


<!-- Page Navigator functions -->
<script>
    // Function to show the loader
    function showLoaderApp() {
        const loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'block';
        }
    }

    // Function to hide the loader
    function hideLoaderApp() {
        const loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }


    // Function to handle navigation
    function navigateToAppPages(url) {
        showLoaderApp(); // Show loader during content loading

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                history.pushState({ url: url }, null, url); // Store state in history

                // Extract the content within #kt_app_main
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const ktAppMain = doc.getElementById('kt_app_main');

                if (ktAppMain) {
                    // Update the title and content
                    const titleMatch = data.match(/<title>(.*?)<\/title>/i);
                    document.title = titleMatch ? titleMatch[1] : 'Default Title';

                    // Replace the content of #kt_app_main in the current page
                    document.getElementById('kt_app_main').innerHTML = ktAppMain.innerHTML;

                    // Update menu active state
                    updateActiveMenuLink(url);
                } else {
                    console.error('Error: #kt_app_main not found in the fetched content.');
                }

                setTimeout(hideLoader, 300);
            })
            .catch(error => {
                console.error('Error fetching content:', error);
                document.getElementById('kt_app_main').innerHTML = '404 Page Not Found.';
                hideLoaderApp();
            });
    }

    // Function to load content based on URL
    function renderAppPage(url) {
        const pageContent = document.getElementById('kt_app_main');

        if (!pageContent) {
            console.error('Error: Element #kt_app_main not found.');
            return; // Stop execution if the element is missing
        }

        showLoaderApp(); // Show loader during content loading

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Get the HTML content
            })
            .then(data => {
                // Parse the fetched HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const ktAppMain = doc.getElementById('kt_app_main');

                if (ktAppMain) {
                    // Extract the title from the fetched content
                    const titleMatch = data.match(/<title>(.*?)<\/title>/i);
                    document.title = titleMatch ? titleMatch[1] : 'Default Title'; // Update the document title

                    // Insert the fetched content into the page
                    pageContent.innerHTML = ktAppMain.innerHTML;
                } else {
                    console.error('Error: #kt_app_main not found in the fetched content.');
                }

                // Hide the loader after a small delay (optional)
                setTimeout(hideLoader, 300);
            })
            .catch(error => {
                console.error('Error fetching content:', error);

                if (pageContent) {
                    pageContent.innerHTML = '404 Page Not Found.'; // Fallback content
                }

                hideLoaderApp();
            });
    }


    // Handle back/forward navigation
    window.addEventListener('popstate', (event) => {
        if (event.state && event.state.url) {
            renderAppPage(event.state.url); // Load the correct content
        } else {
            renderAppPage(window.location.pathname); // Default behavior
        }
    });

    // Reload page especially when going to the database
    function reloadToApp(url) {
        window.location.href = url; // Redirect on success
    }

    function updateActiveMenuLink(url) {
        // Remove 'active' class from all menu links
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });

        // Add 'active' class to the matching menu link
        document.querySelectorAll('.menu-link').forEach(link => {
            const onclickAttr = link.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes(url)) {
                link.classList.add('active');
            }
        });
    }

    // Initial load
    renderAppPage(window.location.pathname);
</script>




<!-- Currency -->
<script>

    function updateStatusCurrency(uniqueId, selectedStatus) {
        // console.log(" ID:", uniqueId, "Selected status:", selectedStatus);
        const updateRoute = '/currency-status/' + uniqueId;
        LiveBlade.loopUpdateStatus(updateRoute, selectedStatus);
    }

    function editInstanceLoopCurrency(uniqueId) {
        const submitButton = document.getElementById('editCurrencyButton' + uniqueId);
        LiveBlade.toggleButtonLoading(submitButton, true);

        var form = document.getElementById('kt_modal_edit_currency_form' + uniqueId);
        var formData = new FormData(form);

        var data = Object.fromEntries(formData.entries());
        // console.log(data);
        var updateUrl = '{{ route('currency.update', ['currency' => ':id']) }}'.replace(':id', uniqueId);
        handleEditResponse(data, updateUrl, uniqueId, submitButton);
    }

    function submitCurrencyForm(formId, submitButtonId, url, method = 'POST', discardButtonId = 'discardButton') {
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

</script>


<!-- Settings -->
<script>

    function submitSettingFormEntities(formId, submitButtonId, url, method = 'POST', discardButtonId = '') {
        const form = document.getElementById(formId);
        const submitButton = document.getElementById(submitButtonId);

        // Collect form data
        const formData = Object.fromEntries(new FormData(form));
        formData._method = method;
        formData.routeName = url;
        console.log(formData);

        // Start loading
        LiveBlade.toggleButtonLoading(submitButton, true);

        // Pass handling + data to reusable handler
        handleFormSubmission(formData, submitButton, discardButtonId);
    }


    function changeLocale(locale) {
        window.location.href = "/change-locale/" + locale;
    }
</script>


<!-- Unit Of Measure -->
<script> 
    function submitFormUOM(formId, submitButtonId, url, method = 'POST', discardButtonId = 'discardButton') {
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

    function editInstanceLoopUOM(uniqueId) {
        
        const submitButton = document.getElementById('editUOMButton' + uniqueId);
        LiveBlade.toggleButtonLoading(submitButton, true);
        
        var form = document.getElementById('kt_modal_edit_uom_form' + uniqueId);
        var formData = new FormData(form);
        var data = Object.fromEntries(formData.entries());
        // console.log(data);

        var updateUrl = '{{ route('uom.update', ['uom' => ':id']) }}'.replace(':id', uniqueId);
        handleEditResponse(data, updateUrl, uniqueId, submitButton);
    }


    function updateStatusUOM(uniqueId, selectedStatus) {
        // console.log(" ID:", uniqueId, "Selected status:", selectedStatus);
        const updateRoute = '/uom-status/' + uniqueId;
        LiveBlade.loopUpdateStatus(updateRoute, selectedStatus);
    }

</script>



<!-- Department -->
<script>
    
    function submitFormDept(formId, submitButtonId, url, method = 'POST', discardButtonId = 'discardButton') {
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

    function editInstanceLoopDept(uniqueId) {
        const submitButton = document.getElementById('editDepartmentButton' + uniqueId);
        LiveBlade.toggleButtonLoading(submitButton, true);

        const form = document.getElementById('kt_modal_edit_department_form' + uniqueId);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        // console.log(data)

        // Set up the URL dynamically
        var updateUrl = '{{ route('department.update', ['department' => ':id']) }}'.replace(':id', uniqueId);
        handleEditResponse(data, updateUrl, uniqueId, submitButton);
    }

    function updateStatusDept(uniqueId, selectedStatus) {
        // console.log(" ID:", uniqueId, "Selected status:", selectedStatus);
        const updateRoute = '/department-status/' + uniqueId;
        LiveBlade.loopUpdateStatus(updateRoute, selectedStatus);
    }

</script>


<!-- General Reusable Functions -->
<script>
    
    // for general table searches
    // Auto Table search
    function searchTable(query, tableId) {
        const table = document.getElementById(tableId);
        if (!table) {
            console.error('Table not found:', tableId);
            return;
        }

        const rows = table.getElementsByTagName('tr');
        query = query.toLowerCase();

        for (let i = 1; i < rows.length; i++) { // skip header row
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(query)) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? '' : 'none';
        }
    }


    // Reintialize the datatables after submission
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
    

    // General General form submitter
    function handleFormSubmission(formData, submitButton, discardButtonId = 'discardButton') {
        LiveBlade.submitFormItems(formData)
            .then(noErrors => {
                console.log(noErrors);

                if (noErrors) {
                    const closeModal = document.getElementById(discardButtonId);
                    if (closeModal) closeModal.click();
                }
            })
            .catch(error => {
                console.error('An unexpected error occurred:', error);
            })
            .finally(() => {
                LiveBlade.toggleButtonLoading(submitButton, false);
            });
    }


    // General Update or Edit Function 
    function handleEditResponse(data, updateUrl, uniqueId, submitButton) {
        LiveBlade.editLoopForms(data, updateUrl)
        .then(noErrorStatus => {
            if (noErrorStatus) {
                const closeButton = document.getElementById(`closeModalEditButton${uniqueId}`);
                if (closeButton) closeButton.click();
            }
        })
        .catch(error => {
            console.error('An unexpected error occurred:', error);
        })
        .finally(() => {
            LiveBlade.toggleButtonLoading(submitButton, false);
        });
    }


    // General Delete function
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