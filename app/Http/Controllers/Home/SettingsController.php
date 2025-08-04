<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $app_info = Setting::find(1);
        return view('settings.index', [
            'app_info' => $app_info
        ]);
    }

    // Languages
    public function switchLocale($locale)
    {
        if (in_array($locale, ['en', 'fr', 'lg'])) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        // update system language
        $app_locale = Setting::first();
        $app_locale->update([
            'locale' => $locale
        ]);

        return redirect()->back();
    }

    public function updateSettings(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
            'app_contact' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_descrip' => 'nullable|string|max:500',
            'favicon' => 'nullable|string|max:255', 
            'logo' => 'nullable|string|max:255',
            'mail_status' => 'nullable|in:enabled,disabled',
            'mail_mailer' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_address' => 'nullable|string|max:255',
            'mail_name' => 'nullable|string|max:255',
        ]);

        // Retrieve the application settings
        $setting = Setting::first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => __('auth.settings_not_found'),
            ], 404);
        }

        $componentToReload = null;

        // Determine what to update based on provided inputs
        if ($request->hasAny(['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_address', 'mail_name', 'mail_status'])) {
            $setting->update($request->only([
                'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 
                'mail_password', 'mail_encryption', 'mail_address', 
                'mail_name', 'mail_status',
            ]));
            $componentToReload = 'updateSMTPForm';

        } elseif ($request->hasAny(['app_name', 'app_email', 'app_contact', 'currency'])) {
            $setting->update($request->only(['app_name', 'app_email', 'app_contact','currency']));
            $componentToReload = 'updateAppInfoForm';

        } elseif ($request->hasAny(['meta_keyword', 'meta_descrip'])) {
            $setting->update($request->only(['meta_keyword', 'meta_descrip']));
            $componentToReload = 'updateMetaInfoForm';

        } elseif ($request->hasAny(['menu_nav_color', 'font_family', 'font_size'])) {
            $setting->update($request->only(['menu_nav_color', 'font_family', 'font_size']));
            $componentToReload = 'updateMetaInfoForm';

        } else {
            return response()->json([
                'success' => false,
                'message' => __('auth.something_wrong'),
            ], 400);
        }

        // Return success response
        return response()->json([
            'success' => true,
            'message' => __('auth._updated'),
            'reload' => true,
            'component' => $componentToReload,
            'redirect' => route('settings.index'),
        ], 200);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo_image' => 'required|image|mimes:jpeg,png,gif|max:5120', // Max size: 5MB
        ]);

        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');

            // Store file in storage/app/public/logos and return filename
            $path = $file->store('logos', 'public'); // returns something like logos/logo_abc123.png

            // Delete old file if it exists
            $setting = Setting::find(1);
            if ($setting && $setting->logo) {
                Storage::disk('public')->delete('logos/' . $setting->logo);
            }

            // Save only the filename (optional: you can store full $path)
            $filename = basename($path);
            $setting->update(['logo' => $filename]);

            return response()->json([
                'success' => true,
                'message' => __('auth._uploaded'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('auth.upload_failed'),
        ], 400);
    }


    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon_image' => 'required|file|mimes:ico|max:1024', // Max size: 1MB, accepts .ico only
        ]);

        try {
            if ($request->hasFile('favicon_image')) {
                $file = $request->file('favicon_image');

                // Store in storage/app/public/favicons
                $path = $file->storeAs('favicons', 'favicon_' . time() . '.ico', 'public');

                // Fetch setting
                $setting = Setting::find(1);

                // Delete old favicon if it exists
                if ($setting && $setting->favicon) {
                    Storage::disk('public')->delete('favicons/' . $setting->favicon);
                }

                // Save just the filename
                $filename = basename($path);
                $setting->update(['favicon' => $filename]);

                return response()->json([
                    'success' => true,
                    'message' => __('auth._uploaded'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('auth.upload_failed'),
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Upload Favicon Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Favicon upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

}
