<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoUsuario;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->enum('tipo', array_column(TipoUsuario::cases(), 'value'))
                  ->default(TipoUsuario::FUNCIONARIO->value);
            $table->string('departamento')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('email_verificado_em')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};