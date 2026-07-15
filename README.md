# MOE-Laravel-CRM

CRM module for MOE ecosystem — Contact, Segment, Lead, Activity.

## Installation

```bash
composer require moe/laravel-crm
php artisan vendor:publish --provider="Moe\CRM\CRMServiceProvider" --tag="crm-config"
php artisan vendor:publish --provider="Moe\CRM\CRMServiceProvider" --tag="crm-migrations"
php artisan migrate
```

## What's Included

### Models

| Model | Table | Description |
|-------|-------|-------------|
| `Contact` | `crm_contacts` | CRM contacts (linked to User or standalone) |
| `Segment` | `crm_segments` | Customer segments / groups |
| `Lead` | `crm_leads` | Sales leads pipeline |
| `Activity` | `crm_activities` | Polymorphic activity log |

### Services

| Service | Description |
|---------|-------------|
| `ActivityService` | Log and query activities |
| `SegmentService` | Assign contacts to segments |
| `LeadService` | Lead pipeline management |

### Contracts

| Contract | Description |
|----------|-------------|
| `ContactInterface` | Contact data interface |
| `LeadInterface` | Lead pipeline interface |
| `SegmentableInterface` | Models that can be segmented |

## Usage

### Activity

```php
use Moe\CRM\Services\ActivityService;

$service = app(ActivityService::class);

// Log activity
$service->log($contact, 'call', 'Diskusi proposal', ['duration' => 30]);

// Get activities for a subject
$activities = $service->getForSubject($contact);

// Recent activities
$recent = $service->getRecent();
```

### Lead

```php
use Moe\CRM\Services\LeadService;

$service = app(LeadService::class);

// Create lead from contact
$lead = $service->createFromContact($contactId, ['source' => 'website']);

// Advance stage
$service->advanceStage($leadId);

// Mark as won/lost
$service->markAsWon($leadId);
$service->markAsLost($leadId, 'Harga terlalu tinggi');

// Pipeline summary
$summary = $service->getPipelineSummary();
```

### Segment

```php
use Moe\CRM\Services\SegmentService;

$service = app(SegmentService::class);

// Assign contact to segments
$service->assignToContact($contactId, [1, 2, 3]);

// Get contacts by segment
$contacts = $service->getContactsBySegment($segmentId);
```

## Requirements

- PHP ^8.2
- Laravel ^12.0|^13.0
- `moe/laravel-core`

## License

MIT
