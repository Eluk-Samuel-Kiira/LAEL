<?php


use App\Models\Setting;
use App\Models\Currency;

if (! function_exists('is_tab_show')) {
    function is_tab_show($routeName)
    {
        return request()->routeIs($routeName) ? 'show' : '';
    }
}

if (! function_exists('is_route_active')) {
    function is_route_active($routeName)
    {
        return request()->routeIs($routeName) ? 'active' : '';
    }
}


if (!function_exists('trim_description')) {
    function trim_description($text, $wordLimit = 10)
    {
        $words = explode(' ', $text);
        if (count($words) > $wordLimit) {
            return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
        }
        return $text;
    }
}


if (!function_exists('getProfileImage')) {
    function getProfileImage()
    {
        $user = auth()->user();
        $defaultImage = asset('assets/media/avatars/300-3.jpg'); // Default image path

        // Check if the user exists and has a profile image
        if ($user && $user->profile_image) {
            // Use the stored relative path
            $filename = $user->profile_image;

            // Build the full path to the image using the public path
            $path = public_path('storage/' . $filename); // Full path to the file

            // Check if the file exists
            if (file_exists($path)) {
                // Return the URL to access the image
                return asset('storage/' . $filename); // Return the image URL
            }
        }

        // Return the default image URL if no profile image is found
        return $defaultImage;
    }
}

if (!function_exists('employeeProfileImage')) {
    function employeeProfileImage($profile_image)
    {
        $defaultImage = asset('assets/media/avatars/300-6.jpg'); // Default image path

        // Check if the user exists and has a profile image
        if ($profile_image) {
            // Use the stored relative path
            $filename = $profile_image;

            // Build the full path to the image using the public path
            $path = public_path('storage/' . $filename); // Full path to the file

            // Check if the file exists
            if (file_exists($path)) {
                // Return the URL to access the image
                return asset('storage/' . $filename); // Return the image URL
            }
        }

        // Return the default image URL if no profile image is found
        return $defaultImage;
    }
}




if (!function_exists('getLogoImage')) {
    function getLogoImage()
    {
        $setting = Setting::find(1);

        if ($setting && !empty($setting->logo)) {
            $path = 'logos/' . $setting->logo;

            // Check if file exists in storage/app/public/logos
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
        }

        // Fallback: return default logo
        return asset('assets/media/logos/default-dark.svg');
    }
}



if (!function_exists('getFaviconImage')) {
    function getFaviconImage()
    {
        $setting = Setting::find(1);

        if ($setting && !empty($setting->favicon)) {
            $path = 'favicons/' . $setting->favicon;

            // Check if file exists in storage/app/public/favicons
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
        }

        // Fallback: return default favicon
        return asset('assets/media/logos/favicon.ico');
    }
}


// Auto currency converter
if (! function_exists('formatCurrency')) {
    function formatCurrency($amountUSD, $currencyCode = null)
    {
        // Get system default currency from settings if not passed
        $systemCurrency = $currencyCode ?? getMailOptions('currency');

        // Fetch currency from DB
        $currency = Currency::where('code', $systemCurrency)->first();

        if (! $currency) {
            // fallback to USD if not found
            $currency = Currency::where('isBaseCurrency', true)->first();
        }

        // Convert from USD → target currency
        $converted = $amountUSD * $currency->exchange_rate;

        // Return formatted number with symbol after value
        return $converted;
    }
}

if (! function_exists('toUSD')) {
    function toUSD($amount, $currencyCode = null)
    {
        // Get system default currency from settings if not passed
        $systemCurrency = $currencyCode ?? getMailOptions('currency');

        // Fetch currency from DB
        $currency = Currency::where('code', $systemCurrency)->first();

        if (! $currency) {
            $currency = Currency::where('isBaseCurrency', true)->first();
        }

        // Convert from target currency → USD
        return $amount / $currency->exchange_rate;
    }
}



// Pretty display
if (! function_exists('currencySymbol')) {
    function currencySymbol()
    {
        // Get system default currency from settings if not passed
        $systemCurrency = $currencyCode ?? getMailOptions('currency');

        // Fetch currency from DB
        $currency = Currency::where('code', $systemCurrency)->first();

        return $currency->symbol;
    }
}

// Pretty display
if (! function_exists('displayFormatedCurrency')) {
    function displayFormatedCurrency($amount)
    {
        // Return formatted number with symbol after value
        return number_format($amount, 2);
    }
}




function getMailOptions($option_key){
    $app_details = Setting::first();
    if ($option_key && isset($app_details[$option_key])) {
        return $app_details[$option_key];
    } else {
        $default = '';
        return $default;
    }
}


if (!function_exists('appDefaultName')) {
    function appDefaultName()
    {
        $setting = Setting::find(1);
        
        if ($setting && !empty($setting->app_name)) {
            // Extract the first word from the app_name
            $firstWord = explode(' ', trim($setting->app_name))[0];
            return $firstWord;
        }

        return 'Lael';
    }
}

if (!function_exists('productCategoryImage')) {
    function productCategoryImage($categoryImagePath = '')
    {
        $defaultImage = asset('assets/media/stock/ecommerce/123.png');

        if (!empty($categoryImagePath)) {
            $fullPath = public_path('storage/' . ltrim($categoryImagePath, '/'));
            if (file_exists($fullPath)) {
                return asset('storage/' . ltrim($categoryImagePath, '/'));
            }
        }

        return $defaultImage;
    }
}

if (!function_exists('productImage')) {
    function productImage($productImagePath = '')
    {
        $defaultImage = asset('assets/media/stock/ecommerce/123.png');

        if (!empty($productImagePath)) {
            $fullPath = public_path('storage/' . ltrim($productImagePath, '/'));
            if (file_exists($fullPath)) {
                return asset('storage/' . ltrim($productImagePath, '/'));
            }
        }

        return $defaultImage;
    }
}

if (!function_exists('productVariantImage')) {
    function productVariantImage($variantImagePath = '')
    {
        $defaultImage = asset('assets/media/stock/ecommerce/2.png');

        if (!empty($variantImagePath)) {
            $fullPath = public_path('storage/' . ltrim($variantImagePath, '/'));
            if (file_exists($fullPath)) {
                return asset('storage/' . ltrim($variantImagePath, '/'));
            }
        }

        return $defaultImage;
    }
}

