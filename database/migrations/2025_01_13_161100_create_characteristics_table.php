<?php

use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('characteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class)->constrained()->cascadeOnDelete();
//            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->integer('index');
            $table->float('accuracy');
            $table->integer('period');
            $table->string('lane')->nullable();
            $table->string('type');
            $table->json('conditions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
