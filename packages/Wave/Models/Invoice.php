<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invoice Model for billing records.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Wave\Enums\InvoiceStatus;

/**
 * @property int                  $id
 * @property string               $refid
 * @property int                  $owner_id
 * @property string               $owner_type
 * @property ?int                 $subscription_id
 * @property InvoiceStatus|string $status
 * @property int                  $amount
 * @property int                  $tax
 * @property int                  $total
 * @property string               $currency
 * @property string               $invoice_number
 * @property ?array               $metadata
 * @property ?DateTimeHelper      $due_at
 * @property ?DateTimeHelper      $paid_at
 * @property ?DateTimeHelper      $created_at
 * @property ?DateTimeHelper      $updated_at
 * @property-read ModelCollection $items
 * @property-read ?Subscription $subscription
 *
 * @method static Builder open()
 * @method static Builder paid()
 * @method static Builder void()
 * @method static Builder uncollectible()
 * @method static Builder unpaid()
 * @method static Builder overdue()
 * @method static Builder owner(int $ownerId)
 */
class Invoice extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_invoice';

    protected array $fillable = [
        'refid',
        'owner_id',
        'owner_type',
        'subscription_id',
        'status',
        'amount',
        'tax',
        'total',
        'currency',
        'invoice_number',
        'due_at',
        'paid_at',
        'metadata'
    ];

    protected array $casts = [
        'id' => 'integer',
        'status' => InvoiceStatus::class,
        'amount' => 'integer',
        'tax' => 'integer',
        'total' => 'integer',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'json'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::OPEN);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeVoid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::VOID);
    }

    public function scopeUncollectible(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::UNCOLLECTIBLE);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::OPEN);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::OPEN)
            ->where('due_at', '<', DateTimeHelper::now());
    }

    public function scopeOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }
}
