<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Response;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoRequestAndResponseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNull('role_id')->get();
        if ($users->count() < 3) {
            $this->call(DemoUsersSeeder::class);
            $users = User::whereNull('role_id')->get();
        }

        // Cache user subscription limits
        $userLimits = [];
        foreach ($users as $u) {
            $sub = Subscription::where('user_id', $u->id)->where('status', 'active')->first();
            $userLimits[$u->id] = [
                'request_limit' => $sub ? (int) $sub->request_limit : 1,
                'response_limit' => $sub ? (int) $sub->response_limit : 1,
                'requests_created' => 0,
                'responses_created' => 0,
            ];
        }

        // Helper to pick model & category
        $camryModel = DeviceModel::where('name', 'like', '%Camry%')->first() ?? DeviceModel::first();
        $hpModel = DeviceModel::where('name', 'like', '%EliteBook%')->first() ?? DeviceModel::first();
        $benzModel = DeviceModel::where('name', 'like', '%Mercedes%')->first() ?? DeviceModel::first();

        // 3 Community Requests (Discussions)
        $requestsData = [
            [
                'author_index' => 0, // e.g. Emeka
                'type' => 'item',
                'title' => 'Urgent: Looking for Toyota Camry 2.4L 2AZ-FE Complete Tokunbo Engine in Lagos',
                'body' => "Client's 2008 Camry 2.4L experienced rod knock. Need a clean, low-mileage Belgium/Tokunbo 2AZ-FE complete engine with intake manifold and wiring harness. Must come with testing warranty against oil burning and compression issues. Ready to pick up in Ladipo or Ikeja.",
                'budget' => '₦480,000',
                'category_id' => $camryModel?->category_id,
                'brand_id' => $camryModel?->brand_id,
                'model_id' => $camryModel?->id,
            ],
            [
                'author_index' => 1, // e.g. Tunde
                'type' => 'item',
                'title' => 'Need Clean Original Motherboard for HP EliteBook 840 G5 (Core i5 8th Gen)',
                'body' => "Need an untouched, original factory motherboard for HP EliteBook 840 G5. Part number L15525-601. No reworked or heat-gun blown boards please. Must boot smoothly to Windows with sound and charging working 100%.",
                'budget' => '₦70,000',
                'category_id' => $hpModel?->category_id,
                'brand_id' => $hpModel?->brand_id,
                'model_id' => $hpModel?->id,
            ],
            [
                'author_index' => 2, // e.g. Chioma
                'type' => 'service',
                'title' => 'Need On-Site Diagnostic & Key Coding Specialist for Mercedes W204 EIS/ESL Issue',
                'body' => "2011 Mercedes-Benz C300 key turns in ignition but cluster won't light up and steering lock does not release. Battery is fully charged. Looking for an experienced auto-electrician with Star Diagnostics or Autel to diagnose and install an ESL emulator on-site in Lekki.",
                'budget' => '₦45,000',
                'category_id' => $benzModel?->category_id,
                'brand_id' => $benzModel?->brand_id,
                'model_id' => $benzModel?->id,
            ],
        ];

        // Pool of realistic responses
        $responsesPool = [
            0 => [
                "Hello, I have two tested 2AZ-FE units just arrived from our container at Mushin. Cylinder compression is 175-180 PSI across all 4 cylinders. 14 days full testing warranty included. You can inspect at our shop before paying.",
                "Available at our Ladipo warehouse. Clean tokunbo with standard manifolds, never opened in Nigeria. Price is 460,000 NGN including loading.",
                "We have one in stock tested with video proof of crank and compression. Can arrange delivery across Lagos through platform escrow.",
            ],
            1 => [
                "I have an original pulled board from a corporate lease donor laptop with a broken screen. Never repaired or reflowed, BIOS is clean and unlocked. 14 days warranty.",
                "Available at Computer Village, Ikeja. Tested Core i5 8th Gen board. We can even install and test in your laptop shell in our workshop.",
                "Original HP spare board in stock. Includes heatsink and fan assembly if you need it. Ready for immediate pickup.",
            ],
            2 => [
                "I can handle this on-site in Lekki today. I carry pre-programmed ESL emulators and Autel MaxiIM IM608. Diagnosis, emulator installation and key pairing will take under 2 hours.",
                "Specialist in Mercedes EIS/ESL repairs based in Victoria Island. We test the motor, bypass faulty steering lock with high-quality emulator, and provide 6 months warranty.",
                "Can dispatch one of our diagnostic technicians to your location with original diagnostic equipment. Work guaranteed with platform escrow.",
            ],
        ];

        foreach ($requestsData as $reqIndex => $reqData) {
            $author = $users[$reqData['author_index'] % $users->count()];

            // Check author's request limit
            if ($userLimits[$author->id]['requests_created'] >= $userLimits[$author->id]['request_limit']) {
                continue;
            }

            $authorLocation = $author->locations()->first();

            $discussion = Discussion::updateOrCreate(
                [
                    'user_id' => $author->id,
                    'title' => $reqData['title'],
                ],
                [
                    'type' => $reqData['type'],
                    'category_id' => $reqData['category_id'],
                    'brand_id' => $reqData['brand_id'],
                    'model_id' => $reqData['model_id'],
                    'location_id' => $authorLocation?->id,
                    'budget' => $reqData['budget'],
                    'body' => $reqData['body'],
                    'status' => 'open',
                ]
            );

            $userLimits[$author->id]['requests_created']++;

            // Create responses from other users
            // Rules:
            // 1. A user should not respond to their own request.
            // 2. A user should not respond twice to the same request.
            // 3. User's responses must be below their subscription response_limit.
            $potentialResponders = $users->filter(fn($u) => $u->id !== $author->id)->shuffle();
            $responseTexts = $responsesPool[$reqIndex] ?? [];
            $textIndex = 0;

            foreach ($potentialResponders as $responder) {
                // Check if responder reached response_limit
                if ($userLimits[$responder->id]['responses_created'] >= $userLimits[$responder->id]['response_limit']) {
                    continue;
                }

                $replyText = $responseTexts[$textIndex % count($responseTexts)] ?? "I have this available and can supply with warranty. Let me know if you would like more details.";
                $textIndex++;

                // A user responds at most once to this discussion
                Response::updateOrCreate(
                    [
                        'discussion_id' => $discussion->id,
                        'user_id' => $responder->id,
                    ],
                    [
                        'body' => $replyText,
                        'status' => 'visible',
                    ]
                );

                $userLimits[$responder->id]['responses_created']++;
            }
        }
    }
}