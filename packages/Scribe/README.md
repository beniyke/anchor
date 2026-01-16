<!-- This file is auto-generated from docs/scribe.md -->

# Scribe (Blog)

Scribe is a professional blogging and content management package for the Anchor Framework. It provides a robust foundation for building feature-rich blogs with categories, tags, comments, and built-in SEO controls.

## Features

- **Fluent Post Building**: Expressive API for creating, updating, and scheduling posts.
- **Taxonomies**: Nested Categories and flat Tags for organized content.
- **SEO & Social Metadata**: Built-in support for Meta Titles, Descriptions, and Open Graph tags.
- **Publishing Workflows**: Drafts, Scheduled, and Published statuses.
- **Analytics**: Track post views and engagement trends.
- **Defensive Integration**: Gracefully works with or without optional packages like `Audit` and `Media`.

## Basic Usage

### Creating a Post

Use the `Scribe` facade to build a new post programmatically.

```php
use Scribe\Scribe;

$post = Scribe::post()
    ->title('The Future of Agentic Coding')
    ->content('Content goes here...')
    ->excerpt('A brief summary of the post.')
    ->category(5)
    ->tags([1, 2, 8])
    ->seo([
        'title' => 'Agentic Coding | Anchor Framework',
        'description' => 'Learn how AI agents are transforming software development.',
    ])
    ->status('published')
    ->create();
```

### Scheduling a Post

You can schedule posts for future publication using the `schedule` method.

```php
use Scribe\Scribe;
use Helpers\DateTimeHelper;

$post = Scribe::post()
    ->title('Upcoming Feature Announcement')
    ->content('Stay tuned...')
    ->status('scheduled')
    ->publishedAt(DateTimeHelper::now()->addDays(7))
    ->create();
```

### Handling Analytics

Scribe tracks post views automatically if you use the facade helper.

```php
use Scribe\Scribe;

Scribe::recordView($post, $userId, $sessionId);
```

Access trends via the analytics service:

```php
$topPosts = Scribe::analytics()->getTopPosts(5);
```

## Taxonomy Management

Scribe supports both hierarchical categories and tags.

### Categories

Categories can be nested to create complex content structures.

```php
// Coming soon: CategoryBuilder API
```

### Tags

Tags are flat taxonomies used for broad cross-referencing of posts.

## SEO Metadata

Every post includes a `seo_meta` JSON field. If not manually provided, Scribe can generate defaults based on the title and excerpt.

```php
$meta = Scribe::generateSeoMeta($post);
```

## Integration

- **Media**: Use the `Media` facade to handle featured images and post assets.
- **Audit**: Automatically logs publishing and scheduling events if installed.
- **Link**: Generate signed URLs for private or early-access post previews.

> [!NOTE]
> Scribe maintains strict **Architecture Isolation**. It never imports models from other packages, ensuring a modular and stable foundation.
