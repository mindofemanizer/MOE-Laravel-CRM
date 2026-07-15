<?php
declare(strict_types=1);

namespace Moe\CRM\Listeners;

use Illuminate\Support\Facades\Log;
use Moe\CRM\Models\Activity;
use Moe\CRM\Models\Contact;

class LogCustomerActivityOnOrderEvent
{
    /**
     * @param object $event
     * @return void
     */
    public function handleOrderPlaced(object $event): void
    {
        try {
            $orderPlacedClass = 'Moe\Commerce\Events\OrderPlaced';
            if (!class_exists($orderPlacedClass) || !$event instanceof $orderPlacedClass) {
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
                'description' => "Order dibuat: {$event->order->order_number} (Rp " . number_format((float) $event->order->total, 0, ',', '.') . ')',
                'metadata' => [
                    'order_id' => $event->order->id,
                    'order_number' => $event->order->order_number,
                    'total' => (float) $event->order->total,
                ],
                'performed_by' => $event->order->user_id,
                'performed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[crm] LogCustomerActivityOnOrderEvent::handleOrderPlaced failed: '.$e->getMessage(), [
                'order_id' => $event->order?->id,
            ]);
        }
    }

    /**
     * @param object $event
     * @return void
     */
    public function handleOrderCompleted(object $event): void
    {
        try {
            $orderStatusChangedClass = 'Moe\Commerce\Events\OrderStatusChanged';
            if (!class_exists($orderStatusChangedClass) || !$event instanceof $orderStatusChangedClass) {
                return;
            }

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
        } catch (\Throwable $e) {
            Log::error('[crm] LogCustomerActivityOnOrderEvent::handleOrderCompleted failed: '.$e->getMessage(), [
                'order_id' => $event->order?->id,
            ]);
        }
    }
}
