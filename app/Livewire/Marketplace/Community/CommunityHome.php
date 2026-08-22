<?php

namespace App\Livewire\Marketplace\Community;

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

    // Form fields
    public $formType = '';
    public $formCategory = '';
    public $formTitle = '';
    public $formDesc = '';
    public $formLocation = '';
    public $formBudget = '';
    public $formResponse = 'Any';
    public $postSuccessMessage = false;

    public function getRequestsData()
    {
        return [
            [
                'id' => 1,
                'name' => 'TechSam',
                'location' => 'Computer Village, Ikeja',
                'time' => '2 hours ago',
                'title' => 'Looking for HP EliteBook 840 G5 motherboard in Lagos',
                'desc' => 'Need a clean, tested board without GPU issues. Willing to pick up at Computer Village today. Instant payment guaranteed.',
                'type' => 'Product / Part',
                'category' => 'Electronics',
                'offers' => 3,
                'budget' => '₦70,000 – ₦90,000',
                'status' => 'open'
            ],
            [
                'id' => 2,
                'name' => 'AbujaAutoFix',
                'location' => 'Wuse Zone 4, Abuja',
                'time' => '5 hours ago',
                'title' => 'Where can I get 2015 Toyota Corolla ECU brain box in Abuja?',
                'desc' => 'Looking for a working ECU for a 2015 Toyota Corolla. Must be tested and compatible. Budget is flexible.',
                'type' => 'Product / Part',
                'category' => 'Vehicles',
                'offers' => 7,
                'budget' => '₦120,000',
                'status' => 'offers'
            ],
            [
                'id' => 3,
                'name' => 'GenTechNG',
                'location' => 'Ikeja, Lagos',
                'time' => '1 day ago',
                'title' => 'Need someone to repair a 15KVA generator in Ikeja',
                'desc' => 'My 15KVA generator keeps cutting off after 10 mins of use. Need a reliable technician who can diagnose and fix on-site.',
                'type' => 'Repair / Service',
                'category' => 'Equipment',
                'offers' => 5,
                'budget' => '₦25,000 – ₦40,000',
                'status' => 'open'
            ],
            [
                'id' => 4,
                'name' => 'LekkiMovers',
                'location' => 'Lekki, Lagos',
                'time' => '2 days ago',
                'title' => 'Need someone to move a washing machine from Ikeja to Lekki',
                'desc' => 'Need a reliable transporter with a van to move a washing machine. Pickup in Ikeja, drop-off in Lekki Phase 1.',
                'type' => 'Delivery / Logistics',
                'category' => 'Appliances',
                'offers' => 12,
                'budget' => '₦8,000 – ₦12,000',
                'status' => 'open'
            ],
            [
                'id' => 5,
                'name' => 'PhoneDoc',
                'location' => 'Surulere, Lagos',
                'time' => '3 days ago',
                'title' => 'iPhone battery is swelling — should I replace the battery or the phone?',
                'desc' => 'My iPhone 11 battery is visibly swelling and the screen is lifting. Is it safe to just replace the battery or should I get a new phone?',
                'type' => 'Question / Advice',
                'category' => 'Electronics',
                'offers' => 14,
                'budget' => null,
                'status' => 'open'
            ],
            [
                'id' => 6,
                'name' => 'FarmEquip',
                'location' => 'Kano, Kano',
                'time' => '4 days ago',
                'title' => 'Need a water pump for a 5-hectare farm in Kano',
                'desc' => 'Looking for a reliable water pump (diesel or electric) for irrigation. Must be able to handle 5 hectares. Open to new or used.',
                'type' => 'Product / Part',
                'category' => 'Agricultural',
                'offers' => 2,
                'budget' => '₦150,000 – ₦250,000',
                'status' => 'open'
            ],
            [
                'id' => 7,
                'name' => 'BuildRight',
                'location' => 'Abuja, FCT',
                'time' => '5 days ago',
                'title' => 'Where can I source quality roofing sheets in Abuja?',
                'desc' => 'Building a 3-bedroom house and need durable, affordable roofing sheets. Recommendations for suppliers in Abuja appreciated.',
                'type' => 'Question / Advice',
                'category' => 'Construction',
                'offers' => 9,
                'budget' => null,
                'status' => 'offers'
            ],
            [
                'id' => 8,
                'name' => 'TechRepairHub',
                'location' => 'Port Harcourt, Rivers',
                'time' => '1 week ago',
                'title' => 'Looking for a PS5 HDMI port repair specialist in PH',
                'desc' => 'My PS5 HDMI port is damaged and needs replacement. Need someone with experience and the right tools in Port Harcourt.',
                'type' => 'Repair / Service',
                'category' => 'Electronics',
                'offers' => 4,
                'budget' => '₦15,000 – ₦25,000',
                'status' => 'open'
            ],
            [
                'id' => 9,
                'name' => 'LogisticsPro',
                'location' => 'Lagos, Lagos',
                'time' => '1 week ago',
                'title' => 'Need a 3-ton truck to move furniture from Lagos to Ibadan',
                'desc' => 'Moving a 3-bedroom apartment worth of furniture. Need a covered 3-ton truck with helpers.',
                'type' => 'Delivery / Logistics',
                'category' => 'Vehicles',
                'offers' => 18,
                'budget' => '₦60,000 – ₦80,000',
                'status' => 'open'
            ],
            [
                'id' => 10,
                'name' => 'SolarGuy',
                'location' => 'Abuja, FCT',
                'time' => '1 week ago',
                'title' => 'Need solar inverter installation for a 3-bedroom house',
                'desc' => 'Looking for a certified installer for a 3.5KVA solar system. Should include panels, batteries, and inverter installation.',
                'type' => 'Repair / Service',
                'category' => 'Equipment',
                'offers' => 6,
                'budget' => '₦120,000 – ₦180,000',
                'status' => 'open'
            ],
        ];
    }

    #[On('filtersUpdated')]
    public function handleFiltersUpdated($filters)
    {
        $this->search = $filters['search'] ?? '';
        $this->category = $filters['category'] ?? '';
        $this->type = $filters['type'] ?? '';
        $this->location = $filters['location'] ?? '';
        $this->budgetMin = isset($filters['budgetMin']) && $filters['budgetMin'] !== '' ? (float)$filters['budgetMin'] : null;
        $this->budgetMax = isset($filters['budgetMax']) && $filters['budgetMax'] !== '' ? (float)$filters['budgetMax'] : null;
        $this->status = $filters['status'] ?? ['open'];
        $this->page = 1;
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
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
        $this->validate([
            'formType' => 'required',
            'formCategory' => 'required',
            'formTitle' => 'required|min:5',
            'formDesc' => 'required|min:10',
            'formLocation' => 'required',
        ]);

        $this->postSuccessMessage = true;
        $this->reset(['formType', 'formCategory', 'formTitle', 'formDesc', 'formLocation', 'formBudget', 'formResponse']);
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

        return view('livewire.marketplace.community.community-home', [
            'requests' => $paginated,
            'totalRequests' => $total,
            'totalPages' => $totalPages,
            'startDisplay' => $total === 0 ? 0 : $offset + 1,
            'endDisplay' => min($offset + $this->perPage, $total),
        ]);
    }
}
