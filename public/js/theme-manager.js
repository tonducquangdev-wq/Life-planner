/**
 * Life Planner - Theme Manager (Dark / Light / System Mode)
 * Seamless real-time theme switching with localStorage & Backend Sync.
 */
(function () {
    'use strict';

    const LifePlannerTheme = {
        getPreferredTheme: function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || savedTheme === 'light') {
                return savedTheme;
            }
            if (window.__USER_THEME && (window.__USER_THEME === 'dark' || window.__USER_THEME === 'light')) {
                return window.__USER_THEME;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },

        setTheme: function (theme, persistBackend = true) {
            let actualTheme = theme;
            if (theme === 'system') {
                actualTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.setAttribute('data-bs-theme', actualTheme);
            if (actualTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
                document.body?.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
                document.body?.classList.remove('dark-theme');
            }

            // Cập nhật meta theme-color cho thanh trạng thái di động (đồng bộ nền Header để tránh camera / tai thỏ nổi lệch màu)
            const metaThemeList = document.querySelectorAll('meta[name="theme-color"]');
            metaThemeList.forEach(function (el) {
                el.setAttribute('content', actualTheme === 'dark' ? '#0f172a' : '#ffffff');
            });

            localStorage.setItem('theme', theme);
            this.updateIcons(actualTheme);

            if (persistBackend) {
                const csrfToken = window.__CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    fetch('/profile/theme/quick-toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ giao_dien: theme })
                    }).catch(err => console.warn('[Theme] Lỗi lưu theme lên máy chủ:', err));
                }
            }
        },

        toggle: function () {
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = (current === 'dark') ? 'light' : 'dark';
            this.setTheme(next, true);

            if (window.LifePlannerPWA && typeof window.LifePlannerPWA.showToast === 'function') {
                window.LifePlannerPWA.showToast('info', next === 'dark' ? 'Đã bật giao diện Tối (Dark Mode)' : 'Đã bật giao diện Sáng (Light Mode)', 'Chế độ giao diện');
            }
        },

        updateIcons: function (actualTheme) {
            const isDark = (actualTheme === 'dark');
            document.querySelectorAll('.theme-icon-dark').forEach(el => {
                el.classList.toggle('d-none', isDark);
            });
            document.querySelectorAll('.theme-icon-light').forEach(el => {
                el.classList.toggle('d-none', !isDark);
            });
            document.querySelectorAll('.theme-label-text').forEach(el => {
                el.textContent = isDark ? '☀️ Giao diện Sáng' : '🌙 Giao diện Tối';
            });
            document.querySelectorAll('#topbarThemeToggle, #calendarThemeToggle, .btn-theme-toggle').forEach(btn => {
                btn.title = isDark ? 'Chuyển sang Giao diện Sáng' : 'Chuyển sang Giao diện Tối';
            });
        },

        init: function () {
            const preferred = this.getPreferredTheme();
            this.setTheme(preferred, false);

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                const saved = localStorage.getItem('theme');
                if (saved === 'system' || !saved) {
                    this.setTheme(e.matches ? 'dark' : 'light', false);
                }
            });
        }
    };

    window.LifePlannerTheme = LifePlannerTheme;

    // Chạy đồng bộ icon và trạng thái khi DOM tải xong
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            LifePlannerTheme.updateIcons(document.documentElement.getAttribute('data-bs-theme') || 'light');
        });
    } else {
        LifePlannerTheme.updateIcons(document.documentElement.getAttribute('data-bs-theme') || 'light');
    }
})();
