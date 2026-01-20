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
    <title><?= $title ?> - Anchor Framework Documentation</title>
    <link rel="stylesheet" href="<?= assets('docs/style-rust.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/tokyo-night-dark.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="/docs/learn" class="logo d-flex align-items-start gap-2">
            <span><img width="35" height="35" src="<?= assets('img/logo.png') ?>"></span>
            <span>Anchor</span>
        </a>
        <div class="header-actions">
            <button class="search-trigger" id="searchTrigger">
                <span>🔍</span>
                <span>Search documentation...</span>
                <span class="kbd">Ctrl+K</span>
            </button>
            <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                <span id="themeIcon">🌙</span>
            </button>
            <button class="theme-toggle" id="mobileMenuToggle" style="display: none;" title="Menu">
                <span>☰</span>
            </button>
        </div>
    </header>

    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <?php foreach ($groupedDocs as $groupName => $groupDocs): ?>
                <?php if (!empty($groupDocs)): ?>
                    <div class="nav-group">
                        <div class="nav-group-title">
                            <span><?= htmlspecialchars($groupName) ?></span>
                            <span class="nav-group-icon">▼</span>
                        </div>
                        <div class="nav-group-items">
                            <?php foreach ($groupDocs as $doc): ?>
                                <a href="<?= $doc['url'] ?>"
                                    class="nav-link <?= $doc['name'] === $currentPage ? 'active' : '' ?>"
                                    data-page="<?= $doc['name'] ?>">
                                    <?= htmlspecialchars($doc['title']) ?>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </aside>

        <!-- Content -->
        <main class="content">
            <?= $content ?>
        </main>

        <!-- Table of Contents -->
        <?php if (!empty($toc)): ?>
            <aside class="toc-container">
                <div class="toc-title">On This Page</div>
                <ul class="toc-list">
                    <?php foreach ($toc as $item): ?>
                        <li class="toc-item level-<?= $item['level'] ?>">
                            <a href="#<?= $item['anchor'] ?>" class="toc-link">
                                <?= htmlspecialchars(str_replace(['**', '__'], '', $item['title'])) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </aside>
        <?php endif ?>
    </div>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-box">
            <div class="search-input-wrapper">
                <input type="text" class="search-input" id="searchInput" placeholder="Search documentation...">
            </div>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="<?= assets('docs/smart-search.js') ?>"></script>
    <script>
        // Highlight code blocks
        hljs.highlightAll();

        // Theme toggle
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

        // Navigation group toggle
        document.querySelectorAll('.nav-group-title').forEach(title => {
            title.addEventListener('click', () => {
                title.parentElement.classList.toggle('collapsed');
            });
        });

        // Auto-expand current group
        const activeLink = document.querySelector('.nav-link.active');
        if (activeLink) {
            const group = activeLink.closest('.nav-group');
            if (group) {
                group.classList.remove('collapsed');
            }
            // Scroll sidebar to show active link
            setTimeout(() => {
                activeLink.scrollIntoView({
                    block: 'center',
                    behavior: 'smooth'
                });
            }, 100);
        }

        // Table of Contents scroll spy
        const tocLinks = document.querySelectorAll('.toc-link');
        const headings = Array.from(document.querySelectorAll('.content h2, .content h3'));
        const tocContainer = document.querySelector('.toc-container');

        function updateActiveTocLink() {
            let current = '';

            // Default to first heading if at top
            if (window.scrollY < 100 && headings.length > 0) {
                // optionally set first one active or none
            }

            headings.forEach(heading => {
                // Adjust offset to account for header
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 120) {
                    current = heading.id;
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');

                    // Auto-scroll TOC container to keep active link in view
                    if (tocContainer) {
                        const linkRect = link.getBoundingClientRect();
                        const containerRect = tocContainer.getBoundingClientRect();

                        if (linkRect.top < containerRect.top || linkRect.bottom > containerRect.bottom) {
                            link.scrollIntoView({
                                block: 'nearest',
                                behavior: 'smooth'
                            });
                        }
                    }
                }
            });
        }

        window.addEventListener('scroll', () => {
            requestAnimationFrame(updateActiveTocLink);
        });
        updateActiveTocLink();

        // Explicit clean scroll handler
        tocLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);

                if (target) {
                    // Get dynamic header height + buffer
                    const header = document.querySelector('.header');
                    const headerHeight = header ? header.offsetHeight : 73;
                    const buffer = 24; // Extra breathing room

                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - (headerHeight + buffer);

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL without jumping
                    history.pushState(null, '', link.getAttribute('href'));
                }
            });
        });

        // Smart Search functionality
        const searchModal = document.getElementById('searchModal');
        const searchTrigger = document.getElementById('searchTrigger');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');

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

        searchTrigger.addEventListener('click', openSearch);
        searchModal.addEventListener('click', (e) => {
            if (e.target === searchModal) closeSearch();
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openSearch();
            }
            if (e.key === 'Escape') {
                closeSearch();
            }
        });

        function openSearch() {
            searchModal.classList.add('active');
            searchInput.focus();
        }

        function closeSearch() {
            searchModal.classList.remove('active');
            searchInput.value = '';
            searchResults.innerHTML = '';
        }

        let selectedIndex = -1;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            selectedIndex = -1;

            if (!query) {
                searchResults.innerHTML = '';
                return;
            }

            // Use smart search
            const results = smartSearch.search(query);

            if (results.length === 0) {
                searchResults.innerHTML = '<div class="search-empty">No results found. Try different keywords!</div>';
                return;
            }

            searchResults.innerHTML = results.map((doc, index) => `
                <div class="search-result" data-index="${index}" data-url="${doc.url}">
                    <div class="search-result-title">
                        ${smartSearch.highlightMatch(doc.title, query)}
                        <span class="search-result-score">${doc.score}% match</span>
                    </div>
                    <div class="search-result-group">${doc.group}</div>
                    ${doc.description ? `<div class="search-result-description">${smartSearch.highlightMatch(doc.description, query)}</div>` : ''}
                </div>
            `).join('');

            document.querySelectorAll('.search-result').forEach(result => {
                result.addEventListener('click', () => {
                    window.location.href = result.dataset.url;
                });
            });
        });

        searchInput.addEventListener('keydown', (e) => {
            const results = document.querySelectorAll('.search-result');
            if (results.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, results.length - 1);
                updateSelectedResult(results);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelectedResult(results);
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                window.location.href = results[selectedIndex].dataset.url;
            }
        });

        function updateSelectedResult(results) {
            results.forEach((result, index) => {
                result.classList.toggle('selected', index === selectedIndex);
                if (index === selectedIndex) {
                    result.scrollIntoView({
                        block: 'nearest'
                    });
                }
            });
        }

        // Mobile menu
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');

        if (window.innerWidth <= 768) {
            mobileMenuToggle.style.display = 'flex';
        }

        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('active');
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                mobileMenuToggle.style.display = 'flex';
            } else {
                mobileMenuToggle.style.display = 'none';
                sidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('active');
            }
        });
    </script>
</body>

</html>