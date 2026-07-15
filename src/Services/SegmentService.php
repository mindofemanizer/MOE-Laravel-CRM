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
     *
     * @param int $contactId
     * @param array $segmentIds
     * @return \Moe\CRM\Models\Contact
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignToContact(int $contactId, array $segmentIds): Contact
    {
        $contact = Contact::findOrFail($contactId);
        $contact->segments()->syncWithoutDetaching($segmentIds);
        return $contact;
    }

    /**
     * Remove contact from segments.
     *
     * @param int $contactId
     * @param array $segmentIds
     * @return \Moe\CRM\Models\Contact
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function removeFromContact(int $contactId, array $segmentIds): Contact
    {
        $contact = Contact::findOrFail($contactId);
        $contact->segments()->detach($segmentIds);
        return $contact;
    }

    /**
     * Get contacts by segment.
     *
     * @param int $segmentId
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getContactsBySegment(int $segmentId): array
    {
        $segment = Segment::with('contacts')->findOrFail($segmentId);
        return $segment->contacts->all();
    }

    /**
     * Get segments for a contact.
     *
     * @param int $contactId
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getContactSegments(int $contactId): array
    {
        $contact = Contact::with('segments')->findOrFail($contactId);
        return $contact->segments->all();
    }
}
