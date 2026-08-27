# Community Pages CSS Mapping & Audit Reference

This document maps all HTML elements and CSS definitions from the original prototype templates (`request-all.html` and `request-details-3.html`) against `resources/views/livewire/marketplace/community/community-home.blade.php`, `resources/views/layouts/app.blade.php`, and `public/css/app-custom.css`.

---

## 1. Global & Base Layout CSS (Discarded from Page Views)

These CSS selectors originate from the standalone prototype HTML templates and must **NOT** be included in page-specific Blade views because they are already provided by `resources/views/layouts/app.blade.php`, `public/css/app-custom.css`, or Tailwind CSS base resets.

| Prototype Selector (`request-all.html` / `request-details-3.html`) | Original Prototype CSS Definition | Replacement in `app.blade.php`, `app-custom.css`, or Tailwind Utility |
|---|---|---|
| `*`, `*::before`, `*::after` | `box-sizing: border-box; margin: 0; padding: 0;` | Tailwind Base Reset (`@tailwind base`) |
| `html` | `font-size: 16px; scroll-behavior: smooth;` | `html { scroll-behavior: smooth; }` in `app-custom.css` |
| `body` | `font-family: 'Inter', sans-serif; background: #F4F6FA; color: #0B1A33;` | `<body class="bg-slate-50/50 text-slate-900 pb-16 lg:pb-0">` in `app.blade.php` + `app-custom.css` typography |
| `a` | `text-decoration: none; color: inherit;` | Tailwind base link styling |
| `button` | `cursor: pointer; font-family: inherit; border: none; background: none;` | Tailwind base button reset |
| `:root` variables | `--purple: #6B2FA0; --navy: #0B1A33; --border-color: #E8ECF2;` | Tailwind color tokens (`bg-pp-600`, `text-pp-700`, `border-slate-200`) or inline CSS variables |
| `.site-header`, `.header-top` | `position: fixed; top: 0; height: 72px; background: #fff; box-shadow: ...` | `@include('layouts.partials.header')` with Tailwind classes (`sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200/80`) |
| `.logo`, `.logo-icon`, `.logo span` | `font-weight: 800; font-size: 1.3rem; color: #6B2FA0;` | Header partial Tailwind logo styling (`text-pp-600 font-extrabold`) |
| `.header-search`, `.header-search input` | `max-width: 520px; padding: 10px 18px 10px 44px; border-radius: 50px;` | Header partial Tailwind search input (`w-full pl-10 pr-4 py-2 rounded-full border border-slate-200 bg-slate-50`) |
| `.header-actions`, `.header-nav` | `display: flex; gap: 6px; border-top: 1px solid #E8ECF2; height: 52px;` | Header partial navigation bar with Tailwind flex utilities |
| `.mega-menu`, `.mega-menu.open` | `display: none; &.open { display: block; }` | Defined in `public/css/app-custom.css` (lines 27-30) |
| `.nav-trigger`, `.nav-chevron` | `transition: transform .2s ease; &.open { transform: rotate(180deg); }` | Defined in `public/css/app-custom.css` (lines 29-30) |
| `[x-cloak]` | `display: none !important;` | Defined in `public/css/app-custom.css` (line 4) |
| `.custom-scrollbar` | `::-webkit-scrollbar { width: 4px; }` | Defined in `public/css/app-custom.css` (lines 10-24) |
| `.hero-grid`, `.product-img` | Background patterns & gradients | Defined in `public/css/app-custom.css` (lines 33-40) |
| Drawer & Overlay base (`.drawer`, `.overlay`) | `transform: translateX(110%); transition: transform .28s;` | Defined in `public/css/app-custom.css` (lines 106-130) |

---

## 2. Element-by-Element Mapping: `request-all.html` vs `community-home.blade.php`

This table traces every HTML section and element in `community-home.blade.php` back to its equivalent in `request-all.html`, detailing the original CSS and its Tailwind CSS / App layout replacement.

