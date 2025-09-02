<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoBeneficio;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', array_column(TipoBeneficio::cases(), 'value'));
            $table->text('descricao');
            $table->decimal('valor_maximo', 10, 2)->nullable();
            $table->json('regras')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('requer_aprovacao_dupla')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficios');
    }
};