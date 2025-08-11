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

