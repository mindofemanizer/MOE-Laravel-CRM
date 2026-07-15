<?php

namespace Moe\CRM\Tests;

use Moe\CRM\Models\Contact;
use Moe\CRM\Models\Segment;
use Moe\CRM\Services\ActivityService;
use Moe\CRM\Services\LeadService;
use Moe\CRM\Services\SegmentService;

class CRMServiceTest extends TestCase
{
    private ActivityService $activityService;
    private LeadService $leadService;
    private SegmentService $segmentService;
    private Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contact = Contact::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'source' => 'website',
            'is_active' => true,
        ]);

        $this->activityService = new ActivityService();
        $this->leadService = new LeadService();
        $this->segmentService = new SegmentService();
    }

    public function test_can_log_activity()
    {
        $activity = $this->activityService->record($this->contact, 'call', 'Telpon diskusi');

        $this->assertNotNull($activity->id);
        $this->assertEquals('call', $activity->type);
    }

    public function test_can_create_lead()
    {
        $lead = $this->leadService->createFromContact($this->contact->id, [
            'source' => 'website',
            'expected_value' => 1000000,
        ]);

        $this->assertEquals('new', $lead->status);
        $this->assertEquals(1000000, (int) $lead->expected_value);
    }

    public function test_can_advance_lead_stage()
    {
        $lead = $this->leadService->createFromContact($this->contact->id);

        $this->leadService->advanceStage($lead->id);
        $this->assertEquals('qualified', $lead->fresh()->status);
    }

    public function test_can_mark_lead_as_won()
    {
        $lead = $this->leadService->createFromContact($this->contact->id);
        $this->leadService->markAsWon($lead->id);

        $this->assertEquals('won', $lead->fresh()->status);
        $this->assertNotNull($lead->fresh()->converted_at);
    }

    public function test_can_mark_lead_as_lost()
    {
        $lead = $this->leadService->createFromContact($this->contact->id);
        $this->leadService->markAsLost($lead->id, 'Tidak tertarik');

        $this->assertEquals('lost', $lead->fresh()->status);
        $this->assertEquals('Tidak tertarik', $lead->fresh()->lost_reason);
    }

    public function test_can_create_segment_and_assign_contact()
    {
        $segment = Segment::create([
            'name' => 'VIP Customer',
            'code' => 'vip',
            'is_active' => true,
        ]);

        $this->segmentService->assignToContact($this->contact->id, [$segment->id]);

        $contactSegments = $this->segmentService->getContactSegments($this->contact->id);
        $this->assertCount(1, $contactSegments);
        $this->assertEquals('VIP Customer', $contactSegments[0]->name);
    }
}
