<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invoice Item Model for line items in invoices.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Traits\HasRefid;

class InvoiceItem extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_invoice_item';

    protected array $fillable = [
        'refid',
        'invoice_id',
        'description',
        'amount',
        'quantity',
        'type',
        'metadata'
    ];

    protected array $casts = [
        'id' => 'integer',
        'amount' => 'integer',
        'quantity' => 'integer',
        'metadata' => 'json'
    ];
}
