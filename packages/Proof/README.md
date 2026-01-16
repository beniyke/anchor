<!-- This file is auto-generated from docs/proof.md -->

# Proof Package

The **Proof** package handles Testimonials and Case Studies, providing a robust framework for social proof collection, management, and display.

## Installation

The package is pre-installed in the Anchor Framework. Configuration is available in `Config/proof.php`.

## Core Concepts

### Testimonials

Customer quotes, ratings, and video testimonials.

### Case Studies

In-depth success stories with multiple sections (Problem, Solution, Results) and measurable metrics.

### Sources

The entities (people or companies) that provide the social proof.

## Usage

### Managing Testimonials

#### Creating a Testimonial (Fluent API)

```php
use Proof\Proof;

$testimonial = Proof::testimonial()
    ->source($sourceId)
    ->content('Anchor has transformed our workflow!')
    ->rating(5)
    ->verified()
    ->save();
```

#### Approving a Testimonial

```php
Proof::approve($testimonialId);
```

### Managing Case Studies

#### Creating a Case Study

```php
$caseStudy = Proof::caseStudy()
    ->source($sourceId)
    ->title('Scaling with Anchor')
    ->slug('scaling-with-anchor')
    ->summary('How we achieved 300% growth.')
    ->save();

// Adding sections
$caseStudy->sections()->create([
    'title' => 'The Challenge',
    'content' => 'We were struggling with legacy systems...',
    'order' => 1
]);

// Adding metrics
$caseStudy->metrics()->create([
    'label' => 'Growth',
    'value' => '300',
    'suffix' => '%'
]);
```

### Integrations

#### Form Integration (Stack)

Automatically convert form submissions into testimonials.

```php
use Proof\Proof;
use Stack\Models\Submission;

Proof::fromSubmission($submission, [
    'content' => 'feedback_text',
    'rating' => 'star_rating',
    'name' => 'customer_name'
]);
```

#### Secure Collection Requests

Generate secure, expiring links for customers to submit their proof.

```php
$request = Proof::request($source);
$url = Proof::collectionUrl($request);
```

#### Media Integration

Attach photos or videos from the Media package.

```php
Proof::attachMedia($testimonial, $mediaId, 'photo');
```

## Configuration

Available in `packages/Proof/Config/proof.php`:

- `form_integration`: Enable/disable automatic form conversion.
- `approval.required`: Whether testimonials need approval before display.
- `request.expiry_days`: Duration for secure collection links.

## Analytics

Track views and engagement:

```php
Proof::analytics()->recordView($testimonial, ['ip' => request()->ip()]);
```
