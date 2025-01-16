<?php

use App\Models\Characteristic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('value')->index();
            $table->foreignIdFor(Characteristic::class)->constrained()->cascadeOnDelete();
            $table->timestamp('timestamp');
            $table->timestamps();
            $table->unique(['characteristic_id', 'timestamp', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
