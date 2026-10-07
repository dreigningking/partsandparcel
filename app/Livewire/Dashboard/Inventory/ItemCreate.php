<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Setting;
use App\Models\State;
use App\Traits\HasMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dash')]
class ItemCreate extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1 Fields
    public string $item_type = 'whole'; // whole, part, scrap
    public ?int $category_id = null;
    public ?int $brand_id = null;
    public ?int $model_id = null;
    public string $name = '';
    public string $condition_status = 'new'; // new (Brand New), used (Used - Working), refurbished (Refurbished), faulty (Scrap)
    public string $condition_notes = '';
    public string $description = '';
    public array $photos = [];

    // Step 2 Fields (Harvesting Matrix for Abel)
    public bool $disassemble_mode = false;
    public bool $as_is_scrap = false; // For non-technician Abel sellers who list whole scrap unit as-is
    public array $harvested_components = [];

    // Step 3 Fields (Listing & Location & Warranty)
    public float $price = 0.00;
    public bool $is_negotiable = false;
    public int $quantity = 1;
    public ?int $location_id = null;
    public int $warranty_period_days = 0;
    public bool $is_warranty_negotiable = false;
    public string $warranty_terms = '';
    public bool $allow_shipping = false;
    public bool $include_whole_listing = true;
    public bool $include_component_listings = true;

    // Location Modal Properties
    public bool $showLocationModal = false;
    public string $newLocationLabel = '';
    public string $newLocationContactName = '';
    public string $newLocationPhone = '';
    public string $newLocationAddress = '';
    public string $newLocationCity = '';
    public ?int $newLocationStateId = null;

    public function mount()
    {
        $defaultLocation = Auth::user()?->locations()->where('is_default', true)->first() 
            ?? Auth::user()?->locations()->first();
        $this->location_id = $defaultLocation?->id;
    }

    #[On('location-created')]
    public function onLocationCreated($id, $label = null, $city = null)
    {
        $this->location_id = (int) $id;
        session()->flash('location_success', "Location '{$label}' saved and selected!");
    }

    public function openLocationModal()
    {
        $this->dispatch('open-location-modal');
    }

    public function closeLocationModal()
    {
        $this->showLocationModal = false;
    }

    public function createLocation()
    {
        $this->validate([
            'newLocationLabel' => 'required|string|max:100',
            'newLocationAddress' => 'required|string|max:255',
            'newLocationCity' => 'required|string|max:100',
            'newLocationStateId' => 'required|exists:states,id',
            'newLocationPhone' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $state = State::find($this->newLocationStateId);
        $isFirst = $user->locations()->count() === 0;

        $loc = Location::create([
            'user_id' => $user->id,
            'label' => $this->newLocationLabel,
            'contact_name' => $this->newLocationContactName ?: $user->name,
            'phone' => $this->newLocationPhone ?: $user->phone,
            'address_line_1' => $this->newLocationAddress,
            'city' => $this->newLocationCity,
            'state_id' => $state?->id,
            'country_id' => $state?->country_id ?? Country::where('is_default', true)->value('id'),
            'latitude' => $state?->latitude,
            'longitude' => $state?->longitude,
            'is_default' => $isFirst,
        ]);

        $this->location_id = $loc->id;
        $this->showLocationModal = false;
        $this->reset(['newLocationLabel', 'newLocationContactName', 'newLocationPhone', 'newLocationAddress', 'newLocationCity', 'newLocationStateId']);
        
        session()->flash('location_success', 'New location added and selected!');
    }

    public function updatedCategoryId()
    {
        $this->model_id = null;
    }

    public function updatedBrandId()
    {
        $this->model_id = null;
    }

    public function updatedItemType($value)
    {
        if ($value === 'scrap') {
            $this->disassemble_mode = true;
            $this->as_is_scrap = false;
            $this->condition_status = 'faulty';
        } else {
            $this->disassemble_mode = false;
            $this->as_is_scrap = false;
            if ($this->condition_status === 'faulty') {
                $this->condition_status = 'new';
            }
        }
    }

    public function updatedAsIsScrap($value)
    {
        if ($value) {
            $this->disassemble_mode = false;
        } else if ($this->item_type === 'scrap') {
            $this->disassemble_mode = true;
        }
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function goToStep2()
    {
        $this->validateStep1();
        $this->item_type = 'scrap';
        $this->condition_status = 'faulty';
        $this->disassemble_mode = true;
        $this->populateSuggestedComponents();
        $this->step = 2;
    }

    public function skipStep2ToStep3()
    {
        $this->validateStep1();
        if ($this->item_type === 'scrap') {
            $this->include_whole_listing = true;
        }
        $this->step = 3;
    }

    public function goToStep3()
    {
        if ($this->step === 1) {
            $this->validateStep1();
        }

        $validComponents = array_filter($this->harvested_components, fn($c) => !empty(trim($c['name'] ?? '')));
        if ($this->item_type === 'scrap' && ($this->as_is_scrap || !$this->disassemble_mode || empty($validComponents))) {
            $this->include_whole_listing = true;
        }

        $this->step = 3;
    }

    public function backStep()
    {
        if ($this->step === 3 && $this->item_type === 'scrap' && !$this->as_is_scrap) {
            $this->step = 2;
        } else {
            $this->step = 1;
        }
    }

    public function getSuggestedComponentChipsProperty(): array
    {
        // 1. Check historical child items matching category/brand/model
        $historyNames = Item::whereNotNull('parent_id')
            ->when($this->model_id, function ($q) {
                $q->where('model_id', $this->model_id);
            }, function ($q) {
                if ($this->category_id) {
                    $q->whereHas('deviceModel', fn ($m) => $m->where('category_id', $this->category_id));
                }
            })
            ->select('name')
            ->distinct()
            ->limit(10)
            ->pluck('name')
            ->filter()
            ->toArray();

        if (!empty($historyNames)) {
            return array_values($historyNames);
        }

        // 2. Fallbacks based on selected category or general electronics
        $categoryName = strtolower(Category::find($this->category_id)?->name ?? '');

        if (str_contains($categoryName, 'phone') || str_contains($categoryName, 'mobile')) {
            return ['Display Screen Assembly', 'Original Battery Pack', 'Motherboard / Logic Board', 'Charging Port Flex', 'Camera Module', 'Unidentified Internal Component'];
        }

        if (str_contains($categoryName, 'laptop') || str_contains($categoryName, 'computer')) {
            return ['Display Screen Assembly', 'Motherboard / Mainboard', 'Original Battery Pack', 'RAM Memory Module', 'Storage Drive (SSD/HDD)', 'Keyboard & Top Case', 'Unidentified Internal Component'];
        }

        if (str_contains($categoryName, 'appliances') || str_contains($categoryName, 'washing') || str_contains($categoryName, 'fridge')) {
            return ['Main Control Board / PCB', 'Electric Motor Unit', 'Water Drain Pump', 'Thermostat / Sensor Unit', 'Power Cable & Plug', 'Unidentified Internal Component'];
        }

        return ['Display Screen Assembly', 'Motherboard / Mainboard', 'Original Battery Pack', 'Power Supply Unit', 'Internal Sensor Module', 'Unidentified Internal Component'];
    }

    public function addSuggestedComponent(string $componentName)
    {
        $this->harvested_components[] = [
            'name' => $componentName,
            'condition_status' => 'Testing working',
            'notes' => '',
            'price' => 0,
            'is_negotiable' => false,
            'list_for_sale' => true,
            'warranty_period_days' => 7,
            'is_warranty_negotiable' => false,
            'warranty_terms' => '7 days testing warranty',
        ];
    }

    public function populateSuggestedComponents()
    {
        if (!empty($this->harvested_components)) {
            return;
        }

        $chips = $this->suggested_component_chips;
        foreach (array_slice($chips, 0, 4) as $name) {
            $this->addSuggestedComponent($name);
        }
    }

    public function addCustomComponentRow()
    {
        $this->harvested_components[] = [
            'name' => '',
            'condition_status' => 'Testing working',
            'notes' => '',
            'price' => 0,
            'is_negotiable' => false,
            'list_for_sale' => true,
            'warranty_period_days' => 0,
            'is_warranty_negotiable' => false,
            'warranty_terms' => '',
        ];
    }

    public function removeComponentRow($index)
    {
        unset($this->harvested_components[$index]);
        $this->harvested_components = array_values($this->harvested_components);
    }

    public function resolveModelId(): int
    {
        if ($this->model_id) {
            return $this->model_id;
        }

        $categoryId = $this->category_id ?? Category::first()?->id;
        $brandId = $this->brand_id;

        if ($categoryId) {
            $existing = DeviceModel::where('category_id', $categoryId)
                ->when($brandId, fn($q) => $q->where('brand_id', $brandId))
                ->first();

            if ($existing) {
                return $existing->id;
            }
        }

        $fallback = DeviceModel::first();
        if ($fallback) {
            return $fallback->id;
        }

        $defaultCat = Category::firstOrCreate(['name' => 'General Electronics'], ['slug' => 'general-electronics']);
        $newModel = DeviceModel::create([
            'category_id' => $defaultCat->id,
            'name' => 'General / Unspecified Model',
            'slug' => 'general-unspecified-model-' . time(),
        ]);

        return $newModel->id;
    }

    public function validateStep1()
    {
        if ($this->item_type === 'scrap') {
            $this->condition_status = 'faulty';
        }

        $maxImageKb = HasMedia::getMaxMediaSizeKb('image');
        $maxVideoKb = HasMedia::getMaxMediaSizeKb('video');
        $maxFileKb = max(1024, max($maxImageKb, $maxVideoKb));

        $this->validate([
            'name' => ['required', 'string', 'max:255', new \App\Rules\ProhibitedWordsRule],
            'description' => ['nullable', 'string', new \App\Rules\ProhibitedWordsRule],
            'item_type' => 'required|in:whole,part,scrap',
            'condition_status' => 'required|in:new,used,refurbished,faulty',
            'location_id' => 'required|exists:locations,id',
            'model_id' => 'nullable|exists:models,id',
            'photos.*' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm',
                "max:{$maxFileKb}",
            ],
        ], [
            'photos.*.max' => 'Uploaded media files must not exceed ' . round($maxFileKb / 1024) . 'MB each based on platform media settings.',
        ]);
    }

    protected function attachUploadedMedia(Item $item)
    {
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    $item->attachMedia($photo);
                }
            }
        }
    }

    public function saveUnlisted()
    {
        $this->validateStep1();

        $item = Item::create([
            'user_id' => Auth::id(),
            'location_id' => $this->location_id,
            'model_id' => $this->resolveModelId(),
            'item_type' => $this->item_type,
            'name' => $this->name,
            'condition_status' => $this->condition_status,
            'condition_notes' => $this->condition_notes,
            'description' => $this->description,
        ]);

        $this->attachUploadedMedia($item);

        if ($this->item_type === 'scrap' && !$this->as_is_scrap && $this->disassemble_mode && !empty($this->harvested_components)) {
            foreach ($this->harvested_components as $compData) {
                if (!empty($compData['name'])) {
                    Item::create([
                        'user_id' => Auth::id(),
                        'parent_id' => $item->id,
                        'location_id' => $this->location_id,
                        'model_id' => $item->model_id,
                        'item_type' => 'part',
                        'name' => $compData['name'],
                        'condition_status' => $compData['condition_status'] ?? 'Testing working',
                        'condition_notes' => $compData['notes'] ?? null
                    ]);
                }
            }
        }

        session()->flash('message', 'Item registered successfully in unlisted inventory!');
        return redirect()->route('myitems');
    }

    public function submitListing()
    {
        $this->validateStep1();

        $validComponents = array_filter($this->harvested_components, fn($c) => !empty(trim($c['name'] ?? '')));
        $hasComponents = $this->item_type === 'scrap' && !$this->as_is_scrap && $this->disassemble_mode && !empty($validComponents);
        if (!$hasComponents && $this->item_type === 'scrap') {
            $this->include_whole_listing = true;
        }

        if ($this->item_type === 'scrap') {
            $this->quantity = 1;
        } else {
            $this->validate(['quantity' => 'required|integer|min:1']);
        }

        $this->validate([
            'price' => 'required|numeric|min:0',
        ]);

        // 1. Create Physical Item Asset
        $item = Item::create([
            'user_id' => Auth::id(),
            'location_id' => $this->location_id,
            'model_id' => $this->resolveModelId(),
            'item_type' => $this->item_type,
            'name' => $this->name,
            'condition_status' => $this->condition_status,
            'condition_notes' => $this->condition_notes,
            'description' => $this->description,
            'year' => now(),
        ]);

        $this->attachUploadedMedia($item);

        // 2. Publish Main Item Listing if requested (images are retrieved dynamically from Item via Media getter)
        if ($this->include_whole_listing) {
            $listing = Listing::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'is_negotiable' => $this->is_negotiable,
                'is_published' => true,
                'warranty_period_days' => $this->warranty_period_days,
                'is_warranty_negotiable' => $this->is_warranty_negotiable,
                'warranty_terms' => $this->warranty_terms,
                'allow_shipping' => $this->allow_shipping,
            ]);
        }

        // 3. Create Harvested Components & Component Listings if disassemble mode and not as_is_scrap
        if ($this->item_type === 'scrap' && !$this->as_is_scrap && $this->disassemble_mode && !empty($this->harvested_components)) {
            foreach ($this->harvested_components as $compData) {
                if (!empty($compData['name'])) {
                    $childItem = Item::create([
                        'user_id' => Auth::id(),
                        'parent_id' => $item->id,
                        'location_id' => $this->location_id,
                        'model_id' => $item->model_id,
                        'item_type' => 'part',
                        'name' => $compData['name'],
                        'condition_status' => $compData['condition_status'] ?? 'Testing working',
                        'condition_notes' => $compData['notes'] ?? null,
                        'year' => now(),
                    ]);

                    $listThisComp = $this->include_component_listings && !empty($compData['list_for_sale']);
                    if ($listThisComp && isset($compData['price']) && $compData['price'] > 0) {
                        Listing::create([
                            'user_id' => Auth::id(),
                            'item_id' => $childItem->id,
                            'quantity' => 1,
                            'price' => $compData['price'],
                            'is_negotiable' => !empty($compData['is_negotiable']),
                            'is_published' => true,
                            'warranty_period_days' => $compData['warranty_period_days'] ?? $this->warranty_period_days,
                            'is_warranty_negotiable' => !empty($compData['is_warranty_negotiable']),
                            'warranty_terms' => $compData['warranty_terms'] ?? $this->warranty_terms,
                            'allow_shipping' => $this->allow_shipping,
                        ]);
                    }
                }
            }
        }

        session()->flash('message', 'Item & Marketplace Listings published successfully!');
        return redirect()->route('myitems');
    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::orderBy('name')->get();
        $models = DeviceModel::when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->brand_id, fn($q) => $q->where('brand_id', $this->brand_id))
            ->orderBy('name')
            ->get();

        $locations = Auth::user()?->locations()->with(['state', 'country'])->get() ?? collect();
        $states = State::where('is_active', true)->orderBy('name')->get();
        $mediaSettings = HasMedia::getMediaDimensionSettings();

        return view('livewire.dashboard.inventory.item-create', [
            'categories' => $categories,
            'brands' => $brands,
            'models' => $models,
            'locations' => $locations,
            'states' => $states,
            'maxImageWidth' => $mediaSettings['width'],
            'maxImageHeight' => $mediaSettings['height'],
            'maxImageMb' => $mediaSettings['max_image_size_mb'],
            'maxVideoMb' => $mediaSettings['max_video_size_mb'],
        ]);
    }
}
