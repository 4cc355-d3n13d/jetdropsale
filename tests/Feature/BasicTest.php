<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class BasicTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    /**
     * A basic test example.
     */
    public function testBasicTest(): void
    {
        $this
            ->visit('/catalog')
            ->see('Catalog')
            //->see('Resources')
            //->see('Help')
            //->see('Contact us')
        ;

        $this->assertResponseStatus(200);
    }
}
