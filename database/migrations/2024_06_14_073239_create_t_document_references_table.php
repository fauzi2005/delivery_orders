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
        Schema::create('t_document_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained('t_delivery_orders')->onDelete('restrict');
            $table->string('document_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_document_references');
    }
};
