# Anchor Framework Skeleton

> The clean, stable starting point for your next high-performance PHP application.

This skeleton provides the essential directory structure and entry points (`index.php`, `dock`) to build a modern application using the **Anchor Framework**.

## Quick Start

Anchor supports two primary installation workflows. Both start by running the `dock` tool.

### 1. Managed Mode (Recommended)

Standard composer-based setup for modern development.

```bash
composer create-project beniyke/anchor-skeleton my-app
cd my-app
php dock
```

### 2. Standalone Mode (Portable)

Zero-dependency, portable setup. Download this repository and run:

```bash
php dock
```

## Core Requirements

- **PHP**: >= 8.2
- **Database**: SQLite (default), MySQL 8.0+, or PostgreSQL 15+
- **Extensions**: PDO, Mbstring, OpenSSL, Ctype, JSON, BCMath, cURL, ZipArchive

## Documentation

Comprehensive guides are available on the official repository:

- [Installation Guide](https://github.com/beniyke/anchor/blob/master/docs/installation.md)
- [Architecture Overview](https://github.com/beniyke/anchor/blob/master/docs/architecture.md)
- [Package Management](https://github.com/beniyke/anchor/blob/master/docs/package-management.md)

## License

Open-sourced software licensed under the [MIT license](LICENSE).
