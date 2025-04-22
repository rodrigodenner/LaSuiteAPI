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
    Schema::create('phones', function (Blueprint $table) {
      $table->id();
      $table->foreignId('guest_id')->constrained('guests');
      $table->string('phone_number')->unique();
      $table->string('phone_type');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('phones');
  }
};
