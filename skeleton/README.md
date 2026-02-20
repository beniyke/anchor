# Anchor Framework

> Anchor is a modular, high-performance PHP framework built on a philosophy of self-reliance. It provides a full-featured, zero-bloat toolkit for modern applications, minimizing external dependencies in favor of integrated excellence.

## Philosophy

### Stability & Foundation

Just as an anchor provides stability to a ship, the **Anchor Framework** gives you a solid, reliable foundation to build upon. It keeps your application grounded with robust architecture, proven patterns, and production-ready features.

### Shipping Code

In software development, we don't just _deploy_ code, we **ship** it. Anchor is designed to help you confidently ship quality code to production. Every feature, from the ORM to the queue system, is built with production readiness in mind.

## Key Features

- **Module-Based Architecture**: Organize code by feature, not just file type.
- **Lightweight Core**: Fast request lifecycle with minimal overhead.
- **Powerful ORM**: Eloquent-like syntax for database interactions.
- **Convention over Configuration**: Sensible defaults to get you started quickly.
- **Built-in Tools**: CLI, Migrations, Queues, Mailer, and more.
- **Zero-Bloat**: Minimized external dependencies in favor of integrated, optimized solutions.

## Requirements

Anchor is designed for modern PHP environments. Ensure your system meets these requirements:

- **PHP**: >= 8.2
- **Database**: SQLite (default), MySQL 8.0+, or PostgreSQL 15+
- **Extensions**: PDO, Mbstring, OpenSSL, Ctype, JSON, **BCMath**, **cURL**, **ZipArchive**, **Tokenizer**, **fileinfo**
- **Composer**: Dependency Manager (for Managed Mode)

## Installation

Anchor provides two ways to build your application: **Managed** (via Composer) and **Standalone** (Portable).

### Create a New Project (Managed Mode)

The recommended way to start is with the **[Anchor Skeleton](https://github.com/beniyke/anchor-skeleton)**:

```bash
# Create project from skeleton
composer create-project beniyke/anchor-skeleton my-app

# Initialize the framework
cd my-app
php dock
```

Choosing the "Managed" option in the `dock` tool will provision the latest version of the framework.

### Environment Configuration

Copy the example environment file and configure your settings:

```bash
cp .env.example .env
```

### Database Initialization

Run the migrations to create your core application tables:

```bash
# Run database migrations
php dock migration:run
```

## Maintenance

Keep your framework core up to date with a single command:

```bash
php dock anchor:update
```

It intelligently handles both Managed (Composer) and Standalone (Hydrated) installations.

## Documentation

Full documentation is available in the [docs/](docs/) directory.

- **[Installation](docs/installation.md)** - Managed vs Standalone setups
- **[Introduction](docs/introduction.md)** - Core philosophy and metaphors
- **[Architecture](docs/architecture.md)** - How Anchor works under the hood
- **[Package Management](docs/package-management.md)** - Extending the framework

## License

The Anchor Framework is open-sourced software licensed under the [MIT license](LICENSE).
