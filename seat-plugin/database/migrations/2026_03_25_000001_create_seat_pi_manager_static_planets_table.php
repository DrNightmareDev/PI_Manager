<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seat_pi_manager_static_planets', function (Blueprint $table) {
            $table->unsignedBigInteger('planet_id')->primary();
            $table->unsignedBigInteger('system_id')->index();
            $table->string('planet_name');
            $table->string('planet_number', 32)->nullable();
            $table->unsignedBigInteger('radius')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_pi_manager_static_planets');
    }
};
