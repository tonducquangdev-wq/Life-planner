<!-- Anti-Flicker & Theme Manager Initializer -->
<script>
    window.__USER_THEME = "{{ Auth::check() ? (Auth::user()->giao_dien ?? 'system') : 'system' }}";
    window.__CSRF_TOKEN = "{{ csrf_token() }}";
    (function () {
        var userTheme = window.__USER_THEME;
        var savedTheme = localStorage.getItem('theme');
        var themeToApply = 'light';
        if (savedTheme && (savedTheme === 'dark' || savedTheme === 'light')) {
            themeToApply = savedTheme;
        } else if (userTheme && (userTheme === 'dark' || userTheme === 'light')) {
            themeToApply = userTheme;
        } else {
            themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-bs-theme', themeToApply);
        if (themeToApply === 'dark') {
            document.documentElement.classList.add('dark-theme');
        } else {
            document.documentElement.classList.remove('dark-theme');
        }
    })();
</script>
<script src="{{ asset('js/theme-manager.js') }}?v={{ file_exists(public_path('js/theme-manager.js')) ? filemtime(public_path('js/theme-manager.js')) : time() }}" defer></script>