| Section / Element | Prototype Element (`request-all.html`) | Original CSS in `request-all.html` | Equivalent in `community-home.blade.php` (Tailwind / Layout) |
|---|---|---|---|
| **Page Outer Container** | `<main class="main-content">` | `max-width: 1280px; margin: 0 auto; padding: 20px 24px 60px;` | `<main class="w-full pb-16">` |
| **Breadcrumb Container** | `<div class="breadcrumb-wrap">` | `max-width: 1280px; margin: 0 auto; padding: 16px 24px 4px; font-size: 0.8rem; color: #7A8AA0;` | `<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-3.5">` |
| **Breadcrumb Links** | `<a href="#">Home</a> <span>/</span>` | `.breadcrumb-wrap a { color: #6B2FA0; font-weight: 500; }` | `<div class="flex items-center gap-2 text-xs font-semibold text-slate-400"><a href="..." class="hover:text-slate-700">Home</a>` |
| **Hero Banner Container** | `<div class="hero-wrap">` | `background: linear-gradient(135deg, #F5F0FA 0%, #FFFFFF 100%); border: 1px solid #E8ECF2; border-radius: 16px; padding: 32px;` | `<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-6"><div class="bg-gradient-to-r from-pp-50 via-white to-pp-50/50 rounded-2xl border border-pp-100 p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 shadow-2xs">` |
| **Hero Title & Subtitle** | `<h1 class="hero-title">` | `font-size: 1.8rem; font-weight: 800; color: #0B1A33; margin-bottom: 6px;` | `<h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">Community <span class="text-pp-600">Hub</span></h1>` |
| **Hero Action Buttons** | `<button class="btn-primary">` | `background: #6B2FA0; color: #fff; padding: 12px 28px; border-radius: 50px; font-weight: 700;` | `<button wire:click="openPostModal" class="px-5 py-2.5 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">` |
| **Hero Stats Grid** | `<div class="hero-stats">` | `display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; background: #fff; padding: 16px; border-radius: 12px;` | `<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 gap-3 bg-white p-4 rounded-xl border border-slate-200/80 shrink-0 shadow-2xs">` |
| **Hero Stat Item** | `<div class="stat-box">` | `.stat-number { font-size: 1.4rem; font-weight: 800; color: #0B1A33; }` | `<div class="text-center px-3 py-1"><div class="text-lg sm:text-xl font-black text-slate-900">1,248</div><div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Open Requests</div></div>` |
| **Category Layout Grid** | `<div class="hub-layout">` | `display: grid; grid-template-columns: 280px 1fr; gap: 28px;` | `<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8"><div class="grid grid-cols-1 lg:grid-cols-4 gap-6">` |
| **Sidebar Filter Container** | `<div class="sidebar-col">` | `position: sticky; top: 140px; display: flex; flex-direction: column; gap: 20px;` | `<aside class="hidden lg:block lg:col-span-1 space-y-5">` |
| **Sidebar Card Widget** | `<div class="sidebar-card">` | `background: #fff; border-radius: 12px; border: 1px solid #E8ECF2; padding: 20px; box-shadow: 0 1px 3px rgba(11,26,51,0.06);` | `<div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs space-y-4">` |
| **Sidebar Filter Header** | `<div class="sidebar-title">` | `font-weight: 700; font-size: 0.9rem; text-transform: uppercase; color: #0B1A33; margin-bottom: 12px;` | `<div class="flex items-center justify-between pb-3 border-b border-slate-100"><span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider flex items-center gap-2">` |
| **Filter Checkboxes / Radios** | `<label class="filter-item">` | `display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #4A5A72; cursor: pointer;` | `<label class="flex items-center gap-2.5 text-xs text-slate-600 font-semibold cursor-pointer"><input type="checkbox" ... class="rounded border-slate-300 text-pp-600 focus:ring-pp-500">` |
| **Navigation Tabs** | `<div class="hub-tabs">` | `display: flex; gap: 8px; border-bottom: 1px solid #E8ECF2; margin-bottom: 20px;` | `<div class="border-b border-slate-200 mb-5"><nav class="-mb-px flex space-x-2 sm:space-x-3 overflow-x-auto no-scrollbar" aria-label="Tabs">` |
| **Tab Buttons** | `<button class="tab-btn active">` | `.tab-btn { padding: 10px 20px; font-size: 0.85rem; font-weight: 600; border-radius: 50px; color: #4A5A72; } .tab-btn.active { background: #6B2FA0; color: #fff; }` | `<button wire:click="setTab('all')" class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 transition cursor-pointer rounded-t-xl whitespace-nowrap {{ $tab === 'all' ? 'border-pp-600 text-pp-600 bg-pp-50/60' : 'border-transparent text-slate-500 hover:text-slate-900' }}">` |
| **Toolbar Container** | `<div class="toolbar">` | `display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px;` | `<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 mb-5 shadow-2xs">` |
| **Sort Select Dropdown** | `<select class="sort-select">` | `padding: 8px 16px; border-radius: 8px; border: 1.5px solid #E8ECF2; font-size: 0.85rem; outline: none;` | `<select wire:model.live="sort" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 focus:bg-white focus:border-pp-600 outline-none transition">` |
| **Request Item Card** | `<div class="request-card">` | `background: #fff; border-radius: 12px; border: 1px solid #E8ECF2; padding: 20px; box-shadow: 0 1px 3px rgba(11,26,51,0.06); margin-bottom: 16px;` | `<div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs hover:shadow-card hover:border-pp-200 transition duration-200 space-y-3">` |
| **Request Card Meta Header** | `<div class="card-header">` | `display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #7A8AA0;` | `<div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">` |
| **Request Type Badge** | `<span class="type-badge">` | `background: #F5F0FA; color: #4E1D78; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; padding: 4px 12px; border-radius: 50px;` | `<span class="px-2.5 py-0.5 rounded-full bg-pp-50 text-pp-700 font-extrabold text-[10px] uppercase tracking-wider border border-pp-100">` |
| **Request Card Title** | `<h3 class="card-title">` | `font-size: 1.1rem; font-weight: 700; color: #0B1A33; margin-bottom: 6px;` | `<h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">` |
| **Request Card Description** | `<p class="card-desc">` | `font-size: 0.9rem; color: #4A5A72; line-height: 1.6; margin-bottom: 14px;` | `<p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-2">` |
| **Request Card Footer** | `<div class="card-footer">` | `display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid #E8ECF2;` | `<div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">` |
| **Offer Count Pill Badge** | `<span class="stat-badge">` | `font-size: 0.75rem; color: #6B2FA0; background: #F5F0FA; padding: 4px 12px; border-radius: 50px; font-weight: 600;` | `<span class="px-3 py-1 rounded-full bg-pp-50 text-pp-700 font-extrabold text-xs border border-pp-100 flex items-center gap-1.5">` |
| **Action Button ("View & Offer")** | `<a class="btn-action">` | `background: #6B2FA0; color: #fff; padding: 8px 18px; border-radius: 50px; font-weight: 700; font-size: 0.8rem;` | `<a href="..." class="px-4 py-2 rounded-xl bg-pp-600 hover:bg-pp-700 text-white font-extrabold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">` |
| **Post Request Modal Overlay** | `<div class="modal-overlay">` | `position: fixed; inset: 0; background: rgba(11,26,51,0.5); z-index: 2000; display: flex; align-items: center; justify-content: center;` | `<div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">` |
| **Post Request Modal Card** | `<div class="modal">` | `background: #fff; border-radius: 16px; width: 100%; max-width: 600px; padding: 28px; box-shadow: 0 8px 32px rgba(11,26,51,0.12);` | `<div class="bg-white rounded-2xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border border-slate-200">` |
| **Mobile Filter Drawer Overlay** | `<div class="drawer-overlay">` | `position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1500;` | `<div class="fixed inset-0 z-50 bg-slate-900/50 lg:hidden">` |
| **Mobile Filter Drawer Panel** | `<div class="mobile-filter-drawer">` | `position: fixed; top: 0; right: 0; width: 320px; height: 100%; background: #fff; z-index: 1600; padding: 24px;` | `<div class="fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-white p-6 overflow-y-auto shadow-2xl flex flex-col justify-between lg:hidden">` |

