<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── VAGAS ───────────────────────────────────────────
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();            // ex: "São Paulo - SP"
            $table->string('tipo_contrato')->nullable();    // CLT, PJ, Temporário…
            $table->string('salario')->nullable();          // texto livre "A combinar" / "R$ 2.000"
            $table->text('requisitos')->nullable();
            $table->text('beneficios')->nullable();
            $table->string('slug')->unique();               // URL amigável
            $table->string('token', 64)->unique();          // token público do link
            $table->enum('status', ['aberta', 'fechada'])->default('aberta');
            $table->timestamp('data_limite')->nullable();
            $table->foreignId('user_id')->constrained()->comment('Quem criou a vaga');
            $table->timestamps();
        });

        // ─── PERGUNTAS DA VAGA ───────────────────────────────
        Schema::create('vaga_perguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaga_id')->constrained()->cascadeOnDelete();
            $table->string('pergunta');
            $table->enum('tipo', ['texto', 'textarea', 'select'])->default('texto');
            $table->json('opcoes')->nullable();   // para tipo=select
            $table->boolean('obrigatoria')->default(true);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });

        // ─── CANDIDATURAS ────────────────────────────────────
        Schema::create('vaga_candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaga_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone');
            $table->string('curriculo_path');              // caminho do arquivo PDF/Word
            $table->string('curriculo_nome_original');     // nome original do arquivo
            $table->json('respostas')->nullable();         // { pergunta_id: resposta }
            $table->enum('status', ['nova', 'analisando', 'aprovada', 'reprovada'])->default('nova');
            $table->text('observacoes')->nullable();       // notas internas do admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaga_candidaturas');
        Schema::dropIfExists('vaga_perguntas');
        Schema::dropIfExists('vagas');
    }
};
