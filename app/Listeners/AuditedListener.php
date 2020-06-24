<?php

namespace App\Listeners;

use OwenIt\Auditing\Events\Audited;

/**
 * Class AuditedListener
 */
class AuditedListener
{
    /**
     * Handle the Audited event.
     */
    public function handle(Audited $event): void
    {
        // Implement logic
    }
}
