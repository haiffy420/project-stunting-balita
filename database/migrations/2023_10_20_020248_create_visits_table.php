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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baby_id')->constrained()->cascadeOnDelete();
            $table->integer('kunjungan');
            $table->date('tanggal_kunjungan');
            $table->decimal('berat_badan');
            $table->decimal('tinggi_badan');
            $table->decimal('lingkar_lengan')->nullable();
            $table->decimal('lingkar_kepala');
            $table->decimal('suhu_badan');
            $table->string('penyakit')->nullable();
            $table->string('keluhan')->nullable();
            $table->json('subform1')->nullable();
            $table->json('subform2')->nullable();
            $table->json('subform3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
