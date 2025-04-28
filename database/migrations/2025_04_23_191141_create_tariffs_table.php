<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('tariffs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('regime_id')->constrained()->onDelete('cascade');
      $table->foreignId('room_id')->constrained()->onDelete('cascade');
      $table->date('start_date');
      $table->date('end_date');
      $table->enum('type', ['daily', 'package']);
      $table->decimal('value_room', 10, 2);
      $table->decimal('additional_adult', 10, 2)->nullable();
      $table->decimal('additional_child', 10, 2)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tariffs');
  }
};
