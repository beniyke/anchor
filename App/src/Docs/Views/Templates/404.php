<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Apply theme immediately to prevent flash -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Anchor Documentation</title>
    <link rel="stylesheet" href="<?= assets('docs/style-rust.css') ?>">
</head>

<body>
    <header class="header">
        <a href="/docs/learn" class="logo">
            <span><img width="30" height="30" src="<?=assets('img/logo.png')?>"></span>
            <span>Anchor</span>
        </a>
        <div class="header-right">
            <a href="/docs/learn" class="nav-link-header">Docs</a>
            <button class="theme-toggle" id="themeToggle">
                <span id="themeIcon">🌙</span>
            </button>
        </div>
    </header>

    <div class="container landing-container" style="min-height: calc(100vh - 73px); display: flex; align-items: center; justify-content: center;">
        <div class="hero-landing" style="flex-direction: column; text-align: center; padding: 2rem;">
            <div class="hero-content" style="max-width: 600px; margin: 0 auto; text-align: center;">
                <div class="badge-pill" style="margin: 0 auto 2rem;">
                    <span class="badge-dot" style="background: #ef4444; box-shadow: 0 0 8px #ef4444;"></span>
                    404 Error
                </div>

                <h1 class="hero-title">
                    Page <span class="text-gradient">Not Found</span>
                </h1>

                <p class="hero-subtitle">
                    The documentation page <code><?= htmlspecialchars($page) ?>.md</code> doesn't exist or has been moved.
                </p>

                <div class="hero-actions" style="justify-content: center;">
                    <a href="/docs/learn" class="btn btn-primary btn-lg">
                        <span>Browse Docs</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h10m-4-4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary btn-lg">
                        <span>Go Back</span>
                    </a>
                </div>
            </div>

            <!-- Decorative blobs -->
            <div class="glow-blob blob-1" style="width: 400px; height: 400px; opacity: 0.2;"></div>
            <div class="glow-blob blob-2" style="width: 300px; height: 300px; opacity: 0.2;"></div>
        </div>
    </div>

    <div class="footer">
        <p>Built with ❤️ using Anchor Framework</p>
    </div>

    <script>
        // Theme toggle logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        const currentTheme = html.getAttribute('data-theme') || 'light';
        updateThemeIcon(currentTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            themeIcon.textContent = theme === 'light' ? '🌙' : '☀️';
        }
    </script>
</body>

</html>