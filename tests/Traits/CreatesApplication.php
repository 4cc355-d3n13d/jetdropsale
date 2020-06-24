<?php

namespace Tests\Traits;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     */
    public function createApplication()
    {
        ini_set('memory_limit', '-1');
        /** @var Application $app */
        $app = require dirname(__DIR__) . '/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        if (method_exists($this, 'useSqlite')) {
            $this->useSqlite();
        }

        return $app;
    }
}
