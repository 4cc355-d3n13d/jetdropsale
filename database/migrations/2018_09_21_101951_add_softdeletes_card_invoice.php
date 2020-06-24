<?php /** @noinspection ALL */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftdeletesCardInvoice extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedInteger('payed_with_card_id')->nullable();
            $table->timestamp('payed_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->removeColumn('deleted_at');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->removeColumn('payed_with_card_id');
            $table->removeColumn('payed_at');
            $table->removeColumn('deleted_at');
        });
    }
}
