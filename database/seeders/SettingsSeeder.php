<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // //audience
            // 'newsletter_audience' => [
            //     'viewed_my_profile' => 'People who have viewed my profile',
            //     'not_viewed_my_profile' => 'People who have not viewed my profile',
            //     'viewed_listing' => 'People who have viewed my listing',
            //     'not_viewed_listing' => 'People who have not viewed my listing',
            //     'bought_listing' => 'People who have bought my listing',
            //     'not_bought_listing' => 'People who have not bought my listing',
            //     'offers_without_purchase' => 'People who have made offers on my listing without buying',
            //     'offers_without_purchase' => 'People who have made offers on my listing without buying',
            // ],

            ['name' => 'auto_approve_listings', 'value' => '0', 'type' => 'boolean', 'segment' => 'marketplace'],
            ['name' => 'abandoned_cart_reaction_hours', 'value' => '48', 'type' => 'integer', 'segment' => 'marketplace'],
            ['name' => 'cart_reaction_gap_days', 'value' => '30', 'type' => 'integer', 'segment' => 'marketplace'],
            ['name' => 'prohibited_words', 'value' => 'free,win', 'type' => 'string', 'segment' => 'marketplace'],
            ['name' => 'gateways', 'value' => json_encode(['paystack', 'flutterwave', 'opay']), 'type' => 'array', 'segment' => 'marketplace'],


            ['name' => 'max_media_image_size', 'value' => '10', 'type' => 'integer', 'segment' => 'media'],
            ['name' => 'max_media_image_width', 'value' => '1000', 'type' => 'integer', 'segment' => 'media'],
            ['name' => 'max_media_image_height', 'value' => '1000', 'type' => 'integer', 'segment' => 'media'],
            ['name' => 'max_media_video_size', 'value' => '10', 'type' => 'integer', 'segment' => 'media'],
            ['name' => 'max_media_document_size', 'value' => '10', 'type' => 'integer', 'segment' => 'media'],

            // Promotions Settings (tab: promotions)
            ['name' => 'auto_approve_promotions', 'value' => '1', 'type' => 'boolean', 'segment' => 'promotions'],
            ['name' => 'minimum_promotion_clicks', 'value' => '10', 'type' => 'integer', 'segment' => 'promotions'],
            ['name' => 'minimum_promotion_views', 'value' => '10000', 'type' => 'integer', 'segment' => 'promotions'],

            // Community Settings (tab: community)
            ['name' => 'auto_approve_discussion', 'value' => '1', 'type' => 'boolean', 'segment' => 'community'],

            ['name' => 'order_processing_to_cancel_hours', 'value' => '6', 'type' => 'integer', 'segment' => 'timelines'], //time between when buyer can cancel
            ['name' => 'order_processing_to_auto_cancel_warning_hours', 'value' => '12', 'type' => 'integer', 'segment' => 'timelines'], //if vendor doesnt fulfil the order before time
            ['name' => 'order_processing_to_auto_cancel_hours', 'value' => '48', 'type' => 'integer', 'segment' => 'timelines'], //if vendor doesnt fulfil the order before time
            ['name' => 'order_pickup_allowance_hours', 'value' => '48', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_shipped_to_delivery_hours', 'value' => '72', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_delivered_to_auto_reception_hours', 'value' => '12', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_delivered_to_auto_acceptance_hours', 'value' => '12', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_rejected_to_returned_hours', 'value' => '72', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_returned_to_auto_acceptance_hours', 'value' => '12', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_replacement_to_auto_refund_hours', 'value' => '48', 'type' => 'integer', 'segment' => 'timelines'],
            ['name' => 'order_accepted_to_settlement_eligible_hours', 'value' => '24', 'type' => 'integer', 'segment' => 'timelines'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting['name']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'segment' => $setting['segment'] ?? null,
                ]
            );
        }
    }
}
