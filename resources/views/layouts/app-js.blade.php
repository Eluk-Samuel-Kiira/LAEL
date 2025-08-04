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
