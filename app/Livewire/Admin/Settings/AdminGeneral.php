<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Platform General Settings — Admin Control Center')]
class AdminGeneral extends Component
{
    // Active Segment filter ('marketplace', 'media', 'promotions', 'timelines', or 'all')
    public string $activeSegment = 'marketplace';

    // Search query for settings
    public string $search = '';

    /**
     * Stored values bound to the input elements:
     * Keyed by setting name => value
     * - boolean: '1' or '0'
     * - integer: int
     * - array: array of strings
     * - string: string
     *
     * @var array<string, mixed>
     */
    public array $settings = [];

    /**
     * Buffer for new tag input keyed by setting name:
     * $newTag[$name] = 'string'
     *
     * @var array<string, string>
     */
    public array $newTag = [];

    public function mount(): void
    {
        if (Setting::count() === 0) {
            (new SettingsSeeder())->run();
        }

        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $allSettings = Setting::orderBy('segment')->orderBy('id')->get();

        foreach ($allSettings as $setting) {
            $val = Setting::getValue($setting->name);

            if ($setting->type === 'boolean') {
                $this->settings[$setting->name] = ($val === true || $val === '1' || $val === 1) ? '1' : '0';
            } elseif ($setting->type === 'array') {
                $this->settings[$setting->name] = is_array($val) ? array_values($val) : [];
                $this->newTag[$setting->name] = '';
            } elseif ($setting->type === 'integer') {
                $this->settings[$setting->name] = (int) ($val ?? 0);
            } else {
                $this->settings[$setting->name] = (string) ($val ?? '');
            }
        }
    }

    public function setSegment(string $segment): void
    {
        $this->activeSegment = $segment;
    }

    /**
     * Add a tag to an array setting
     */
    public function addTag(string $name): void
    {
        $tag = trim($this->newTag[$name] ?? '');
        if ($tag === '') {
            return;
        }

        $current = (array) ($this->settings[$name] ?? []);

        // Avoid duplicate tags
        if (! in_array($tag, $current, true)) {
            $current[] = $tag;
            $this->settings[$name] = array_values($current);
        }

        $this->newTag[$name] = '';
    }

    /**
     * Remove a tag from an array setting by index
     */
    public function removeTag(string $name, int $index): void
    {
        if (isset($this->settings[$name][$index])) {
            unset($this->settings[$name][$index]);
            $this->settings[$name] = array_values($this->settings[$name]);
        }
    }

    /**
     * Save all settings to the Setting model in the database
     */
    public function saveSettings(): void
    {
        $allDefinitions = Setting::all();

        foreach ($allDefinitions as $settingModel) {
            $name = $settingModel->name;
            if (! array_key_exists($name, $this->settings)) {
                continue;
            }

            $rawVal = $this->settings[$name];
            $type = $settingModel->type;
            $segment = $settingModel->segment;

            $storedValue = match ($type) {
                'boolean' => ($rawVal === '1' || $rawVal === 1 || $rawVal === true || $rawVal === 'true') ? '1' : '0',
                'array', 'json' => json_encode(array_values((array) $rawVal), JSON_UNESCAPED_SLASHES),
                'integer' => (string) (int) $rawVal,
                default => (string) $rawVal,
            };

            $settingModel->update([
                'value' => $storedValue,
            ]);
        }

        session()->flash('status', 'Platform settings saved successfully.');
    }

    /**
     * Reset settings back to default seed values
     */
    public function resetToDefaults(): void
    {
        (new SettingsSeeder())->run();
        $this->loadSettings();
        session()->flash('status', 'Platform settings restored to system defaults.');
    }

