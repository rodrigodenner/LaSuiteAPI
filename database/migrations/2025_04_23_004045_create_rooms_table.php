<?php

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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug',150)->unique();
            $table->boolean('featured')->default(false);
            $table->text('description');
            $table->string('size');
            $table->integer('max_adults');
            $table->integer('max_children');
            $table->integer('double_beds');
            $table->integer('single_beds');
            $table->string('floor');
            $table->string('type');
            $table->string('number');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
