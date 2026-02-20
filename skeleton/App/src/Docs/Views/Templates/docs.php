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
    <link rel="icon" href="<?= assets('img/favicon.ico') ?>">
    <title>Anchor Framework Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/tokyo-night-dark.min.css">
    <link rel="stylesheet" href="<?= assets('docs/style-rust.css') ?>">
</head>

<body>
    <header class="header">
        <a href="/docs/learn" class="logo d-flex align-items-start gap-2">
            <span><img width="35" height="35" src="<?= assets('img/logo.png') ?>"></span>
            <span>Anchor</span>
        </a>
        <div class="header-right">
            <a href="#reference" class="nav-link-header">Docs</a>
            <button class="theme-toggle" id="themeToggle">
                <span id="themeIcon">🌙</span>
            </button>
        </div>
    </header>

    <div class="container landing-container">
        <!-- Hero Section -->
        <section class="hero-landing">
            <div class="hero-bg-glow"></div>
            <div class="hero-content">
                <div class="badge-pill">
                    <span class="badge-dot"></span>
                    version 2.0
                </div>
                <h1 class="hero-title">
                    The <span class="text-gradient">Stable Foundation</span> for<br>
                    Shipping Production Code
                </h1>
                <p class="hero-subtitle">
                    Anchor is a lightweight, module-based framework designed for simplicity and speed.
                    Build powerful web applications with a toolset that feels familiar yet refreshingly modern.
                </p>
                <div class="hero-actions">
                    <a href="/docs/learn/introduction" class="btn btn-primary btn-lg">
                        <span>Get Started</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h10m-4-4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="https://github.com/beniyke/anchor" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0012 2z" />
                        </svg>
                        <span>GitHub</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Zero Configuration</h3>
                    <p>Ready to use out of the box with sensible defaults. No complex XML or YAML setup required.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Blazing Fast</h3>
                    <p>Optimized routing and request handling ensures your application responds in milliseconds.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Secure by Default</h3>
                    <p>Built-in protection against CSRF, XSS, and SQL injection. Production-ready security headers.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔌</div>
                    <h3>Modular Design</h3>
                    <p>Only use what you need. The module-based architecture keeps your footprint small.</p>
                </div>
            </div>
        </section>

        <!-- Documentation Reference -->
        <section id="reference" class="reference-section">
            <div class="section-header">
                <h2>Documentation</h2>
                <div class="search-box-inline">
                    <input type="text" class="search-input-inline" id="searchInput" placeholder="Search the docs...">
                </div>
            </div>

            <div id="docsContainer">
                <?php foreach ($groupedDocs as $groupName => $docs): ?>
                    <?php if (!empty($docs)): ?>
                        <div class="group-section" data-group="<?= strtolower($groupName) ?>">
                            <h2 class="group-title"><?= htmlspecialchars($groupName) ?></h2>
                            <div class="grid">
                                <?php foreach ($docs as $doc): ?>
                                    <div class="card" data-title="<?= strtolower($doc['title']) ?>">
                                        <a href="<?= $doc['url'] ?>"><?= htmlspecialchars($doc['title']) ?></a>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </section>

        <div class="footer text-center py-5 border-top" style="border-color: var(--border-color);">
            <div class="mb-4">
                <img width="40" height="40" src="<?= assets('img/logo.png') ?>" alt="Anchor Logo" style="opacity: 0.5; filter: grayscale(1);">
            </div>
            <p style="color: var(--text-tertiary);">
                &copy; <?= date('Y') ?> Anchor Framework.
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script>
    <script src="<?= assets('docs/smart-search.js') ?>"></script>
    <script>
        // Theme toggle logic (same as before)
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

        // Initialize Smart Search
        const searchInput = document.getElementById('searchInput');
        const groupSections = document.querySelectorAll('.group-section');
        const cards = document.querySelectorAll('.card');

        // Prepare docs data for SmartSearch
        const allDocs = <?= json_encode($groupedDocs) ?>;
        const flatDocs = [];
        Object.keys(allDocs).forEach(group => {
            allDocs[group].forEach(doc => {
                flatDocs.push({
                    ...doc,
                    group
                });
            });
        });

        const smartSearch = new SmartSearch(flatDocs, <?= json_encode($searchIndex ?? []) ?>, <?= json_encode($keywordMap ?? []) ?>);

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();

            if (!query) {
                groupSections.forEach(section => section.style.display = 'block');
                cards.forEach(card => card.style.display = 'block');
                return;
            }

            // Get results from SmartSearch
            const results = smartSearch.search(query);
            const matchingUrls = new Set(results.map(doc => doc.url));

            // Filter cards based on smart search results
            cards.forEach(card => {
                // The card link href should match doc.url
                const link = card.querySelector('a');
                const url = link.getAttribute('href');

                if (matchingUrls.has(url)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            // Hide empty sections
            groupSections.forEach(section => {
                const hasVisibleCards = Array.from(section.querySelectorAll('.card')).some(card =>
                    card.style.display !== 'none'
                );
                section.style.display = hasVisibleCards ? 'block' : 'none';
            });
        });
    </script>
</body>

</html>