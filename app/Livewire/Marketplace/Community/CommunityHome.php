<?php

namespace App\Livewire\Marketplace\Community;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class CommunityHome extends Component
{
    public $search = '';
    public $tab = 'all';
    public $sort = 'relevance';
    public $page = 1;
    public $perPage = 8;

    // Filter values from CommunityFilter component
    public $category = '';
    public $type = '';
    public $location = '';
    public $budgetMin = null;
    public $budgetMax = null;
    public $status = ['open'];

    // UI Toggles
    public $showPostModal = false;
    public $showMobileDrawer = false;

    // Form fields corresponding to Discussion model & migration
    public $formType = 'item'; // 'item', 'service', 'advice', 'delivery' or 'Product / Part'
    public $formCategory = ''; // category_id or category name
    public $formBrand = '';    // brand_id or brand name
    public $formModel = '';    // model_id or model name
    public $formLocation = ''; // location_id or location string
    public $formBudget = '';   // budget string
    public $formTitle = '';    // title
    public $formDesc = '';     // body

    public $postSuccessMessage = false;

    public function updatedFormCategory($value)
    {
        // Reset model selection when category changes
        $this->formModel = '';
    }

    public function getRequestsData()
    {
        $dbDiscussions = Discussion::with(['user.primaryLocation', 'category', 'brand', 'deviceModel', 'location', 'responses', 'offers'])
            ->inCurrentCountry()
            ->latest()
            ->get();

        $items = [];
        foreach ($dbDiscussions as $d) {
            $typeLabel = match ($d->type) {
                'service' => 'Repair / Service',
                'delivery' => 'Delivery / Logistics',
                'advice' => 'Question / Advice',
                default => 'Product / Part',
            };

            $loc = $d->location
                ? "{$d->location->city}, {$d->location->state}"
                : ($d->user?->primaryLocation?->city
                    ? "{$d->user->primaryLocation->city}, {$d->user->primaryLocation->state}"
                    : ($d->attachments['location'] ?? 'Lagos, Nigeria'));

            $budget = $d->budget ?: ($d->attachments['budget'] ?? 'Flexible');

            $items[] = [
                'id' => $d->id,
                'name' => $d->user?->name ?? 'Community Member',
                'location' => $loc,
                'time' => $d->created_at->diffForHumans(),
                'title' => $d->title,
                'desc' => $d->body,
                'type' => $typeLabel,
                'category' => $d->category?->name ?? 'General',
                'brand' => $d->brand?->name ?? '',
                'model' => $d->deviceModel?->name ?? '',
                'offers' => $d->offers->count(),
                'budget' => $budget,
                'status' => $d->status,
            ];
        }

        // Merge with sample requests if database has few
        $defaults = [
            [
                'id' => 1001,
                'name' => 'TechSam',
                'location' => 'Computer Village, Ikeja',
                'time' => '2 hours ago',
                'title' => 'Looking for HP EliteBook 840 G5 motherboard in Lagos',
                'desc' => 'Need a clean, tested board without GPU issues. Willing to pick up at Computer Village today. Instant payment guaranteed.',
                'type' => 'Product / Part',
                'category' => 'Electronics',
                'brand' => 'HP',
                'model' => 'EliteBook 840 G5',
                'offers' => 3,
                'budget' => '₦70,000 – ₦90,000',
                'status' => 'open'
            ],
            [
                'id' => 1002,
                'name' => 'AbujaAutoFix',
                'location' => 'Wuse Zone 4, Abuja',
                'time' => '5 hours ago',
                'title' => 'Where can I get 2015 Toyota Corolla ECU brain box in Abuja?',
                'desc' => 'Looking for a working ECU for a 2015 Toyota Corolla. Must be tested and compatible. Budget is flexible.',
                'type' => 'Product / Part',
                'category' => 'Vehicles',
                'brand' => 'Toyota',
                'model' => 'Corolla 2015',
                'offers' => 7,
                'budget' => '₦120,000',
                'status' => 'offers'
            ],
            [
                'id' => 1003,
                'name' => 'GenTechNG',
                'location' => 'Ikeja, Lagos',
                'time' => '1 day ago',
                'title' => 'Need someone to repair a 15KVA generator in Ikeja',
                'desc' => 'My 15KVA generator keeps cutting off after 10 mins of use. Need a reliable technician who can diagnose and fix on-site.',
                'type' => 'Repair / Service',
                'category' => 'Equipment',
                'brand' => 'Mikano',
                'model' => '15KVA Perkins',
                'offers' => 5,
                'budget' => '₦25,000 – ₦40,000',
                'status' => 'open'
            ]
        ];

        return array_merge($items, $defaults);
    }

