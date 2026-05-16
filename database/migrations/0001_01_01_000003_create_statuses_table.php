<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // slug: new, in_progress, done...
            $table->string('label');                    // Отображаемое название
            $table->string('color')->default('gray');   // tailwind: gray, blue, green, red, yellow...
            $table->integer('order')->default(0);
            $table->boolean('is_final')->default(false); // завершённые статусы (done, rejected)
            $table->json('meta')->nullable();           // для будущих настроек
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
