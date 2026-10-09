<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Media;
use App\Models\Post;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $disk = 'public';
        Storage::disk($disk)->makeDirectory('media/demo/images');
        Storage::disk($disk)->makeDirectory('media/demo/videos');
        Storage::disk($disk)->makeDirectory('media/demo/documents');

        // =========================================================================
        // 1. ASSET PROVISIONING (Download with reliable fallback generation)
        // =========================================================================

        // (a) Image Assets
        $imageDefinitions = [
            'engine_tokunbo.jpg' => [
                'title' => 'Toyota 2GR-FE V6 Engine - Tested Foreign Used',
                'url' => 'https://picsum.photos/seed/engine/800/600.jpg',
                'bg_color' => [30, 41, 59],
            ],
            'gearbox_transmission.jpg' => [
                'title' => 'Toyota Camry U660E Automatic Transmission',
                'url' => 'https://picsum.photos/seed/gearbox/800/600.jpg',
                'bg_color' => [15, 23, 42],
            ],
            'laptop_motherboard.jpg' => [
                'title' => 'Dell Latitude 5420 Logic Board Core i5',
                'url' => 'https://picsum.photos/seed/circuit/800/600.jpg',
                'bg_color' => [6, 78, 59],
            ],
            'diagnostic_scanner.jpg' => [
                'title' => 'Autel MaxiSys Diagnostic OBD2 Scanner Output',
                'url' => 'https://picsum.photos/seed/diagnostic/800/600.jpg',
                'bg_color' => [30, 58, 138],
            ],
            'salvage_vehicle.jpg' => [
                'title' => 'Salvage Camry Donor Vehicle Body & Assembly',
                'url' => 'https://picsum.photos/seed/salvage/800/600.jpg',
                'bg_color' => [88, 28, 135],
            ],
        ];

        $storedImages = [];
        foreach ($imageDefinitions as $fileName => $meta) {
            $relativePath = "media/demo/images/{$fileName}";
            $this->ensureImageFile($disk, $relativePath, $meta['url'], $meta['title'], $meta['bg_color']);
            $storedImages[$fileName] = [
                'file_name' => $fileName,
                'file_path' => $relativePath,
                'mime_type' => 'image/jpeg',
                'media_type' => 'image',
                'width' => 800,
                'height' => 600,
                'size' => Storage::disk($disk)->size($relativePath),
            ];
        }

        // (b) Document Assets (PDFs)
        $documentDefinitions = [
            'engine_inspection_certificate.pdf' => [
                'title' => 'Parts & Parcel Technical Inspection Certificate',
                'doc_no' => 'INSP-2026-0892',
                'body' => 'Compression tested: Cylinder 1-4 (180, 178, 182, 179 PSI). Oil analysis clean, zero sludge, factory valve clearance verified.',
            ],
            'customs_clearing_document.pdf' => [
                'title' => 'Federal Customs Service Import Duty Declaration',
                'doc_no' => 'CUS-NG-774921',
                'body' => 'Direct foreign tokunbo container manifest clearance, duty paid and certified authentic. VIN match confirmed.',
            ],
            'autel_diagnostic_scan_report.pdf' => [
                'title' => 'Autel Professional OBD2 System Health Report',
                'doc_no' => 'SCAN-REPORT-4401',
                'body' => 'CAN-bus network healthy. ECU communicable, DTC P0300 cleared after bench testing and harness re-pinning.',
            ],
            'warranty_terms_and_policy.pdf' => [
                'title' => 'Parts & Parcel Platform Escrow Warranty Guarantee',
                'doc_no' => 'WAR-NG-2026',
                'body' => 'Full testing warranty coverage. Covers internal electronic and mechanical failure. Funds held in escrow until buyer release.',
            ],
            'shipping_delivery_waybill.pdf' => [
                'title' => 'Verified Logistics Doorstep Delivery Waybill',
                'doc_no' => 'WAYBILL-LOG-5519',
                'body' => 'Insured courier transit from Lagos Central Hub to recipient destination. Heavy crate packed and tamper-sealed.',
            ],
        ];

        $storedDocuments = [];
        foreach ($documentDefinitions as $fileName => $meta) {
            $relativePath = "media/demo/documents/{$fileName}";
            $this->ensurePdfFile($disk, $relativePath, $meta['title'], $meta['doc_no'], $meta['body']);
            $storedDocuments[$fileName] = [
                'file_name' => $fileName,
                'file_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'media_type' => 'document',
                'width' => null,
                'height' => null,
                'size' => Storage::disk($disk)->size($relativePath),
            ];
        }

        // (c) Video Assets (MP4)
        $videoDefinitions = [
            'engine_testing_run.mp4' => [
                'title' => 'Cold Start Engine Bench Test & RPM Verification Video',
                'url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            ],
            'gearbox_rotation_test.mp4' => [
                'title' => 'Automatic Transmission Fluid & Torque Converter Rotation Video',
                'url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            ],
            'board_hdmi_test.mp4' => [
                'title' => 'Motherboard External Display Boot Benchmark Clip',
                'url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            ],
        ];

        $storedVideos = [];
        foreach ($videoDefinitions as $fileName => $meta) {
            $relativePath = "media/demo/videos/{$fileName}";
            $this->ensureVideoFile($disk, $relativePath, $meta['url']);
            $storedVideos[$fileName] = [
                'file_name' => $fileName,
                'file_path' => $relativePath,
                'mime_type' => 'video/mp4',
                'media_type' => 'video',
                'width' => 640,
                'height' => 360,
                'size' => Storage::disk($disk)->size($relativePath),
            ];
        }

        // =========================================================================
        // 2. ATTACH MEDIA TO: POSTS
        // =========================================================================
        $posts = Post::all();
        if ($posts->isNotEmpty()) {
            $imgKeys = array_keys($storedImages);
            $docKeys = array_keys($storedDocuments);
            $vidKeys = array_keys($storedVideos);

            foreach ($posts as $index => $post) {
                // (a) Image
                $img = $storedImages[$imgKeys[$index % count($imgKeys)]];
                $this->createMediaRecord('post', $post->id, 'featured', 'Featured Tutorial Cover Image', $img, 1);

                // (b) Document PDF
                $doc = $storedDocuments[$docKeys[$index % count($docKeys)]];
                $this->createMediaRecord('post', $post->id, 'documents', 'Technical Bulletin & Troubleshooting Guide PDF', $doc, 2);

                // (c) Video
                $vid = $storedVideos[$vidKeys[$index % count($vidKeys)]];
                $this->createMediaRecord('post', $post->id, 'videos', 'Step-by-Step Diagnostic Walkthrough Video', $vid, 3);
            }
        }

        // =========================================================================
        // 3. ATTACH MEDIA TO: ITEMS
        // =========================================================================
        $items = Item::all();
        if ($items->isNotEmpty()) {
            foreach ($items->take(10) as $index => $item) {
                // (a) Gallery Photo
                $imgKey = array_keys($storedImages)[$index % count($storedImages)];
                $img = $storedImages[$imgKey];
                $this->createMediaRecord('item', $item->id, 'gallery', "{$item->name} High-Res Photo", $img, 1);

                // (b) Document PDF (Inspection / Customs certification)
                $docKey = ($index % 2 === 0) ? 'engine_inspection_certificate.pdf' : 'customs_clearing_document.pdf';
                $doc = $storedDocuments[$docKey];
                $this->createMediaRecord('item', $item->id, 'documents', 'Official Pre-Listing Inspection Certificate', $doc, 2);

                // (c) Operational test video
                $vidKey = ($index % 2 === 0) ? 'engine_testing_run.mp4' : 'gearbox_rotation_test.mp4';
                $vid = $storedVideos[$vidKey];
                $this->createMediaRecord('item', $item->id, 'videos', 'Component Operational Demonstration Clip', $vid, 3);
            }
        }

        // =========================================================================
        // 4. ATTACH MEDIA TO: DISCUSSIONS (COMMUNITY REQUESTS)
        // =========================================================================
        $discussions = Discussion::all();
        if ($discussions->isNotEmpty()) {
            foreach ($discussions as $index => $discussion) {
                // (a) Problem Image
                $imgKey = ($index % 2 === 0) ? 'engine_tokunbo.jpg' : 'diagnostic_scanner.jpg';
                $img = $storedImages[$imgKey];
                $this->createMediaRecord('discussion', $discussion->id, 'attachments', 'Fault Identification Reference Photo', $img, 1);

                // (b) Diagnostic Scan PDF
                $doc = $storedDocuments['autel_diagnostic_scan_report.pdf'];
                $this->createMediaRecord('discussion', $discussion->id, 'documents', 'OBD2 Diagnostic Scan Report PDF', $doc, 2);
            }
        }

        // =========================================================================
        // 5. ATTACH MEDIA TO: RESPONSES
        // =========================================================================
        $responses = Response::all();
        if ($responses->isNotEmpty()) {
            foreach ($responses->take(6) as $index => $response) {
                // (a) In-stock photo
                $imgKey = array_keys($storedImages)[($index + 2) % count($storedImages)];
                $img = $storedImages[$imgKey];
                $this->createMediaRecord('response', $response->id, 'attachments', 'Warehouse Part Stock Proof', $img, 1);

                // (b) Testing video
                $vidKey = array_keys($storedVideos)[$index % count($storedVideos)];
                $vid = $storedVideos[$vidKey];
                $this->createMediaRecord('response', $response->id, 'videos', 'Live Test Proof Video Before Dispatch', $vid, 2);

                // (c) Supplier warranty policy
                $doc = $storedDocuments['warranty_terms_and_policy.pdf'];
                $this->createMediaRecord('response', $response->id, 'documents', 'Seller Testing Warranty Policy PDF', $doc, 3);
            }
        }

        // =========================================================================
        // 6. ATTACH MEDIA TO: CONVERSATION_MESSAGES
        // (Create demo conversation and messages if none exist, then attach media)
        // =========================================================================
        $this->seedConversationMessagesWithMedia($storedImages, $storedDocuments, $storedVideos);
    }

    /**
     * Seeds realistic conversation messages and attaches media (image, video, pdf).
     */
    protected function seedConversationMessagesWithMedia(array $storedImages, array $storedDocuments, array $storedVideos): void
    {
        $users = User::whereNull('role_id')->get();
        if ($users->count() < 2) {
            return;
        }

        $buyer = $users[0];
        $seller = $users[1];

        $listing = Listing::with('item')->where('user_id', $seller->id)->first() ?? Listing::first();
        if (!$listing) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            [
                'contextable_type' => Listing::class,
                'contextable_id' => $listing->id,
                'created_by' => $buyer->id,
            ]
        );

        ConversationParticipant::firstOrCreate(
            ['conversation_id' => $conversation->id, 'user_id' => $buyer->id],
            ['joined_at' => now()->subDays(2)]
        );

        ConversationParticipant::firstOrCreate(
            ['conversation_id' => $conversation->id, 'user_id' => $seller->id],
            ['joined_at' => now()->subDays(2)]
        );

        $itemName = $listing->item?->name ?? 'Camry Gearbox';

        // Message 1: Buyer inquiries and attaches sample reference image
        $msg1 = ConversationMessage::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $buyer->id,
                'body' => "Hello! Confirming availability for {$itemName}. Can you verify whether the wiring harness connector pins match my reference photo?",
            ]
        );
        $this->createMediaRecord('conversation_message', $msg1->id, 'attachments', 'Buyer Pinout Verification Photo', $storedImages['diagnostic_scanner.jpg'], 1);

        // Message 2: Seller confirms with operational video and inspection photo
        $msg2 = ConversationMessage::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $seller->id,
                'body' => "Yes, it is 100% identical! Sharing the test-bench video showing fluid clarity and the close-up photo of the clean OEM serial label.",
            ]
        );
        $this->createMediaRecord('conversation_message', $msg2->id, 'attachments', 'Seller Test-Bench Video Proof', $storedVideos['gearbox_rotation_test.mp4'], 1);
        $this->createMediaRecord('conversation_message', $msg2->id, 'attachments', 'OEM Serial Plate Verification Photo', $storedImages['gearbox_transmission.jpg'], 2);

        // Message 3: Seller attaches official warranty PDF and customs clearing sheet
        $msg3 = ConversationMessage::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $seller->id,
                'body' => "Here is our signed platform warranty declaration and official customs duty clearing sheet for your peace of mind.",
            ]
        );
        $this->createMediaRecord('conversation_message', $msg3->id, 'documents', 'Platform Testing Warranty Guarantee PDF', $storedDocuments['warranty_terms_and_policy.pdf'], 1);

        // Message 4: Buyer accepts and attaches logistics delivery waybill instructions
        $msg4 = ConversationMessage::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $buyer->id,
                'body' => "Looks very good. I have funded platform escrow. Please find our delivery receipt and depot handling instructions attached.",
            ]
        );
        $this->createMediaRecord('conversation_message', $msg4->id, 'documents', 'Doorstep Delivery Waybill Slip PDF', $storedDocuments['shipping_delivery_waybill.pdf'], 1);
    }

    /**
     * Creates or updates a media table entry.
     */
    protected function createMediaRecord(
        string $mediableType,
        int $mediableId,
        string $collection,
        string $friendlyName,
        array $fileMeta,
        int $sortOrder = 0
    ): Media {
        return Media::updateOrCreate(
            [
                'mediable_type' => $mediableType,
                'mediable_id' => $mediableId,
                'file_name' => $fileMeta['file_name'],
            ],
            [
                'collection' => $collection,
                'name' => $friendlyName,
                'file_path' => $fileMeta['file_path'],
                'disk' => 'public',
                'mime_type' => $fileMeta['mime_type'],
                'media_type' => $fileMeta['media_type'],
                'size' => $fileMeta['size'] ?? 1024,
                'width' => $fileMeta['width'],
                'height' => $fileMeta['height'],
                'is_processed' => true,
                'sort_order' => $sortOrder,
                'custom_properties' => [
                    'source' => 'demo_media_seeder',
                    'original_name' => $fileMeta['file_name'],
                    'verified' => true,
                ],
            ]
        );
    }

    /**
     * Ensures an image exists in storage: downloads if possible, or generates via GD.
     */
    protected function ensureImageFile(string $disk, string $relativePath, string $url, string $title, array $bgColor): void
    {
        if (Storage::disk($disk)->exists($relativePath) && Storage::disk($disk)->size($relativePath) > 500) {
            return;
        }

        // Try downloading
        try {
            $res = Http::withoutVerifying()->timeout(5)->get($url);
            if ($res->successful() && strlen($res->body()) > 1000) {
                Storage::disk($disk)->put($relativePath, $res->body());
                return;
            }
        } catch (\Throwable) {
            // Fallback to local GD generation
        }

        // Generate clean branded JPEG via GD
        if (extension_loaded('gd')) {
            $im = imagecreatetruecolor(800, 600);
            $bg = imagecolorallocate($im, $bgColor[0], $bgColor[1], $bgColor[2]);
            imagefilledrectangle($im, 0, 0, 800, 600, $bg);

            // Border
            $borderColor = imagecolorallocate($im, 203, 213, 225);
            imagerectangle($im, 10, 10, 789, 589, $borderColor);

            // Text
            $textColor = imagecolorallocate($im, 255, 255, 255);
            $subTextColor = imagecolorallocate($im, 148, 163, 184);

            imagestring($im, 5, 40, 260, 'PARTS & PARCEL DEMO MEDIA', $textColor);
            imagestring($im, 4, 40, 300, substr($title, 0, 65), $subTextColor);
            imagestring($im, 3, 40, 330, 'Resolution: 800x600 | Format: JPEG | Verified OEM Spec', $subTextColor);

            ob_start();
            imagejpeg($im, null, 85);
            $binary = ob_get_clean();
            imagedestroy($im);

            Storage::disk($disk)->put($relativePath, $binary);
        } else {
            // Ultra-minimal 1x1 JPEG fallback
            $minimalJpeg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
            Storage::disk($disk)->put($relativePath, $minimalJpeg);
        }
    }

    /**
     * Ensures a valid PDF document exists in storage.
     */
    protected function ensurePdfFile(string $disk, string $relativePath, string $title, string $docNo, string $body): void
    {
        if (Storage::disk($disk)->exists($relativePath) && Storage::disk($disk)->size($relativePath) > 200) {
            return;
        }

        // Try downloading standard sample PDF if preferred
        try {
            $res = Http::withoutVerifying()->timeout(5)->get('https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf');
            if ($res->successful() && strlen($res->body()) > 500) {
                Storage::disk($disk)->put($relativePath, $res->body());
                return;
            }
        } catch (\Throwable) {
            // Generate clean valid PDF below
        }

        // Construct a compliant PDF-1.4 binary file
        $sanitizedTitle = addcslashes($title, '()\\');
        $sanitizedDocNo = addcslashes("Doc Ref: {$docNo} | Date: " . date('Y-m-d'), '()\\');
        $sanitizedBody = addcslashes($body, '()\\');

        $streamContent = "BT\n/F1 16 Tf\n50 720 Td\n({$sanitizedTitle}) Tj\n/F1 10 Tf\n0 -30 Td\n({$sanitizedDocNo}) Tj\n/F1 11 Tf\n0 -40 Td\n({$sanitizedBody}) Tj\n/F1 9 Tf\n0 -80 Td\n(Digitally generated for Parts and Parcel platform verified escrow records.) Tj\nET";
        $streamLen = strlen($streamContent);

        $pdf = "%PDF-1.4\n"
            . "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n"
            . "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n"
            . "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n"
            . "4 0 obj\n<< /Length {$streamLen} >>\nstream\n{$streamContent}\nendstream\nendobj\n"
            . "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n"
            . "xref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000244 00000 n \n0000000350 00000 n \n"
            . "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n450\n%%EOF\n";

        Storage::disk($disk)->put($relativePath, $pdf);
    }

    /**
     * Ensures an MP4 video exists in storage.
     */
    protected function ensureVideoFile(string $disk, string $relativePath, string $url): void
    {
        if (Storage::disk($disk)->exists($relativePath) && Storage::disk($disk)->size($relativePath) > 1000) {
            return;
        }

        // Try downloading sample MP4
        try {
            $res = Http::withoutVerifying()->timeout(8)->get($url);
            if ($res->successful() && strlen($res->body()) > 5000) {
                Storage::disk($disk)->put($relativePath, $res->body());
                return;
            }
        } catch (\Throwable) {
            // Fallback to valid minimal MP4 container
        }

        // Fallback minimal valid MP4 container with ftyp (isom/mp42) and moov/mdat boxes
        // Recognized by browsers and mime-type inspectors as video/mp4
        $ftypPayload = "isom\x00\x00\x02\x00isomiso2mp41";
        $ftypBox = pack('N', strlen($ftypPayload) + 8) . 'ftyp' . $ftypPayload;
        $mdatPayload = str_repeat("\x00\x01\x02\x03", 256);
        $mdatBox = pack('N', strlen($mdatPayload) + 8) . 'mdat' . $mdatPayload;
        $moovPayload = pack('N', 8) . 'free';
        $moovBox = pack('N', strlen($moovPayload) + 8) . 'moov' . $moovPayload;

        $minimalMp4 = $ftypBox . $mdatBox . $moovBox;
        Storage::disk($disk)->put($relativePath, $minimalMp4);
    }
}
