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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('document_number', 20)->unique();
            $table->string('legal_name', 100)->comment('(Razón social)');
            $table->string('business_name', 100)->nullable()->comment('(Nombre comercial)');

            $table->index(['document_type_id', 'document_number']);
            $table->index('business_name');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
