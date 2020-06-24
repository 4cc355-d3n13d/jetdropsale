<?php

namespace App\Listeners;

use App\Models\NeedAuditWhenCreated;
use OwenIt\Auditing\Events\Auditing;

/**
 * Class AuditingListener
 */
class AuditingListener
{
    /**
     * Handle the Auditing event.
     */
    public function handle(Auditing $event): bool
    {
        // Disable new audit event when any model was created
        if ('created' === $event->model->getAuditEvent() && ! ($event->model instanceof NeedAuditWhenCreated)) {
            return false;
        }


        return true;
    }
}
