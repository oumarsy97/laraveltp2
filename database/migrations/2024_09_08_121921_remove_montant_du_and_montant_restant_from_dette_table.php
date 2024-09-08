<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMontantDuAndMontantRestantFromDetteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->dropColumn(['montantDu', 'montantRestant']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montantDu', 10, 2)->nullable();
            $table->decimal('montantRestant', 10, 2)->nullable();
        });
    }
}
