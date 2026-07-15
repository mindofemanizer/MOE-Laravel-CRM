<?php

namespace Moe\CRM\Listeners;

use Moe\Commerce\Events\OrderPlaced;
use Moe\Commerce\Events\OrderStatusChanged;
use Moe\CRM\Models\Activity;
use Moe\CRM\Models\Contact;

class LogCustomerActivityOnOrderEvent
{
    public function handleOrderPlaced(OrderPlaced $event): void
    {
        $contact = Contact::where('user_id', $event->order->user_id)->first();
        if (! $contact) {
            return;
        }

        Activity::create([
            'subject_type' => $contact->getMorphClass(),
            'subject_id' => $contact->id,
            'type' => 'order',
            'description' => "Order dibuat: {$event->order->order_number} (Rp " . number_format((float) $event->order->total, 0, ',', '.') . ')',
            'metadata' => [
                'order_id' => $event->order->id,
                'order_number' => $event->order->order_number,
                'total' => (float) $event->order->total,
            ],
            'performed_by' => $event->order->user_id,
            'performed_at' => now(),
        ]);
    }

    public function handleOrderCompleted(OrderStatusChanged $event): void
    {
        if ($event->newStatus !== 'completed') {
            return;
        }

        $contact = Contact::where('user_id', $event->order->user_id)->first();
        if (! $contact) {
            return;
        }

        Activity::create([
            'subject_type' => $contact->getMorphClass(),
            'subject_id' => $contact->id,
            'type' => 'order',
            'description' => "Order selesai: {$event->order->order_number}",
            'metadata' => [
                'order_id' => $event->order->id,
                'order_number' => $event->order->order_number,
                'status' => 'completed',
            ],
            'performed_by' => $event->order->user_id,
            'performed_at' => now(),
        ]);
    }
}
