# Anchor Framework Skeleton

> The clean, stable starting point for your next high-performance PHP application.

This skeleton provides the essential directory structure and entry points (`index.php`, `dock`) to build a modern application using the **Anchor Framework**.

## Quick Start

Anchor supports two primary installation workflows. Both start by running the `dock` tool.

### Managed Mode (Recommended)

Standard composer-based setup for modern development.

```bash
composer create-project beniyke/anchor-skeleton my-app
cd my-app
php dock
```

### Standalone Mode (Portable)

Zero-dependency, portable setup. Download this repository and run:

```bash
php dock
```

## Setup & Initialization

After the initial installation, complete these steps to prepare your application:

### Environment Configuration

Copy the example environment file and configure your application settings (App Name, URL, Database, etc.):

```bash
cp .env.example .env
```

### Database Initialization

Run the core migrations to create essential application tables:

```bash
php dock migration:run
```

## Maintenance

Keep your framework core and dependencies up to date with a single command:

```bash
php dock anchor:update
```

It intelligently handles both Managed (Composer) and Standalone (Hydrated) installations.

## Core Requirements

- **PHP**: >= 8.2
- **Database**: SQLite (default), MySQL 8.0+, or PostgreSQL 15+
- **Extensions**: PDO, Mbstring, OpenSSL, Ctype, JSON, BCMath, cURL, ZipArchive

## Documentation

Full documentation and guides are available on the official repository:

- [Installation Guide](https://github.com/beniyke/anchor/blob/master/docs/installation.md)
- [Architecture Overview](https://github.com/beniyke/anchor/blob/master/docs/architecture.md)
- [Package Management](https://github.com/beniyke/anchor/blob/master/docs/package-management.md)

## License

Open-sourced software licensed under the [MIT license](LICENSE).
