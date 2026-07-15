<?php
declare(strict_types=1);

namespace Moe\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Moe\CRM\Contracts\LeadInterface;

class Lead extends Model implements LeadInterface
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'contact_id',
        'status',
        'source',
        'score',
        'expected_value',
        'assigned_to',
        'notes',
        'converted_at',
        'lost_reason',
        'next_follow_up',
    ];

    protected $casts = [
        'score' => 'integer',
        'expected_value' => 'decimal:2',
        'converted_at' => 'datetime',
        'next_follow_up' => 'datetime',
    ];

    public const STAGES = [
        'new', 'qualified', 'proposal', 'negotiation', 'won', 'lost',
    ];

    public const SOURCES = [
        'website', 'referral', 'social_media', 'email', 'phone', 'walk_in', 'event', 'other',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crm.tables.leads', 'crm_leads');
    }

    /**
     * @return BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(config('crm.models.user', 'App\\Models\\User'), 'assigned_to');
    }

    /**
     * @return MorphMany
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getStage(): string
    {
        return $this->status;
    }

    public function getExpectedValue(): float
    {
        return (float) $this->expected_value;
    }
}