---

## 3. Application to `community-request.blade.php` Cleanup

By comparing `request-details-3.html` against this exact CSS mapping, we identify the exact CSS rules in `request-details-3.html` that are **DISCARDED** vs **RETAINED**:

### A. DISCARDED Rules (DO NOT put in `@push('styles')`)
1. **Base Resets & Fonts**: `*`, `*::before`, `*::after`, `html`, `body`, `a`, `button`, `input`, `select`, `textarea`, `img` -> Handled by Tailwind base reset and `app-custom.css`.
2. **Site Header & Navigation**: `.site-header`, `.header-top`, `.logo`, `.logo-icon`, `.header-search`, `.header-actions`, `.header-nav`, `.mega-menu`, `.nav-trigger` -> Handled by `@include('layouts.partials.header')` and `app-custom.css`.

### B. RETAINED Rules (Keep in `@push('styles')` in `community-request.blade.php`)
1. **Page-Specific CSS Variables**: `:root` declaration defining local design tokens (`--purple`, `--navy`, `--purple-bg`, etc.).
2. **Breadcrumb**: `.breadcrumb-wrap`, `.breadcrumb-wrap a`, `.breadcrumb-wrap span`.
3. **Request Header & Details Cards**: `.request-header-card`, `.type-badge`, `.meta-row`, `.stats-row`, `.details-card`, `.details-grid`, `.attachments-row`.
4. **Action Bar & Buttons**: `.action-bar`, `.btn-primary`, `.btn-secondary`, `.btn-outline-purple`, `.btn-ghost`.
5. **Response Composer**: `.composer-card`, `.composer-header`, `.composer-offer-area`, `.composer-actions`, `.quota-warning`.
6. **Response List & Cards**: `.response-list`, `.list-header`, `.response-card`, `.resp-avatar`, `.resp-meta`, `.resp-body`, `.resp-actions`, `.resp-offer-snippet`.
7. **Sidebar Widgets**: `.side-card`, `.status-widget`, `.requester-widget`, `.similar-item`.
8. **Offer Drawer**: `.offer-drawer`, `.drawer-header`, `.drawer-body`, `.drawer-footer`, `.drawer-response-context`, `.drawer-offer-count`, `.offer-timeline`, `.offer-card`, `.drawer-offer-form`.
9. **Full Negotiation Overlay**: `.full-negotiation-overlay`, `.full-header`, `.full-body`.
10. **Modals & Overlays**: `.report-modal`, `.messaging-drawer`, `.lightbox-overlay`.

---

## 4. Standard Workflow for Future Prototype Page Conversions

When converting future HTML prototype pages from the same template family:
1. **Discard** `<head>`, `<body>`, `*` reset, and `.site-header` HTML and CSS markup.
2. **Utilize** `@include('layouts.partials.header')`, `@include('layouts.partials.footer')`, and global drawer partials.
3. **Replace** generic layout containers (widths, paddings, flex grids) with Tailwind utility classes (`max-w-[1440px]`, `px-4`, `flex`, `grid`, `rounded-2xl`).
4. **Push** only component-specific styles (custom card components, timeline drawers, interactive overlays) into `@push('styles')`.
5. **Push** page-specific JS into `@push('scripts')`.