<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vaga;
use App\Models\VagaCandidatura;
use App\Models\VagaPergunta;
use Illuminate\Support\Str;

class VagasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $curriculoPadrao = 'vagas/curriculos/27SXiDfj7G8YnEkmEEX4nUPja0NNDxHnudvHHu1P.pdf';
        
        $vagas = [
            [
                'titulo' => 'Mestre de Obras',
                'descricao' => 'Responsável por coordenar equipes de pedreiros, serventes e carpinteiros. Leitura de projetos e gestão de cronograma.',
                'requisitos' => "Experiência mínima de 5 anos em obras residenciais\nLeitura técnica de projetos\nLiderança de equipe",
                'diferenciais' => 'Curso técnico em edificações',
                'beneficios' => 'Vale transporte, Vale alimentação, Plano de saúde',
                'local' => 'Aracaju - SE',
                'salario' => 'R$ 4.500,00',
                'status' => 'aberta',
                'user_id' => 1
            ],
            [
                'titulo' => 'Técnico em Segurança do Trabalho',
                'descricao' => 'Fiscalização de uso de EPIs, treinamentos de segurança e elaboração de relatórios.',
                'requisitos' => "Curso Técnico completo\nRegistro no MTE ativo\nDisponibilidade para viagens",
                'diferenciais' => 'Conhecimento em e-Social',
                'beneficios' => 'Seguro de vida, Carro da empresa',
                'local' => 'Nossa Senhora do Socorro - SE',
                'salario' => 'R$ 3.200,00',
                'status' => 'aberta',
                'user_id' => 1
            ],
            [
                'titulo' => 'Auxiliar Administrativo de Obra',
                'descricao' => 'Controle de ponto, recebimento de materiais e organização de documentos da obra.',
                'requisitos' => "Ensino médio completo\nPacote Office básico\nOrganização",
                'diferenciais' => 'Experiência em almoxarifado',
                'beneficios' => 'Vale refeição',
                'local' => 'Aracaju - SE',
                'salario' => 'R$ 1.800,00',
                'status' => 'aberta',
                'user_id' => 1
            ]
        ];

        $nomes = ['Marcos Silva', 'André Oliveira', 'Juliana Santos', 'Felipe Costa', 'Bruna Lima', 'Ricardo Mendes', 'Camila Rocha', 'Tiago Souza', 'Patrícia Alencar', 'Leonardo Ferreira'];

        foreach ($vagas as $vData) {
            $vaga = Vaga::create($vData);

            // Adiciona uma pergunta padrão
            VagaPergunta::create([
                'vaga_id' => $vaga->id,
                'pergunta' => 'Você possui disponibilidade para início imediato?',
                'tipo' => 'select',
                'opcoes' => ['Sim', 'Não'],
                'obrigatoria' => true,
                'ordem' => 0
            ]);

            // Cria 5 candidatos para cada vaga
            $candidatosVaga = collect($nomes)->random(5);
            
            foreach ($candidatosVaga as $nome) {
                $score = rand(30, 95);
                $status = $score > 80 ? 'aprovada' : ($score < 50 ? 'reprovada' : 'nova');
                
                VagaCandidatura::create([
                    'vaga_id' => $vaga->id,
                    'nome' => $nome,
                    'email' => strtolower(Str::slug($nome)) . '@email.com',
                    'telefone' => '(79) 9' . rand(8888, 9999) . '-' . rand(1111, 9999),
                    'curriculo_path' => $curriculoPadrao,
                    'curriculo_nome_original' => 'Curriculo_' . Str::slug($nome) . '.pdf',
                    'respostas' => [1 => 'Sim'],
                    'status' => $status,
                    'ai_status' => 'completed',
                    'ai_score' => $score,
                    'ai_summary' => "Candidato apresenta perfil compatível para a função de {$vaga->titulo}. Possui as competências básicas exigidas e demonstra interesse na área.",
                    'ai_pontos_fortes' => "Experiência prévia relevante; Boa comunicação; Disponibilidade.",
                    'ai_pontos_fracos' => "Faltam certificações específicas; Experiência em grandes obras limitada."
                ]);
            }
        }
    }
}
