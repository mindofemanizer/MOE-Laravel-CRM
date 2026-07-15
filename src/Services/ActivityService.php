<?php
declare(strict_types=1);

namespace Moe\CRM\Services;

use Illuminate\Database\Eloquent\Model;
use Moe\Core\Base\BaseService;
use Moe\CRM\Models\Activity;

class ActivityService extends BaseService
{
    /**
     * Record an activity.
     *
     * @param \Illuminate\Database\Eloquent\Model $subject
     * @param string $type
     * @param string $description
     * @param array|null $metadata
     * @param int|null $performedBy
     * @return \Moe\CRM\Models\Activity
     */
    public function record(
        Model $subject,
        string $type,
        string $description,
        ?array $metadata = null,
        ?int $performedBy = null,
    ): Activity {
        $activity = new Activity([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'performed_by' => $performedBy ?? auth()->id(),
            'performed_at' => now(),
        ]);
        $activity->save();

        return $activity;
    }

    /**
     * Get activities for a subject (Contact, Lead, etc.).
     *
     * @param \Illuminate\Database\Eloquent\Model $subject
     * @param string|null $type
     * @param int $limit
     * @return array
     */
    public function getForSubject(Model $subject, ?string $type = null, int $limit = 50): array
    {
        $query = Activity::where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());

        if ($type) {
            $query->where('type', $type);
        }

        return $query->latest('performed_at')->limit($limit)->get()->all();
    }

    /**
     * Get recent activities across all subjects.
     *
     * @param int $limit
     * @return array
     */
    public function getRecent(int $limit = 20): array
    {
        return Activity::latest('performed_at')->limit($limit)->get()->all();
    }
}
