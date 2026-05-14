<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organization = Organization::find(auth()->user()->organization_id);
        $settings = OrganizationSetting::where('organization_id', $organization->id)->first();
        return view('organization.index', compact('organization', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        // Verify organization belongs to user
        if ($organization->id !== auth()->user()->organization_id) {
            return redirect()->route('organizations.index')
                ->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug,' . $organization->id,
            'custom_domain' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'pinterest' => 'nullable|url|max:255',
        ]);
        
        $organization->update($request->all());
        return redirect()->route('organizations.index')->with('success', 'Organization profile updated successfully');
    }

    /**
     * Update organization settings
     */
    public function updateSettings(Request $request)
    {
        $organizationId = auth()->user()->organization_id;

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

        $settings = OrganizationSetting::where('organization_id', $organizationId)->first();

        if (!$settings) {
            $validated['organization_id'] = $organizationId;
            $validated = $this->handleFileUploads($request, $validated, $organizationId);
            OrganizationSetting::create($validated);
            return redirect()->route('organizations.index')
                ->with('success', 'Organization settings created successfully!');
        }

        $validated = $this->handleFileUploads($request, $validated, $organizationId, $settings);
        $settings->update($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization settings updated successfully!');
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
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }
}
