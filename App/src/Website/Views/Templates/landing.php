<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anchor Framework - Build Faster</title>
    <!-- Rust Theme Styles -->
    <link rel="stylesheet" href="<?= assets('docs/style-rust.css') ?>">
    <style>
        /* Custom tweaks for the SaaS landing concept */
        .hero-landing {
            text-align: center;
            align-items: center;
            padding-top: 6rem;
            padding-bottom: 4rem;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .hero-actions {
            justify-content: center;
            margin-top: 2rem;
        }

        .code-showcase-section {
            padding: 4rem 1rem;
            position: relative;
        }

        .code-window {
            margin: 0 auto;
            max-width: 700px;
            transform: none;
            /* Reset rotation for this concept if desired, or keep it */
        }

        .feature-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(234, 88, 12, 0.1);
            color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container landing-container">

        <!-- Centered Hero -->
        <section class="hero-landing">
            <div class="hero-bg-glow" style="top: -200%; width: 150%; height: 200%;"></div>
            <div class="hero-content">
                <div class="badge-pill">
                    <span class="badge-dot"></span>
                    Version 2.0
                </div>
                <h1 class="hero-title">
                    The PHP Framework<br>
                    <span class="text-gradient">for Builders</span>
                </h1>
                <p class="hero-subtitle" style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Ship code with confidence on a foundation built for stability.
                    A lightweight, production-ready framework that gives you the tools to build fast and stay grounded.
                </p>
                <div class="hero-actions">
                    <a href="/docs/learn" class="btn btn-primary btn-lg">
                        Start Building
                    </a>
                    <a href="https://github.com/anchor/anchor" class="btn btn-secondary btn-lg">
                        View Source
                    </a>
                </div>
                <div class="mt-4">
                    <a href="/auth/login" class="text-muted text-decoration-none small">
                        Already have an account? Login
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <div class="footer text-center py-5 border-top" style="border-color: var(--border-color);">
            <div class="mb-4">
                <img width="40" height="40" src="<?= assets('img/logo.png') ?>" alt="Anchor Logo" style="opacity: 0.5; filter: grayscale(1);">
            </div>
            <p style="color: var(--text-tertiary);">
                &copy; <?= date('Y') ?> Anchor Framework. Open Source MIT License.
            </p>
        </div>

    </div>
</body>

</html>