<?php
declare(strict_types=1);

namespace Moe\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Segment extends Model
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'icon',
        'is_dynamic',
        'criteria',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_dynamic' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crm.tables.segments', 'crm_segments');
    }

    /**
     * @return BelongsToMany
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            config('crm.tables.contact_segment', 'crm_contact_segment')
        )->withTimestamps();
    }
}
