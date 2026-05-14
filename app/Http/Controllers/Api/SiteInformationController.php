<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\PaymentGateway;
use App\Models\Organization;
use App\Models\Domain;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;
class SiteInformationController extends BaseController
{
    public $organization_id;

    /**
     * Validate domain and get organization ID
     */
    private function validateDomain(Request $request)
    {
        if(!$request->has('domain_name')){
            return ['error' => 'Domain name is required'];
        }
        
        $domain = Domain::where('domain_name', $request->domain_name)->where('is_active', true)->first();
        if(!$domain){
            return ['error' => 'Domain not found'];
        }

        return ['organization_id' => $domain->organization_id];
    }
    public function siteInformation(Request $request)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }
            
            $organization_id = $domainValidation['organization_id'];
            
            $organization = Organization::with('settings')->where('id', $organization_id)->first();
            
            if(!$organization){
                return $this->error('Organization not found', ['error' => 'Organization not found']);
            }

            $settings = $organization->settings;
            
            $siteInformation = [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'custom_domain' => $organization->custom_domain,
                'address' => $organization->address,
                'phone' => $organization->phone,
                'email' => $organization->email,
                'website' => $organization->website,
                'facebook' => $organization->facebook,
                'twitter' => $organization->twitter,
                'instagram' => $organization->instagram,
                'linkedin' => $organization->linkedin,
                'youtube' => $organization->youtube,
                'tiktok' => $organization->tiktok,
                'pinterest' => $organization->pinterest,
                'logo' => $settings && $settings->getOriginal('logo') ? Storage::disk('r2')->url($settings->getOriginal('logo')) : null,
                'favicon' => $settings && $settings->getOriginal('favicon') ? Storage::disk('r2')->url($settings->getOriginal('favicon')) : null,
                'template' => $settings ? $settings->template : null,
                'primary_color' => $settings ? $settings->primary_color : null,
                'footer_color' => $settings ? $settings->footer_color : null,
                'footer_design' => $settings ? $settings->footer_design : null,
                'copyright_text' => $settings ? $settings->copyright_text : null,
                'business_email' => $settings ? $settings->business_email : null,
                'banner' => $settings && $settings->getOriginal('banner') ? Storage::disk('r2')->url($settings->getOriginal('banner')) : null,
                'hero_text' => $settings ? $settings->hero_text : null,
                'baksh_number' => $settings ? $settings->baksh_number : null,
                'ngad_number' => $settings ? $settings->ngad_number : null,
                'rocket_number' => $settings ? $settings->rocket_number : null,
                'celfin_number' => $settings ? $settings->celfin_number : null,
                'about_us_content' => $settings ? $settings->about_us_content : null,
                'privacy_policy_content' => $settings ? $settings->privacy_policy_content : null,
            ];
            return $this->success('Site information fetched successfully', [
                'site_information' => $siteInformation
            ]);
        } catch (\Exception $e) {
            return $this->error('Error fetching site information', ['error' => $e->getMessage()]);
        }
    }

    public function paymentGateways(Request $request)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }

            $organization_id = $domainValidation['organization_id'];

            $paymentGateways = PaymentGateway::where('organization_id', $organization_id)->get();

            return $this->success('Payment gateways fetched successfully', [
                'payment_gateways' => $paymentGateways
            ]);
        } catch (\Exception $e) {
            return $this->error('Error fetching payment gateways', ['error' => $e->getMessage()]);
        }
    }

    public function sliders(Request $request)
    {
        try {
            $domainValidation = $this->validateDomain($request);
            if (isset($domainValidation['error'])) {
                return $this->error($domainValidation['error'], ['error' => $domainValidation['error']]);
            }

            $organization_id = $domainValidation['organization_id'];

            $sliders = Slider::where('organization_id', $organization_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get()
                ->map(function ($slider) {
                    return [
                        'id' => $slider->id,
                        'title' => $slider->title,
                        'description' => $slider->description,
                        'link' => $slider->link,
                        'image_url' => $slider->image_url,
                        'sort_order' => $slider->sort_order,
                        'is_active' => $slider->is_active,
                    ];
                })
                ->values();

            return $this->success('Sliders fetched successfully', [
                'sliders' => $sliders,
            ]);
        } catch (\Exception $e) {
            return $this->error('Error fetching sliders', ['error' => $e->getMessage()]);
        }
    }
}
