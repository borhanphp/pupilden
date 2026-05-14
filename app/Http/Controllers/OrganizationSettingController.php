<?php

namespace App\Http\Controllers;

use App\Models\OrganizationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizationId = auth()->user()->organization_id;
        $setting = OrganizationSetting::where('organization_id', $organizationId)->first();

        if (!$setting) {
            return redirect()->route('organization-settings.create');
        }

        return redirect()->route('organization-settings.show', $setting);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizationId = auth()->user()->organization_id;
        $setting = OrganizationSetting::where('organization_id', $organizationId)->first();

        if ($setting) {
            return redirect()->route('organization-settings.edit', $setting);
        }

        return view('organization-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $organizationId = auth()->user()->organization_id;

        // Check if settings already exist
        $existing = OrganizationSetting::where('organization_id', $organizationId)->first();
        if ($existing) {
            return redirect()->route('organization-settings.edit', $existing)
                ->with('error', 'Settings already exist. Please update instead.');
        }

        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'template' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'privacy_policy_content' => 'nullable|string',
            'about_us_content' => 'nullable|string',
            'footer_color' => 'nullable|string|max:7',
            'footer_design' => 'nullable|string|max:255',
            'copyright_text' => 'nullable|string',
            'business_email' => 'nullable|email|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_text' => 'nullable|string',
            'baksh_number' => 'nullable|string|max:255',
            'ngad_number' => 'nullable|string|max:255',
            'rocket_number' => 'nullable|string|max:255',
            'celfin_number' => 'nullable|string|max:255',
        ]);

        $validated['organization_id'] = $organizationId;

        // Handle file uploads
        $validated = $this->handleFileUploads($request, $validated, $organizationId);

        $setting = OrganizationSetting::create($validated);

        return redirect()->route('organization-settings.show', $setting)
            ->with('success', 'Organization settings created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrganizationSetting $organizationSetting)
    {
        // Verify setting belongs to user's organization
        if ($organizationSetting->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('organization-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $organizationSetting->load('organization');
        return view('organization-settings.show', compact('organizationSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrganizationSetting $organizationSetting)
    {
        // Verify setting belongs to user's organization
        if ($organizationSetting->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('organization-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('organization-settings.edit', compact('organizationSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrganizationSetting $organizationSetting)
    {
        // Verify setting belongs to user's organization
        if ($organizationSetting->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('organization-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'template' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'privacy_policy_content' => 'nullable|string',
            'about_us_content' => 'nullable|string',
            'footer_color' => 'nullable|string|max:7',
            'footer_design' => 'nullable|string|max:255',
            'copyright_text' => 'nullable|string',
            'business_email' => 'nullable|email|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_text' => 'nullable|string',
            'baksh_number' => 'nullable|string|max:255',
            'ngad_number' => 'nullable|string|max:255',
            'rocket_number' => 'nullable|string|max:255',
            'celfin_number' => 'nullable|string|max:255',
        ]);

        // Handle file uploads
        $validated = $this->handleFileUploads($request, $validated, $organizationSetting->organization_id, $organizationSetting);

        $organizationSetting->update($validated);

        return redirect()->route('organization-settings.show', $organizationSetting)
            ->with('success', 'Organization settings updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrganizationSetting $organizationSetting)
    {
        // Verify setting belongs to user's organization
        if ($organizationSetting->organization_id !== auth()->user()->organization_id) {
            return redirect()->route('organization-settings.index')
                ->with('error', 'Unauthorized access.');
        }

        // Delete uploaded files
        $this->deleteFiles($organizationSetting);

        $organizationSetting->delete();

        return redirect()->route('organization-settings.index')
            ->with('success', 'Organization settings deleted successfully!');
    }

    /**
     * Handle file uploads to R2
     */
    private function handleFileUploads(Request $request, array $validated, $organizationId, $existing = null)
    {
        $folder = $organizationId . '/settings';

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($existing && $existing->logo) {
                Storage::disk('r2')->delete($existing->logo);
            }
            $logoName = 'logo_' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $logoPath = $folder . '/' . $logoName;
            $request->file('logo')->storeAs($folder, $logoName, 'r2');
            $validated['logo'] = $logoPath;
        } elseif ($existing) {
            $validated['logo'] = $existing->logo;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($existing && $existing->favicon) {
                Storage::disk('r2')->delete($existing->favicon);
            }
            $faviconName = 'favicon_' . time() . '.' . $request->file('favicon')->getClientOriginalExtension();
            $faviconPath = $folder . '/' . $faviconName;
            $request->file('favicon')->storeAs($folder, $faviconName, 'r2');
            $validated['favicon'] = $faviconPath;
        } elseif ($existing) {
            $validated['favicon'] = $existing->favicon;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($existing && $existing->banner) {
                Storage::disk('r2')->delete($existing->banner);
            }
            $bannerName = 'banner_' . time() . '.' . $request->file('banner')->getClientOriginalExtension();
            $bannerPath = $folder . '/' . $bannerName;
            $request->file('banner')->storeAs($folder, $bannerName, 'r2');
            $validated['banner'] = $bannerPath;
        } elseif ($existing) {
            $validated['banner'] = $existing->banner;
        }

        return $validated;
    }

    /**
     * Delete uploaded files from R2
     */
    private function deleteFiles(OrganizationSetting $setting)
    {
        if ($setting->logo) {
            Storage::disk('r2')->delete($setting->logo);
        }
        if ($setting->favicon) {
            Storage::disk('r2')->delete($setting->favicon);
        }
        if ($setting->banner) {
            Storage::disk('r2')->delete($setting->banner);
        }
    }
}
