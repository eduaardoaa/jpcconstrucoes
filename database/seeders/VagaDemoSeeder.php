<?php

namespace Database\Seeders;

use App\Models\Vaga;
use App\Models\VagaCandidatura;
use App\Models\VagaPergunta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VagaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $vagasData = [
            [
                'titulo' => 'Pedreiro Experiente',
                'descricao' => "Estamos buscando pedreiros com experiência em alvenaria estrutural e de vedação.\nNecessário conhecimento em leitura de projetos e acabamentos de qualidade.",
                'local' => 'São Paulo - SP',
                'tipo_contrato' => 'CLT',
                'salario' => 'R$ 3.200,00',
                'requisitos' => "- Mínimo 3 anos de experiência\n- Conhecimento em alvenaria estrutural\n- Leitura básica de projetos\n- Disponibilidade para hora extra",
                'beneficios' => "- Vale transporte\n- Vale refeição\n- Cesta básica\n- Seguro de vida",
                'status' => 'aberta',
                'perguntas' => [
                    ['pergunta' => 'Quantos anos de experiência você tem como pedreiro?', 'tipo' => 'select', 'opcoes' => ['1 a 2 anos', '3 a 5 anos', '5 a 10 anos', 'Mais de 10 anos']],
                    ['pergunta' => 'Você tem disponibilidade para trabalhar aos sábados?', 'tipo' => 'select', 'opcoes' => ['Sim', 'Não', 'Eventualmente']],
                    ['pergunta' => 'Possui algum curso na área de construção civil?', 'tipo' => 'textarea'],
                ],
                'candidatos' => 18,
            ],
            [
                'titulo' => 'Mestre de Obras',
                'descricao' => "Procuramos mestre de obras para coordenar equipe em obra residencial de alto padrão.\nResponsável pela gestão de equipe, controle de qualidade e cumprimento de prazos.",
                'local' => 'Guarulhos - SP',
                'tipo_contrato' => 'CLT',
                'salario' => 'R$ 5.500,00',
                'requisitos' => "- Experiência mínima de 5 anos como mestre de obras\n- Gestão de equipes de 20+ pessoas\n- Conhecimento em NR-18\n- CNH categoria B",
                'beneficios' => "- Vale transporte\n- Vale refeição R$ 30/dia\n- Plano de saúde\n- PLR semestral",
                'status' => 'aberta',
                'perguntas' => [
                    ['pergunta' => 'Qual o maior número de funcionários que você já gerenciou?', 'tipo' => 'texto'],
                    ['pergunta' => 'Possui CNH?', 'tipo' => 'select', 'opcoes' => ['Sim - Cat. A', 'Sim - Cat. B', 'Sim - Cat. AB', 'Não possuo']],
                    ['pergunta' => 'Descreva sua última experiência como mestre de obras', 'tipo' => 'textarea'],
                ],
                'candidatos' => 12,
            ],
            [
                'titulo' => 'Eletricista Predial',
                'descricao' => "Vaga para eletricista predial com conhecimento em instalações elétricas residenciais e comerciais.",
                'local' => 'Osasco - SP',
                'tipo_contrato' => 'PJ',
                'salario' => 'R$ 3.800,00',
                'requisitos' => "- Curso técnico em eletrotécnica\n- NR-10 atualizada\n- Experiência com instalações prediais",
                'beneficios' => "- Ajuda de custo para transporte\n- Ferramentas fornecidas pela empresa",
                'status' => 'aberta',
                'perguntas' => [
                    ['pergunta' => 'Possui NR-10 atualizada?', 'tipo' => 'select', 'opcoes' => ['Sim', 'Não', 'Vencida']],
                    ['pergunta' => 'Tem experiência com quadros de distribuição?', 'tipo' => 'select', 'opcoes' => ['Sim', 'Não']],
                ],
                'candidatos' => 8,
            ],
            [
                'titulo' => 'Auxiliar de Obras',
                'descricao' => "Oportunidade para auxiliar de obras em canteiro na zona sul de SP.\nNão é necessária experiência prévia, oferecemos treinamento.",
                'local' => 'São Paulo - Zona Sul',
                'tipo_contrato' => 'CLT',
                'salario' => 'R$ 1.800,00',
                'requisitos' => "- Ensino fundamental completo\n- Disponibilidade para início imediato\n- Proatividade e vontade de aprender",
                'beneficios' => "- Vale transporte\n- Almoço no local\n- Cesta básica",
                'status' => 'aberta',
                'perguntas' => [
                    ['pergunta' => 'Você tem disponibilidade para início imediato?', 'tipo' => 'select', 'opcoes' => ['Sim', 'Não', 'Em 15 dias']],
                ],
                'candidatos' => 25,
            ],
            [
                'titulo' => 'Engenheiro Civil - Obras',
                'descricao' => "Buscamos engenheiro civil para acompanhamento de obras residenciais multifamiliares.",
                'local' => 'São Paulo - SP',
                'tipo_contrato' => 'CLT',
                'salario' => 'R$ 9.000,00',
                'requisitos' => "- Graduação em Engenharia Civil\n- CREA ativo\n- Experiência com obras residenciais\n- Conhecimento em MS Project",
                'beneficios' => "- Plano de saúde e odontológico\n- VR R$ 40/dia\n- Celular corporativo\n- PLR anual",
                'status' => 'fechada',
                'perguntas' => [
                    ['pergunta' => 'Possui CREA ativo?', 'tipo' => 'select', 'opcoes' => ['Sim', 'Não']],
                    ['pergunta' => 'Anos de experiência em obras', 'tipo' => 'select', 'opcoes' => ['1 a 3 anos', '3 a 5 anos', '5 a 10 anos', 'Mais de 10 anos']],
                ],
                'candidatos' => 6,
            ],
        ];

        $nomes = [
            'Carlos Silva', 'Maria Oliveira', 'João Santos', 'Ana Souza', 'Pedro Lima',
            'Fernanda Costa', 'Lucas Pereira', 'Juliana Almeida', 'Rafael Ferreira', 'Patricia Rocha',
            'Bruno Nascimento', 'Camila Ribeiro', 'Diego Martins', 'Tatiane Gomes', 'Marcos Araújo',
            'Vanessa Barbosa', 'Gustavo Carvalho', 'Letícia Mendes', 'Roberto Dias', 'Daniela Moreira',
            'Thiago Correia', 'Amanda Lopes', 'Felipe Rodrigues', 'Bianca Nunes', 'Eduardo Vieira',
            'Isabela Castro', 'Matheus Cardoso', 'Larissa Monteiro', 'Henrique Pinto', 'Gabriela Teixeira',
        ];

        $statuses = ['nova', 'nova', 'nova', 'analisando', 'analisando', 'aprovada', 'reprovada'];
        $nomeIdx = 0;

        foreach ($vagasData as $vd) {
            $vaga = Vaga::create([
                'titulo' => $vd['titulo'],
                'descricao' => $vd['descricao'],
                'local' => $vd['local'],
                'tipo_contrato' => $vd['tipo_contrato'],
                'salario' => $vd['salario'],
                'requisitos' => $vd['requisitos'],
                'beneficios' => $vd['beneficios'],
                'status' => $vd['status'],
                'data_limite' => now()->addDays(rand(7, 30)),
                'user_id' => 1,
            ]);

            // Perguntas
            foreach ($vd['perguntas'] as $pi => $pData) {
                VagaPergunta::create([
                    'vaga_id' => $vaga->id,
                    'pergunta' => $pData['pergunta'],
                    'tipo' => $pData['tipo'],
                    'opcoes' => $pData['opcoes'] ?? null,
                    'obrigatoria' => true,
                    'ordem' => $pi,
                ]);
            }

            // Candidatos
            $perguntas = $vaga->perguntas;
            for ($c = 0; $c < $vd['candidatos']; $c++) {
                $nome = $nomes[$nomeIdx % count($nomes)];
                $nomeIdx++;

                $ddd = ['11', '21', '31', '41', '51', '19', '13'][array_rand(['11', '21', '31', '41', '51', '19', '13'])];
                $tel = '(' . $ddd . ') 9' . rand(1000, 9999) . '-' . rand(1000, 9999);

                // Respostas
                $respostas = [];
                foreach ($perguntas as $p) {
                    if ($p->tipo === 'select' && $p->opcoes) {
                        $respostas[$p->id] = $p->opcoes[array_rand($p->opcoes)];
                    } elseif ($p->tipo === 'textarea') {
                        $respostas[$p->id] = 'Tenho experiência na área e estou em busca de novas oportunidades.';
                    } else {
                        $respostas[$p->id] = rand(1, 15) . ' anos';
                    }
                }

                // Distribui candidatos nos últimos 14 dias
                $daysAgo = rand(0, 13);

                VagaCandidatura::create([
                    'vaga_id' => $vaga->id,
                    'nome' => $nome,
                    'email' => strtolower(str_replace(' ', '.', $nome)) . '@email.com',
                    'telefone' => $tel,
                    'curriculo_path' => 'vagas/curriculos/demo_placeholder.pdf',
                    'curriculo_nome_original' => 'curriculo_' . strtolower(str_replace(' ', '_', $nome)) . '.pdf',
                    'respostas' => $respostas,
                    'status' => $statuses[array_rand($statuses)],
                    'observacoes' => rand(0, 3) === 0 ? 'Candidato com bom perfil, agendar entrevista.' : null,
                    'created_at' => now()->subDays($daysAgo)->setTime(rand(7, 19), rand(0, 59)),
                    'updated_at' => now()->subDays($daysAgo),
                ]);
            }
        }
    }
}
