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

- **PHP**: >= 8.2
- **Composer**: Dependency Manager
- **Database**: MySQL, PostgreSQL, or SQLite
- **Extensions**: PDO, Mbstring, OpenSSL, Ctype, JSON

## Installation

### Create a New Project

Clone the repository:

```bash
git clone https://github.com/beniyke/anchor my-app
cd my-app
```

### Environment Configuration

Copy the example environment file and configure your database credentials:

```bash
cp .env.example .env
```

### Initial Setup

Run the `dock` command to initialize your application:

```bash
php dock

# Run database migrations
php dock migration:run
```

## Documentation

Comprehensive documentation is available in the [docs](docs/) directory.

- [Introduction](docs/introduction.md)
- [Architecture](docs/architecture.md)
- [Getting Started](docs/onboard.md)

## License

The Anchor Framework is open-sourced software licensed under the [MIT license](LICENSE).
