// ==========================================
// PARTS & PARCEL — SHARED CUSTOM JS UTILITIES
// ==========================================

// 1. MEGA MENU TRIGGER LOGIC (GLOBAL EVENT DELEGATION)
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.nav-trigger');
    const megaMenus = document.querySelectorAll('.mega-menu');
    const navTriggers = document.querySelectorAll('.nav-trigger');

    if (trigger) {
        e.stopPropagation();
        let targetId = trigger.getAttribute('data-target') || trigger.getAttribute('data-menu');
        if (targetId && !targetId.startsWith('mega-')) {
            targetId = 'mega-' + targetId;
        }
        const targetMenu = document.getElementById(targetId);
        const isOpen = targetMenu ? targetMenu.classList.contains('open') : false;

        megaMenus.forEach(m => m.classList.remove('open'));
        navTriggers.forEach(t => t.classList.remove('open'));

        if (!isOpen && targetMenu) {
            targetMenu.classList.add('open');
            trigger.classList.add('open');
        }
        return;
    }

    if (!e.target.closest('.mega-menu')) {
        megaMenus.forEach(m => m.classList.remove('open'));
        navTriggers.forEach(t => t.classList.remove('open'));
    }
});

// 2. ACCOUNT MENU DROPDOWN (VANILLA JS EVENT DELEGATION)
document.addEventListener('click', function(e) {
    const trigger = e.target.closest('.account-menu-trigger');
    
    if (trigger) {
        e.stopPropagation();
        const wrapper = trigger.closest('.account-menu-wrapper');
        const panel = wrapper ? wrapper.querySelector('.account-dropdown-panel') : null;
        const chevron = trigger.querySelector('.chevron-icon');
        const isOpen = panel ? !panel.classList.contains('hidden') : false;

        document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen && panel) {
            panel.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
        return;
    }

    if (!e.target.closest('.account-menu-wrapper')) {
        document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
    }
});

// 3. HEADER NOTIFICATIONS DROPDOWN
function toggleNotificationsDropdown() {
    const notifications = document.getElementById('notifications');
    if (notifications) {
        notifications.classList.toggle('open');
    }
}

document.addEventListener('click', (e) => {
    const notifications = document.getElementById('notifications');
    if (notifications && notifications.classList.contains('open')) {
        if (!e.target.closest('#notifications') && !e.target.closest('[aria-label="Notifications"]')) {
            notifications.classList.remove('open');
        }
    }
});

// 4. DRAWERS & MESSAGING SYSTEM
function openMessages() {
    const messageDrawer = document.getElementById('messageDrawer');
    const globalOverlay = document.getElementById('globalOverlay');
    if (messageDrawer) messageDrawer.classList.add('open');
    if (globalOverlay) globalOverlay.classList.add('open');
}

function closeMessages() {
    const messageDrawer = document.getElementById('messageDrawer');
    const conversation = document.getElementById('conversation');
    const globalOverlay = document.getElementById('globalOverlay');
    if (messageDrawer) messageDrawer.classList.remove('open');
    if (conversation) conversation.classList.remove('open');
    if (globalOverlay) globalOverlay.classList.remove('open');
}

function backToInbox() {
    const conversation = document.getElementById('conversation');
    if (conversation) conversation.classList.remove('open');
}

function openConversation(who) {
    const conversation = document.getElementById('conversation');
    if (conversation) conversation.classList.add('open');
    const data = {
        adam: ['Adam', 'HP EliteBook 840 G5', 'A'],
        abel: ['Abel', 'Dell Latitude motherboard', 'A'],
        seth: ['Seth', 'HP EliteBook Battery', 'S']
    };
    if (data[who]) {
        const [name, subject, avatar] = data[who];
        const convName = document.getElementById('convName');
        const convSubject = document.getElementById('convSubject');
        const convAvatar = document.getElementById('convAvatar');
        if (convName) convName.textContent = name;
        if (convSubject) convSubject.textContent = subject;
        if (convAvatar) convAvatar.textContent = avatar;
    }
}

function closeAll() {
    closeMessages();
}

// 5. DASHBOARD SIDEBAR CONTROLS
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    if (sidebar) sidebar.classList.toggle('collapsed');
    if (main) main.classList.toggle('ml-[76px]');
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar) sidebar.classList.toggle('mobile-open');
    if (overlay) overlay.classList.toggle('hidden');
}

// 6. DASHBOARD SIDEBAR ACCORDIONS
function openSection(s) {
    const el = document.getElementById(s);
    if (!el) return;
    const isBuyer = s === 'buyer';
    const otherId = isBuyer ? 'seller' : 'buyer';
    const otherEl = document.getElementById(otherId);

    const isOpening = !el.classList.contains('open');

    if (otherEl) otherEl.classList.remove('open');
    if (isOpening) {
        el.classList.add('open');
    } else {
        el.classList.remove('open');
    }

    const bc = document.getElementById('bc');
    const sc = document.getElementById('sc');

    if (isBuyer) {
        if (bc) bc.style.transform = isOpening ? 'rotate(180deg)' : 'rotate(0deg)';
        if (sc) sc.style.transform = 'rotate(0deg)';
    } else {
        if (sc) sc.style.transform = isOpening ? 'rotate(180deg)' : 'rotate(0deg)';
        if (bc) bc.style.transform = 'rotate(0deg)';
    }
}

// 7. COLOR MODE THEME SWITCHER
function setThemeMode(mode, event) {
    if (event) event.stopPropagation();
    const btns = document.querySelectorAll('.theme-btn');
    btns.forEach(b => {
        b.classList.remove('bg-white', 'shadow-xs', 'font-bold', 'text-slate-900');
        b.classList.add('font-semibold', 'text-slate-600');
    });

    if (event && event.target) {
        event.target.classList.add('bg-white', 'shadow-xs', 'font-bold', 'text-slate-900');
        event.target.classList.remove('text-slate-600');
    }

    const label = document.getElementById('current-mode-label');
    if (label) label.textContent = mode.charAt(0).toUpperCase() + mode.slice(1);

    if (mode === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (mode === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}

// 8. MOBILE BROWSE OVERLAY & ACCOUNT CLICK CONTROLS
function toggleMobileBrowse() {
    const el = document.getElementById('mobileBrowseOverlay');
    if (el) el.classList.toggle('open');
}

function closeMobileBrowse() {
    const el = document.getElementById('mobileBrowseOverlay');
    if (el) el.classList.remove('open');
}

function toggleMobileCat(catId, e) {
    if (e) e.stopPropagation();
    const sub = document.getElementById('mobile-sub-' + catId);
    const chev = document.getElementById('mobile-chev-' + catId);
    if (sub) sub.classList.toggle('hidden');
    if (chev) chev.classList.toggle('rotate-90');
}

function handleMobileAccountClick() {
    toggleMobileSidebar();
}
