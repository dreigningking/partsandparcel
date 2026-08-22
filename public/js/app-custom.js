/**
 * Parts & Parcel — Shared Application JavaScript
 */

let sidebarCollapsed = false;

// 1. MEGA MENU INTERACTIVITY
document.addEventListener('DOMContentLoaded', () => {
    const triggers = document.querySelectorAll('.nav-trigger');
    const menus = document.querySelectorAll('.mega-menu');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const key = trigger.dataset.menu;
            const menu = document.getElementById('mega-' + key);
            const isOpen = menu ? menu.classList.contains('open') : false;

            menus.forEach(m => m.classList.remove('open'));
            triggers.forEach(t => t.classList.remove('open'));

            if (!isOpen && menu) {
                menu.classList.add('open');
                trigger.classList.add('open');
            }
        });
    });

    menus.forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
    });
});

// 2. GLOBAL CLICK DELEGATION (ACCOUNT DROPDOWN & MENU DISMISSAL)
document.addEventListener('click', (e) => {
    const accountTrigger = e.target.closest('.account-menu-trigger');
    
    if (accountTrigger) {
        e.stopPropagation();
        const wrapper = accountTrigger.closest('.account-menu-wrapper');
        const panel = wrapper ? wrapper.querySelector('.account-dropdown-panel') : null;
        const chevron = accountTrigger.querySelector('.chevron-icon');
        const notifPanel = document.getElementById('notifications');
        
        const isCurrentlyHidden = panel ? panel.classList.contains('hidden') : true;

        document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
        if (notifPanel) notifPanel.classList.remove('open');

        if (isCurrentlyHidden && panel) {
            panel.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
        return;
    }

    if (e.target.closest('.account-dropdown-panel') || e.target.closest('#notifications') || e.target.closest('#messageDrawer') || e.target.closest('#conversation')) {
        return;
    }

    // Dismiss mega menus & dropdowns when clicking outside
    const triggers = document.querySelectorAll('.nav-trigger');
    const menus = document.querySelectorAll('.mega-menu');
    menus.forEach(m => m.classList.remove('open'));
    triggers.forEach(t => t.classList.remove('open'));
    document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
    
    const notifPanel = document.getElementById('notifications');
    if (notifPanel) notifPanel.classList.remove('open');
});

// 3. NOTIFICATIONS DROPDOWN TOGGLE
function toggleNotificationsDropdown() {
    const notifPanel = document.getElementById('notifications');
    if (notifPanel) {
        document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
        notifPanel.classList.toggle('open');
    }
}

// 4. MESSAGES DRAWER & CONVERSATION PANEL INTERACTIVITY
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
    const d = data[who];
    if (d) {
        const convName = document.getElementById('convName');
        const convSubject = document.getElementById('convSubject');
        const convAvatar = document.getElementById('convAvatar');
        if (convName) convName.textContent = d[0];
        if (convSubject) convSubject.textContent = d[1];
        if (convAvatar) convAvatar.textContent = d[2];
    }
}

function closeAll() {
    document.querySelectorAll('.account-dropdown-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.chevron-icon').forEach(c => c.classList.remove('rotate-180'));
    const notifPanel = document.getElementById('notifications');
    if (notifPanel) notifPanel.classList.remove('open');
    const messageDrawer = document.getElementById('messageDrawer');
    const conversation = document.getElementById('conversation');
    const globalOverlay = document.getElementById('globalOverlay');
    if (messageDrawer) messageDrawer.classList.remove('open');
    if (conversation) conversation.classList.remove('open');
    if (globalOverlay) globalOverlay.classList.remove('open');
}

// 5. DASHBOARD RETRACTABLE SIDEBAR TOGGLES
function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    if (sidebar) sidebar.classList.toggle('collapsed', sidebarCollapsed);
    if (main) main.style.marginLeft = sidebarCollapsed ? '76px' : '280px';
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar) sidebar.classList.toggle('mobile-open');
    if (overlay) overlay.classList.toggle('hidden');
}

// 6. PORTAL ACCORDION SECTIONS
function openSection(s) {
    let b = document.getElementById('buyer'),
        v = document.getElementById('seller');
    let bc = document.getElementById('bc');
    let sc = document.getElementById('sc');

    if (s === 'buyer') {
        if (b) b.classList.toggle('open');
        if (v) v.classList.remove('open');
        if (bc) bc.style.transform = b && b.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
        if (sc) sc.style.transform = 'rotate(0deg)';
    } else {
        if (v) v.classList.toggle('open');
        if (b) b.classList.remove('open');
        if (sc) sc.style.transform = v && v.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
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
