<?php

namespace App\Filament\Resources\LandingSettingResource\Pages;

use App\Filament\Resources\LandingSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditLandingSetting extends EditRecord
{
    protected static string $resource = LandingSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $raw = $data['value'] ?? [];

        switch ($data['key']) {
            case 'landing_business_info':
                $data['info_address'] = $raw['address'] ?? null;
                $data['info_email'] = $raw['email'] ?? null;
                $data['info_maps'] = $raw['maps_iframe'] ?? null;
                break;

            case 'landing_seo_tags':
                $data['seo_title'] = $raw['meta_title'] ?? null;
                $data['seo_description'] = $raw['meta_description'] ?? null;
                $data['seo_keywords'] = $raw['meta_keywords'] ?? null;
                break;

            case 'landing_pixel_ids':
                $data['pixel_google'] = $raw['google_analytics_id'] ?? null;
                $data['pixel_facebook'] = $raw['facebook_pixel_id'] ?? null;
                $data['pixel_tiktok'] = $raw['tiktok_pixel_id'] ?? null;
                break;
            case 'landing_reservation_areas':
            case 'landing_opening_hours':
                $data['value_simple_json'] = $raw;
                break;
            case 'landing_hero_section':
                $data['hero_title'] = $raw['title'] ?? null;
                $data['hero_subtitle'] = $raw['subtitle'] ?? null;
                $data['hero_video_url'] = $raw['video_url'] ?? null;
                $data['hero_main_image'] = $raw['main_image'] ?? null;
                break;
            case 'landing_social_links':
                $data['social_facebook'] = $raw['facebook'] ?? null;
                $data['social_instagram'] = $raw['instagram'] ?? null;
                $data['social_whatsapp'] = $raw['whatsapp'] ?? null;
                break;
            case 'landing_featured_menu':
                $data['value_menu'] = $raw;
                break;
            case 'landing_events_gallery':
                $data['value_events'] = $raw;
                break;
            case 'landing_gallery':
                $data['value_gallery'] = $raw;
                break;
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        switch ($this->record->key) {
            case 'landing_business_info':
                $data['value'] = [
                    'address' => $data['info_address'] ?? null,
                    'email' => $data['info_email'] ?? null,
                    'maps_iframe' => $data['info_maps'] ?? null,
                ];
                break;

            case 'landing_seo_tags':
                $data['value'] = [
                    'meta_title' => $data['seo_title'] ?? null,
                    'meta_description' => $data['seo_description'] ?? null,
                    'meta_keywords' => $data['seo_keywords'] ?? null,
                ];
                break;

            case 'landing_pixel_ids':
                $data['value'] = [
                    'google_analytics_id' => $data['pixel_google'] ?? null,
                    'facebook_pixel_id' => $data['pixel_facebook'] ?? null,
                    'tiktok_pixel_id' => $data['pixel_tiktok'] ?? null,
                ];
                break;
            case 'landing_reservation_areas':
            case 'landing_opening_hours':
                $data['value'] = $data['value_simple_json'] ?? [];
                break;
            case 'landing_hero_section':
                $data['value'] = [
                    'title' => $data['hero_title'] ?? null,
                    'subtitle' => $data['hero_subtitle'] ?? null,
                    'video_url' => $data['hero_video_url'] ?? null,
                    'main_image' => $data['hero_main_image'] ?? null,
                ];
                break;
            case 'landing_social_links':
                $data['value'] = [
                    'facebook' => $data['social_facebook'] ?? null,
                    'instagram' => $data['social_instagram'] ?? null,
                    'whatsapp' => $data['social_whatsapp'] ?? null,
                ];
                break;
            case 'landing_featured_menu':
                $data['value'] = $data['value_menu'] ?? [];
                break;
            case 'landing_events_gallery':
                $data['value'] = $data['value_events'] ?? [];
                break;
            case 'landing_gallery':
                $data['value'] = $data['value_gallery'] ?? [];
                break;
        }

        $fields = [
            'value_simple_json', 'hero_title', 'hero_subtitle', 'hero_video_url', 'hero_main_image', 
            'social_facebook', 'social_instagram', 'social_whatsapp', 'value_menu', 'value_events', 
            'value_gallery', 'info_address', 'info_email', 'info_maps', 'seo_title', 'seo_description', 
            'seo_keywords', 'pixel_google', 'pixel_facebook', 'pixel_tiktok'
        ];
        
        foreach ($fields as $field) { unset($data[$field]); }

        return $data;
    }
}