    /**
     * Metadata definitions for known platform settings
     *
     * @return array<string, array{label: string, description: string, unit?: string, icon?: string}>
     */
    public function allSettingMetadata(): array
    {
        return [
            // Marketplace
            'auto_approve_listings' => [
                'label' => 'Auto-Approve Listings',
                'description' => 'When enabled, newly created items and listings are published immediately without requiring admin moderation.',
                'icon' => 'fas fa-check-double',
            ],
            'abandoned_cart_reaction_hours' => [
                'label' => 'Abandoned Cart Reaction Time',
                'description' => 'Hours of cart inactivity before the platform triggers automated notifications or reminder actions.',
                'unit' => 'hours',
                'icon' => 'fas fa-shopping-cart',
            ],
            'prohibited_words' => [
                'label' => 'Prohibited Content Words',
                'description' => 'Comma-separated keywords or phrases that trigger automatic moderation flags in titles and descriptions.',
                'icon' => 'fas fa-ban',
            ],
            'gateways' => [
                'label' => 'Supported Payment Gateways',
                'description' => 'Active payment providers enabled for direct checkout, platform escrow, and wallet operations.',
                'icon' => 'fas fa-credit-card',
            ],

            // Media
            'max_media_image_size' => [
                'label' => 'Max Image Upload Size',
                'description' => 'Maximum file size allowed for listing photos, component gallery images, and profile avatars.',
                'unit' => 'MB',
                'icon' => 'fas fa-image',
            ],
            'max_media_image_width' => [
                'label' => 'Max Image Resolution Width',
                'description' => 'Maximum width (in pixels) for uploaded images before server-side optimization.',
                'unit' => 'px',
                'icon' => 'fas fa-arrows-alt-h',
            ],
            'max_media_image_height' => [
                'label' => 'Max Image Resolution Height',
                'description' => 'Maximum height (in pixels) for uploaded images before server-side optimization.',
                'unit' => 'px',
                'icon' => 'fas fa-arrows-alt-v',
            ],
            'max_media_video_size' => [
                'label' => 'Max Video Upload Size',
                'description' => 'Maximum allowable file size for video demonstrations and inspection footage.',
                'unit' => 'MB',
                'icon' => 'fas fa-video',
            ],
            'max_media_document_size' => [
                'label' => 'Max Document Upload Size',
                'description' => 'Maximum allowable file size for PDF manuals, diagnostic reports, and invoices.',
                'unit' => 'MB',
                'icon' => 'fas fa-file-pdf',
            ],

            // Promotions
            'auto_approve_promotions' => [
                'label' => 'Auto-Approve Promotions',
                'description' => 'When enabled, promotional campaigns are automatically approved and launched once payment succeeds.',
                'icon' => 'fas fa-bullhorn',
            ],
            'minimum_promotion_clicks' => [
                'label' => 'Minimum Promotion Clicks',
                'description' => 'Minimum number of clicks required when ordering a Pay-Per-Click promotion plan.',
                'unit' => 'clicks',
                'icon' => 'fas fa-mouse-pointer',
            ],
            'minimum_promotion_views' => [
                'label' => 'Minimum Promotion Views',
                'description' => 'Minimum number of impressions required when purchasing a Pay-Per-View promotion campaign.',
                'unit' => 'views',
                'icon' => 'fas fa-eye',
            ],

            // Timelines
            'order_processing_to_cancel_hours' => [
                'label' => 'Buyer Cancellation Grace Period',
                'description' => 'Hours after checkout during which a buyer can cancel an order before vendor dispatch commences.',
                'unit' => 'hours',
                'icon' => 'fas fa-times-circle',
            ],
            'order_processing_to_idle_cancel_hours' => [
                'label' => 'Vendor Inactivity Auto-Cancel Window',
                'description' => 'Hours allowed for a vendor to acknowledge/process an order before the platform auto-cancels and refunds.',
                'unit' => 'hours',
                'icon' => 'fas fa-user-clock',
            ],
            'order_pickup_allowance_hours' => [
                'label' => 'Local Pickup Allowance Window',
                'description' => 'Hours permitted for local pickup orders to be collected before auto-expiry.',
                'unit' => 'hours',
                'icon' => 'fas fa-box-open',
            ],
            'order_processing_to_delivery_hours' => [
                'label' => 'Fulfillment to Delivery Window',
                'description' => 'Estimated total hours allocated from vendor dispatch until order delivery completion.',
                'unit' => 'hours',
                'icon' => 'fas fa-shipping-fast',
            ],
            'order_delivered_to_auto_reception_hours' => [
                'label' => 'Delivered to Auto-Reception Window',
                'description' => 'Hours after delivery confirmation before the system automatically marks the package as received.',
                'unit' => 'hours',
                'icon' => 'fas fa-receipt',
            ],
            'order_received_to_auto_acceptance_hours' => [
                'label' => 'Inspection & Auto-Acceptance Window',
                'description' => 'Inspection window after package receipt before order is auto-accepted and payout is queued.',
                'unit' => 'hours',
                'icon' => 'fas fa-handshake',
            ],
            'order_rejected_to_returned_hours' => [
                'label' => 'Return Transit Allowance Window',
                'description' => 'Hours allocated for returning a rejected item back to the seller upon dispute resolution.',
                'unit' => 'hours',
                'icon' => 'fas fa-undo-alt',
            ],
            'order_returned_to_auto_acceptance_hours' => [
                'label' => 'Returned Package Auto-Acceptance Window',
                'description' => 'Hours allowed for a vendor to inspect returned items before the return is finalized.',
                'unit' => 'hours',
                'icon' => 'fas fa-clipboard-check',
            ],
        ];
    }

    public function getSettingMeta(string $name): array
    {
        $all = $this->allSettingMetadata();

        return $all[$name] ?? [
            'label' => Str::headline($name),
            'description' => 'Configure platform setting value for ' . Str::headline($name) . '.',
            'icon' => 'fas fa-cog',
        ];
    }

    #[Computed]
    public function segmentCounts(): array
    {
        $counts = Setting::selectRaw('segment, count(*) as count')
            ->groupBy('segment')
            ->pluck('count', 'segment')
            ->toArray();

        $counts['all'] = Setting::count();

        return $counts;
    }

    #[Computed]
    public function filteredSettings()
    {
        $query = Setting::query();

        if ($this->activeSegment !== 'all') {
            $query->where('segment', $this->activeSegment);
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('segment', 'like', $searchTerm);
            });
        }

        return $query->orderBy('segment')->orderBy('id')->get();
    }

    public function render(): View
    {
        return view('livewire.admin.settings.admin-general', [
            'settingsList' => $this->filteredSettings(),
            'counts' => $this->segmentCounts(),
            'activeSegment' => $this->activeSegment,
            'search' => $this->search,
            'settings' => $this->settings,
            'newTag' => $this->newTag,
            'metadata' => $this->allSettingMetadata(),
        ]);
    }
}
