/**
 * Universal Technology Corporation (UTC) - Main JavaScript
 * Handles Theme Switching (Dark/Light), 3D Card Tilt Effects, and UI Interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    init3DTilt();
    initMobileNav();
});

/**
 * Theme Manager: Dark / Light Mode
 */
function initTheme() {
    const savedTheme = localStorage.getItem('utc_theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    applyTheme(savedTheme);

    const themeToggles = document.querySelectorAll('.theme-toggle-btn');
    themeToggles.forEach(btn => {
        btn.addEventListener('click', () => {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('utc_theme', newTheme);
            
            // Dispatch event for Three.js scene to react if needed
            window.dispatchEvent(new CustomEvent('utc-theme-changed', { detail: { theme: newTheme } }));
        });
    });
}

function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    updateThemeIcons(theme);
}

function updateThemeIcons(theme) {
    const sunIcons = document.querySelectorAll('.theme-icon-sun');
    const moonIcons = document.querySelectorAll('.theme-icon-moon');
    
    if (theme === 'dark') {
        sunIcons.forEach(el => el.classList.remove('hidden'));
        moonIcons.forEach(el => el.classList.add('hidden'));
    } else {
        sunIcons.forEach(el => el.classList.add('hidden'));
        moonIcons.forEach(el => el.classList.remove('hidden'));
    }
}

/**
 * 3D Interactive Card Tilt on Mouse Move
 */
function init3DTilt() {
    const cards = document.querySelectorAll('.tilt-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -8;
            const rotateY = ((x - centerX) / centerX) * 8;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)';
        });
    });
}

/**
 * Mobile Navigation Toggle
 */
function initMobileNav() {
    const toggleBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (toggleBtn && mobileMenu) {
        toggleBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

/**
 * Utility: Copy Text to Clipboard with toast feedback
 */
function copyToClipboard(text, successMsg = 'কপি করা হয়েছে!') {
    navigator.clipboard.writeText(text).then(() => {
        showToast(successMsg, 'success');
    }).catch(err => {
        showToast('কপি করতে ব্যর্থ হয়েছে', 'error');
    });
}

/**
 * Lightweight Toast Notification
 */
function showToast(message, type = 'info') {
    const existing = document.getElementById('utc-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'utc-toast';
    const bg = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600');
    toast.className = `fixed bottom-6 right-6 ${bg} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium z-50 flex items-center space-x-2 animate-bounce`;
    toast.innerHTML = `<span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