    #[On('filters-updated')]
    public function handleFiltersUpdated($filters)
    {
        $this->category = $filters['category'] ?? '';
        $this->type = $filters['type'] ?? '';
        $this->location = $filters['location'] ?? '';
        $this->budgetMin = $filters['budgetMin'] ?? null;
        $this->budgetMax = $filters['budgetMax'] ?? null;
        $this->status = $filters['status'] ?? ['open'];
        $this->page = 1;
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function updatedSort()
    {
        $this->page = 1;
    }

    public function updatedSearch()
    {
        $this->page = 1;
    }

    public function setPage($page)
    {
        $this->page = max(1, (int)$page);
    }

    public function openPostModal()
    {
        $this->showPostModal = true;
        $this->postSuccessMessage = false;
    }

    public function closePostModal()
    {
        $this->showPostModal = false;
        $this->postSuccessMessage = false;
    }

    public function toggleMobileDrawer()
    {
        $this->showMobileDrawer = !$this->showMobileDrawer;
    }

    public function closeMobileDrawer()
    {
        $this->showMobileDrawer = false;
    }

    public function submitRequest()
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Please sign in to publish a community request.');
            return redirect()->route('login');
        }

        $this->validate([
            'formType' => 'required',
            'formTitle' => 'required|min:5',
            'formDesc' => 'required|min:10',
        ]);

        $user = Auth::user();

        // Resolve enum type
        $typeEnum = match ($this->formType) {
            'Repair / Service', 'service' => 'service',
            'Delivery / Logistics', 'delivery' => 'delivery',
            'Question / Advice', 'advice' => 'advice',
            default => 'item',
        };

        // Resolve category_id (supports both numeric ID and name string)
        $catId = null;
        if (is_numeric($this->formCategory)) {
            $catId = (int) $this->formCategory;
        } elseif (!empty($this->formCategory)) {
            $catId = Category::where('name', 'like', "%{$this->formCategory}%")->value('id');
        }

        // Resolve brand_id
        $brandId = null;
        if (is_numeric($this->formBrand)) {
            $brandId = (int) $this->formBrand;
        } elseif (!empty($this->formBrand)) {
            $brandId = Brand::where('name', 'like', "%{$this->formBrand}%")->value('id');
        }

        // Resolve model_id
        $modelId = null;
        if (is_numeric($this->formModel)) {
            $modelId = (int) $this->formModel;
        } elseif (!empty($this->formModel)) {
            $modelId = DeviceModel::where('name', 'like', "%{$this->formModel}%")->value('id');
        }

        // Resolve location_id
        $locId = null;
        $locationStr = $this->formLocation;
        if (is_numeric($this->formLocation)) {
            $locId = (int) $this->formLocation;
            $locObj = Location::find($locId);
            if ($locObj) {
                $locationStr = "{$locObj->city}, {$locObj->state}";
            }
        }

        $discussion = Discussion::create([
            'user_id' => $user->id,
            'type' => $typeEnum,
            'category_id' => $catId,
            'brand_id' => $brandId,
            'model_id' => $modelId,
            'location_id' => $locId,
            'budget' => $this->formBudget ?: null,
            'title' => $this->formTitle,
            'body' => $this->formDesc,
            'attachments' => [
                'location' => $locationStr,
                'budget' => $this->formBudget ?: 'Flexible',
            ],
            'status' => 'open',
        ]);

