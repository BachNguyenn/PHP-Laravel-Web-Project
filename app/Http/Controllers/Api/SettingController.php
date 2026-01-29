<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /**
     * Get all settings.
     */
    public function index()
    {
        $settings = Setting::getAllSettings();
        return send_response($settings, 'Settings retrieved successfully');
    }

    /**
     * Update settings (bulk update).
     */
    public function update(Request $request)
    {
        $validKeys = ['gym_name', 'gym_logo', 'gym_address', 'gym_phone', 'gym_email', 'facebook_url', 'youtube_url'];
        
        foreach ($validKeys as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                
                // Handle base64 logo upload
                if ($key === 'gym_logo' && !empty($value) && str_starts_with($value, 'data:image')) {
                    $value = $this->saveBase64Image($value);
                }
                
                Setting::set($key, $value);
            }
        }

        return send_response(Setting::getAllSettings(), 'Settings updated successfully');
    }

    /**
     * Save Base64 encoded image to storage.
     */
    private function saveBase64Image(string $base64Image): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1];
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        } else {
            $extension = 'png';
        }

        $imageData = base64_decode($base64Image);
        $fileName = 'settings/logo_' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->put($fileName, $imageData);

        return '/storage/' . $fileName;
    }
}
