<?php
declare(strict_types=1);

namespace Moe\CRM\Services;

use Moe\Core\Base\BaseService;
use Moe\CRM\Models\Contact;
use Moe\CRM\Models\Segment;

class SegmentService extends BaseService
{
    /**
     * Assign contact to segments.
     */
    public function assignToContact(int $contactId, array $segmentIds): Contact
    {
        $contact = Contact::findOrFail($contactId);
        $contact->segments()->syncWithoutDetaching($segmentIds);
        return $contact;
    }

    /**
     * Remove contact from segments.
     */
    public function removeFromContact(int $contactId, array $segmentIds): Contact
    {
        $contact = Contact::findOrFail($contactId);
        $contact->segments()->detach($segmentIds);
        return $contact;
    }

    /**
     * Get contacts by segment.
     */
    public function getContactsBySegment(int $segmentId): array
    {
        $segment = Segment::with('contacts')->findOrFail($segmentId);
        return $segment->contacts->all();
    }

    /**
     * Get segments for a contact.
     */
    public function getContactSegments(int $contactId): array
    {
        $contact = Contact::with('segments')->findOrFail($contactId);
        return $contact->segments->all();
    }
}
