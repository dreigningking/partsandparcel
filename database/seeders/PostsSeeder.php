<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@partsandparcel.com')->first()
            ?? User::whereNotNull('role_id')->first()
            ?? User::first();

        if (!$author) {
            $this->call(DemoUsersSeeder::class);
            $author = User::first();
        }

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(CategoriesAndBrandsSeeder::class);
            $categories = Category::all();
        }

        $autoCategory = $categories->first(fn($c) => Str::contains(strtolower($c->name), ['auto', 'vehicle', 'car'])) ?? $categories->first();
        $techCategory = $categories->first(fn($c) => Str::contains(strtolower($c->name), ['tech', 'comput', 'laptop', 'phone', 'device'])) ?? $categories->last();

        $postsData = [
            [
                'title' => 'How to Inspect a Tokunbo Automatic Gearbox Before Purchase',
                'category_id' => $autoCategory?->id,
                'excerpt' => 'A comprehensive checklist for mechanics and car owners buying foreign-used automatic transmissions in Nigerian auto markets.',
                'tags' => 'guides, how tos, automotive, transmission, gearbox maintenance, tokunbo spares',
                'content' => <<<EOT
Buying a foreign-used (Tokunbo) automatic transmission requires keen attention to detail to avoid buying burnt or defective units. Here is our step-by-step verification guide:

1. **Fluid Color & Smell**: Pull the transmission dipstick or inspect the remaining fluid in the drain plug. Healthy ATF should be translucent pink or reddish-brown. If the oil is pitch black and smells strongly burnt, the clutch friction plates are scorched.
2. **Torque Converter Hub Inspection**: Check the input shaft splines and torque converter hub for scoring, metal shavings, or excessive play.
3. **Solenoid & Wiring Harness**: Ensure the electronic solenoid connectors are not cracked or waterlogged.
4. **Manual Rotation**: Turn the input shaft by hand in neutral; it should rotate smoothly without grinding or binding.
5. **Always Request Testing Warranty**: Never purchase a transmission without at least a 7 to 14 days testing warranty documented through platform escrow.
EOT
                ,
                'comments' => [
                    [
                        'name' => 'Chidi Okonkwo',
                        'email' => 'chidi.mech@gmail.com',
                        'comment' => 'Very practical checklist! The fluid smell test has saved me from bad gearboxes at Ladipo multiple times.',
                    ],
                    [
                        'name' => 'Engr. Babatunde',
                        'email' => 'babatunde.auto@yahoo.com',
                        'comment' => 'Do not forget to verify whether the torque converter matches the exact year code stamped on the bellhousing.',
                    ],
                ],
            ],
            [
                'title' => 'Complete Guide: Diagnosing ECU & ECM Brain Box Faults vs Sensor Failures',
                'category_id' => $autoCategory?->id,
                'excerpt' => 'Learn how to identify whether your car problem stems from a blown computer box (ECU) or simply a bad sensor or wiring ground.',
                'tags' => 'guides, how tos, ecu diagnostics, brain box, car electronics, auto repair tips',
                'content' => <<<EOT
Automobile computer boxes (ECUs/ECMs) are often wrongly blamed when the true culprit is a faulty sensor, damaged wiring harness, or bad chassis ground. Follow this structured diagnosis:

- **Check Power and Ground Feeds**: Always verify 12V BATT+, IGN+, and ground lines at the ECU connector pins with a multimeter or test light before condemning the computer.
- **5-Volt Reference Circuit**: Check whether the ECU produces a steady 5.0V reference signal to sensors (MAP, TPS, CMP). A shorted sensor can pull down the entire 5V bus.
- **Water Ingress & Corrosion**: Inspect ECU pins for greenish oxidation, especially in vehicles where the drain cowl near the windshield was clogged.
- **OBD2 Live Data Scans**: If the scan tool communicates and shows plausible live sensor readings, the processor is typically operational.
EOT
                ,
                'comments' => [
                    [
                        'name' => 'Femi Awolowo',
                        'email' => 'femi.diagnostics@gmail.com',
                        'comment' => 'Crucial advice on the 5V reference wire. A shorted throttle sensor once had a client buying an unnecessary ECU.',
                    ],
                ],
            ],
            [
                'title' => 'How to Safely Harvest and Test Laptop Motherboards from Donor Units',
                'category_id' => $techCategory?->id,
                'excerpt' => 'Step-by-step salvage procedures for reclaiming genuine logic boards and internal components from cracked-screen laptops.',
                'tags' => 'guides, how tos, laptop repair, motherboard salvage, hardware electronics, donor parts',
                'content' => <<<EOT
Accidentally dropped laptops with shattered display panels frequently contain 100% operational motherboards. Reclaiming them properly requires careful handling:

1. **ESD Precaution & Battery Disconnection**: Disconnect the internal battery before touching any connector, RAM slot, or flat flex cable (FFC).
2. **External Display Benchmark**: Connect the motherboard to an external monitor via HDMI or Type-C to confirm the system boots to BIOS and displays zero GPU artifacts.
3. **Thermal Paste Refresh**: Clean old crusty compound and apply fresh non-conductive thermal paste (e.g. Arctic MX-4) before reattaching cooling pipes.
4. **BIOS & Security Lock Check**: Verify that CompuTrace or Corporate BIOS supervisor passwords are removed before listing the board for resale.
EOT
                ,
                'comments' => [
                    [
                        'name' => 'Victor Tech',
                        'email' => 'victor.computerhub@gmail.com',
                        'comment' => 'Checking for BIOS supervisor locks is so important. Thank you for including that warning!',
                    ],
                    [
                        'name' => 'Samuel Dike',
                        'email' => 'sam.dike@outlook.com',
                        'comment' => 'Excellent walkthrough for technician yards parting out business laptops.',
                    ],
                ],
            ],
            [
                'title' => 'Inverter Storage Battery Care: Extending Lithium LiFePO4 & Gel Battery Lifespans',
                'category_id' => $techCategory?->id,
                'excerpt' => 'Essential maintenance practices to protect solar backup inverter battery banks in tropical climates.',
                'tags' => 'guides, how tos, solar inverter, battery maintenance, renewable energy, tips',
                'content' => <<<EOT
Inverter batteries represent the biggest investment in solar and backup power setups. Maximizing cycle life requires adhering to simple operational parameters:

- **Depth of Discharge (DoD)**: For Tubular/Gel lead-acid batteries, keep discharge depth under 50%. For Lithium Iron Phosphate (LiFePO4), keeping DoD around 80% yields 4,000+ cycles.
- **Operating Temperature**: High ambient temperatures accelerate battery degradation. Maintain good ventilation in the battery compartment.
- **Equalization & Terminal Tightening**: Tighten battery terminals every 6 months to prevent high-resistance hot spots and melt-downs.
- **Float Voltage Settings**: Ensure your inverter charger float and absorption cutoffs strictly match the manufacturer data sheet.
EOT
                ,
                'comments' => [
                    [
                        'name' => 'Musa Haruna',
                        'email' => 'musa.solar@gmail.com',
                        'comment' => 'Thermal ventilation makes all the difference in northern climates. Great tips!',
                    ],
                ],
            ],
        ];

        foreach ($postsData as $pData) {
            $slug = Str::slug($pData['title']);

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => $pData['category_id'],
                    'title' => $pData['title'],
                    'excerpt' => $pData['excerpt'],
                    'content' => $pData['content'],
                    'tags' => $pData['tags'],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(2, 20)),
                ]
            );

            foreach ($pData['comments'] as $cData) {
                PostComment::updateOrCreate(
                    [
                        'post_id' => $post->id,
                        'email' => $cData['email'],
                    ],
                    [
                        'name' => $cData['name'],
                        'comment' => $cData['comment'],
                    ]
                );
            }
        }
    }
}
