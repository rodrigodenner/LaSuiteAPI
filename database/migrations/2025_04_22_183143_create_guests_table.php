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
    Schema::create('guests', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->date('birthday');
      $table->string('cpf')->unique()->nullable();
      $table->string('rg')->unique()->nullable();
      $table->string('passport')->unique()->nullable();
      $table->boolean('is_foreigner')->default(false);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('guests');
  }
};
