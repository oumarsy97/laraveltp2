<?php

use App\Enums\EnumRole;
use App\Enums\EtatEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('login')->unique();
            $table->string('password');
            $table->enum('etat', [EtatEnum::ACTIF->value, EtatEnum::INACTIF->value])->default(EtatEnum::ACTIF->value);
            $table->unsignedBigInteger('role_id')->default(RoleEnum::CLIENT->value);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_dettes', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
};
