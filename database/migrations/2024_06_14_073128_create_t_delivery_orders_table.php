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
        Schema::create('t_delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('date');
            $table->foreignId('destination_id')->constrained('m_delivery_destinations')->onDelete('restrict');
            $table->integer('total');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_delivery_orders');
    }
};
