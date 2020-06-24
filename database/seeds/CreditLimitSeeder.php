<?php

use App\Models\CreditLimit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('credit_limits')->delete();

        CreditLimit::create(['limit' => '20']);
    }
}
