<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;

trait UsesSqlite
{
    public function useSqlite()
    {
        DB::setDefaultConnection('sqlite');
    }
}
