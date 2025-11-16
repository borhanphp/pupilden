<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\OrganizationSetting;
use App\Models\Organization;
use App\Models\Domain;
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
            
            $siteInformation = Organization::with('settings')->where('id', $organization_id)->first();
            if(!$siteInformation){
                return $this->error('Site information not found', ['error' => 'Site information not found']);
            }
            return $this->success('Site information fetched successfully', [
                'site_information' => $siteInformation
            ]);
        } catch (\Exception $e) {
            return $this->error('Error fetching site information', ['error' => $e->getMessage()]);
        }
    }
}