        $this->postSuccessMessage = true;
        $this->reset(['formType', 'formCategory', 'formBrand', 'formModel', 'formLocation', 'formBudget', 'formTitle', 'formDesc']);
        session()->flash('message', "Your request #REQ-{$discussion->id} has been posted to the Community Hub!");
    }

    public function getFilteredRequests()
    {
        $all = $this->getRequestsData();
        $filtered = array_filter($all, function ($r) {
            // Category filter
            if (!empty($this->category) && $r['category'] !== $this->category) {
                return false;
            }
            // Type filter
            if (!empty($this->type) && $r['type'] !== $this->type) {
                return false;
            }
            // Location filter
            if (!empty($this->location) && strpos($r['location'], $this->location) === false) {
                return false;
            }
            // Tab filter
            if ($this->tab === 'products' && $r['type'] !== 'Product / Part') return false;
            if ($this->tab === 'repairs' && $r['type'] !== 'Repair / Service') return false;
            if ($this->tab === 'delivery' && $r['type'] !== 'Delivery / Logistics') return false;
            if ($this->tab === 'questions' && $r['type'] !== 'Question / Advice') return false;

            // Search filter
            if (!empty($this->search)) {
                $term = strtolower(trim($this->search));
                $haystack = strtolower($r['title'] . ' ' . $r['desc'] . ' ' . $r['name'] . ' ' . $r['location']);
                if (strpos($haystack, $term) === false) {
                    return false;
                }
            }

            // Status filter
            if (!empty($this->status) && !in_array($r['status'], $this->status)) {
                return false;
            }

            // Budget range filter
            $minVal = (float)($this->budgetMin ?? 0);
            $maxVal = $this->budgetMax !== null && $this->budgetMax !== '' ? (float)$this->budgetMax : INF;

            if (!empty($r['budget'])) {
                preg_match_all('/[\d,]+/', $r['budget'], $matches);
                if (!empty($matches[0])) {
                    $vals = array_map(fn($n) => (float)str_replace(',', '', $n), $matches[0]);
                    $low = $vals[0] ?? 0;
                    $high = $vals[1] ?? $low;
                    if ($high < $minVal || $low > $maxVal) return false;
                }
            } elseif ($minVal > 0 || $maxVal < INF) {
                return false;
            }

            return true;
        });

        // Sorting
        usort($filtered, function ($a, $b) {
            if ($this->sort === 'newest') {
                return $b['id'] <=> $a['id'];
            } elseif ($this->sort === 'offers') {
                return $b['offers'] <=> $a['offers'];
            } elseif ($this->sort === 'budget') {
                $getHigh = function ($s) {
                    if (!$s) return 0;
                    preg_match_all('/[\d,]+/', $s, $m);
                    if (empty($m[0])) return 0;
                    $v = array_map(fn($n) => (float)str_replace(',', '', $n), $m[0]);
                    return $v[1] ?? $v[0] ?? 0;
                };
                return $getHigh($b['budget']) <=> $getHigh($a['budget']);
            }
            return 0; // relevance / default
        });

        return array_values($filtered);
    }

    public function render()
    {
        $filtered = $this->getFilteredRequests();
        $total = count($filtered);
        $totalPages = (int)ceil($total / $this->perPage) ?: 1;
        $offset = ($this->page - 1) * $this->perPage;
        $paginated = array_slice($filtered, $offset, $this->perPage);

        // Fetch database collections for modal dropdown options
        $allCategories = Category::orderBy('name')->get();
        $allBrands = Brand::orderBy('name')->get();
        $allModels = is_numeric($this->formCategory) && $this->formCategory
            ? DeviceModel::where('category_id', $this->formCategory)->orderBy('name')->get()
            : DeviceModel::orderBy('name')->take(100)->get();

        $allLocations = Auth::check()
            ? Location::where('user_id', Auth::id())->orderBy('is_primary', 'desc')->get()
            : Location::orderBy('city')->get();

        return view('livewire.marketplace.community.community-home', [
            'requests' => $paginated,
            'totalRequests' => $total,
            'totalPages' => $totalPages,
            'startDisplay' => $total === 0 ? 0 : $offset + 1,
            'endDisplay' => min($offset + $this->perPage, $total),
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'allModels' => $allModels,
            'allLocations' => $allLocations,
        ]);
    }
}
