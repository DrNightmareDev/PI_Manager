<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seat_pi_manager_import_runs', function (Blueprint $table) {
            $table->id();
            $table->string('import_type', 64)->index();
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedInteger('records_processed')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_pi_manager_import_runs');
    }
};
