<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
class OrganizationSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*SELECT `id`, `name`, `slug`, `custom_domain`, `address`, `phone`, `email`, `website`, `facebook`, `twitter`, `instagram`, `linkedin`, `youtube`, `tiktok`, `pinterest`, `logo`, `is_active`, `sms_active`, `email_active`, `whatsapp_active`, `sms_limit`, `email_limit`, `whatsapp_limit`, `plan_type`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at` FROM `organizations` WHERE 1*/
        Organization::create([
            'name' => 'Pupilden',
            'slug' => 'pupilden',
            'custom_domain' => 'pupilden.com',
            'address' => '123 Main St, Anytown, USA',
            'phone' => '123-456-7890',
            'email' => 'info@pupilden.com',
            'website' => 'https://pupilden.com',
            'facebook' => 'https://facebook.com/pupilden',
            'twitter' => 'https://twitter.com/pupilden',
            'instagram' => 'https://instagram.com/pupilden',
            'linkedin' => 'https://linkedin.com/pupilden',
            'youtube' => 'https://youtube.com/pupilden',
            'tiktok' => 'https://tiktok.com/pupilden',
            'pinterest' => 'https://pinterest.com/pupilden',
            'logo' => 'https://pupilden.com/logo.png',
            'is_active' => 1,
            'sms_active' => 1,
            'email_active' => 1,
            'whatsapp_active' => 1,
            'sms_limit' => 1000,
            'email_limit' => 1000,
            'whatsapp_limit' => 1000,
            'plan_type' => 'free',
            'status' => 'active',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
