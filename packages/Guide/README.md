<!-- This file is auto-generated from docs/guide.md -->

# Guide (FAQ/Help Center)

The **Guide** package provides a robust FAQ and knowledge base system for the Anchor Framework. It features hierarchical categories, rich media support, full-text search, and helpfulness analytics.

## Core Capabilities

- **Hierarchical Categories**: Organize articles into nested categories.
- **Rich Media**: Attach images and videos to help articles.
- **Full-text Search**: Find articles quickly with built-in search analytics.
- **Helpfulness Feedback**: Collect user ratings and comments on articles.
- **Automatic Audit**: Logs article views and management actions.

## Installation

```bash
php dock package:install Guide --packages
```

## Basic Usage

### Creating Categories

```php
use Guide\Guide;

// Create a parent category
$billing = Guide::category()
    ->name('Billing & Payments')
    ->description('Everything related to your invoices and plans.')
    ->save();

// Create a sub-category
Guide::category()
    ->name('Refunds')
    ->parent($billing)
    ->save();
```

### Managing Articles

```php
// Create a published article
$article = Guide::article()
    ->title('How to Update Your Card')
    ->content('Go to Settings > Billing and click "Update Card"...')
    ->category($billing)
    ->status('published')
    ->save();
```

### Searching Articles

```php
$results = Guide::search('payment');

foreach ($results as $article) {
    echo $article->title;
}
```

### Search with Filters & Metadata

```php
$results = Guide::search('refund', [
    'category' => $billing->id
], [
    'ip' => request()->ip(),
    'user_agent' => request()->header('User-Agent')
]);
```

## Analytics & Feedback

### Recording Views

```php
Guide::analytics()->recordView($article);
```

### Submitting Feedback

```php
Guide::analytics()->submitFeedback(
    $article,
    rating: 5,
    comment: 'Very helpful!'
);
```

### Popular Articles

```php
$popular = Guide::analytics()->getPopularArticles(limit: 5);
```

## Integrations

### Media Attachments

```php
// Assuming $mediaId from Media package
Guide::attachMedia($article, $mediaId, type: 'featured');
```

### Related Articles

```php
Guide::relateArticles($article, $anotherArticle);
```

## Configuration

Configuration is located in `App/Config/guide.php`:

```php
return [
    'search' => [
        'limit' => 10,
        'log_enabled' => true,
    ],
    'feedback' => [
        'enabled' => true,
    ],
];
```
