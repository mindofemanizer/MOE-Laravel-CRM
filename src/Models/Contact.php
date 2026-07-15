<?php

namespace Moe\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Moe\CRM\Contracts\ContactInterface;

class Contact extends Model implements ContactInterface
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'position',
        'source',
        'notes',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crm.tables.contacts', 'crm_contacts');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('crm.models.user', 'App\\Models\\User'));
    }

    public function segments()
    {
        return $this->belongsToMany(
            Segment::class,
            config('crm.tables.contact_segment', 'crm_contact_segment')
        )->withTimestamps();
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
