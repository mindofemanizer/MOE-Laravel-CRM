<?php
declare(strict_types=1);

namespace Moe\CRM\Services;

use Moe\Core\Base\BaseService;
use Moe\CRM\Models\Contact;
use Moe\CRM\Models\Lead;

class LeadService extends BaseService
{
    /**
     * Create lead from contact.
     */
    public function createFromContact(int $contactId, array $data = []): Lead
    {
        $contact = Contact::findOrFail($contactId);

        return Lead::create(array_merge([
            'contact_id' => $contactId,
            'status' => 'new',
            'source' => $data['source'] ?? 'other',
            'score' => $data['score'] ?? 0,
            'expected_value' => $data['expected_value'] ?? 0,
            'assigned_to' => $data['assigned_to'] ?? null,
            'notes' => $data['notes'] ?? null,
        ], $data));
    }

    /**
     * Move lead to next stage.
     */
    public function advanceStage(int $leadId, ?string $notes = null): Lead
    {
        $lead = Lead::findOrFail($leadId);
        $stages = Lead::STAGES;
        $currentIndex = array_search($lead->status, $stages);

        if ($currentIndex === false || $currentIndex >= count($stages) - 1) {
            throw new \Exception('Cannot advance lead from current stage');
        }

        // Skip 'won' and 'lost' — those are terminal
        $nextIndex = $currentIndex + 1;
        if ($stages[$nextIndex] === 'won' || $stages[$nextIndex] === 'lost') {
            throw new \Exception('Use markAsWon or markAsLost for terminal stages');
        }

        $lead->update([
            'status' => $stages[$nextIndex],
            'notes' => $notes ? ($lead->notes . "\n" . $notes) : $lead->notes,
        ]);

        return $lead;
    }

    /**
     * Mark lead as won.
     */
    public function markAsWon(int $leadId): Lead
    {
        $lead = Lead::findOrFail($leadId);
        $lead->update([
            'status' => 'won',
            'converted_at' => now(),
        ]);

        return $lead;
    }

    /**
     * Mark lead as lost.
     */
    public function markAsLost(int $leadId, string $reason): Lead
    {
        $lead = Lead::findOrFail($leadId);
        $lead->update([
            'status' => 'lost',
            'lost_reason' => $reason,
        ]);

        return $lead;
    }

    /**
     * Get pipeline summary.
     */
    public function getPipelineSummary(): array
    {
        $summary = [];
        foreach (Lead::STAGES as $stage) {
            $summary[$stage] = Lead::where('status', $stage)->count();
        }
        return $summary;
    }
}
