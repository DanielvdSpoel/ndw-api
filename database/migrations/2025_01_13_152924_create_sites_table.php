<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('ndw_id')->unique()->index();
            $table->string('label')->nullable();
            $table->string('type')->nullable();
            $table->integer('version');
            $table->string('side')->nullable();
            $table->integer('lanes');
            $table->timestamp('version_time');
            $table->string('computation_method');
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
