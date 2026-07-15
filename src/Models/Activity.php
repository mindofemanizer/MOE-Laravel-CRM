<?php
declare(strict_types=1);

namespace Moe\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'type',
        'description',
        'metadata',
        'performed_by',
        'performed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'performed_at' => 'datetime',
    ];

    public const TYPES = [
        'note', 'email', 'call', 'meeting', 'order', 'support',
        'login', 'page_view', 'form_submit', 'custom',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crm.tables.activities', 'crm_activities');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(config('crm.models.user', 'App\\Models\\User'), 'performed_by');
    }
}
