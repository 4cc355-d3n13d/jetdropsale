<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBalance extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('balance')->default(0)->after('deleted_at');
            });
            Schema::table('invoices', function (Blueprint $table) {
                $table->json('payment_structure')->nullable()->after('content');
                $table->json('user_balance_history_id')->nullable()->after('paid_with_card_id');
            });
            Schema::create('user_balance_history', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->comment('User reference');
                $table->decimal('balance_before')->default(0);
                $table->decimal('balance_diff')->default(0);
                $table->decimal('balance_after')->default(0);
                $table->morphs('initiator');
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_structure');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('user_balance_history_id');
        });
        Schema::dropIfExists('user_balance_log');
    }
}
