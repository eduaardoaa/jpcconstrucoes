<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 16px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111;
            margin: 0;
        }

        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 90px;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
        }

        .empresa-box h1 {
            font-size: 17px;
            margin: 0 0 2px 0;
        }

        .empresa-box p {
            margin: 1px 0;
            font-size: 10px;
        }

        .titulo-ficha {
            margin-top: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .box {
            border: 1px solid #000;
            padding: 10px 14px;
            margin-bottom: 8px;
        }

        .row {
            width: 100%;
            margin-bottom: 6px;
            font-size: 10px;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
            font-size: 9px;
            line-height: 1.2;
        }

        table th {
            font-size: 9px;
            font-weight: bold;
        }

        .assinatura-area {
            margin-top: 14px;
            text-align: center;
        }

        .assinatura-linha {
            margin: 0 auto;
            width: 260px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 10px;
        }

        .texto-legal {
            margin-top: 10px;
            font-size: 8.8px;
            text-align: justify;
            line-height: 1.55;
        }

        .subtitulo-entrega {
            margin-top: 12px;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .nowrap {
            white-space: nowrap;
        }

        .destaque-dds {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        @php
            $logoPath = public_path('assets/imgs/logo2.png');
            $dataUltimoDds = $funcionario->obra?->ultimoTreinamentoDds?->data_treinamento;
        @endphp

        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo JPC">
        @endif

        <div class="empresa-box">
            <h1>JPC CONSTRUÇÕES</h1>
            <p><strong>CNPJ: 05.108.674/0001-28</strong></p>
            <p class="titulo-ficha">FICHA DE CONTROLE E ENTREGA DE EPI</p>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <span class="label">NOME:</span> {{ $funcionario->nome }}
        </div>

        <div class="row">
            <span class="label">FUNÇÃO:</span> {{ $funcionario->cargo->nome ?? '-' }}
            &nbsp;&nbsp;&nbsp;
            <span class="label">OBRA / SETOR:</span> {{ $funcionario->obra->nome ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Nº DE REGISTRO / MATRÍCULA:</span> {{ $funcionario->matricula ?? '-' }}
            &nbsp;&nbsp;&nbsp;
            <span class="label">DATA DE ADMISSÃO:</span>
            {{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : '-' }}
        </div>

        <div class="row">
            <span class="label">CPF:</span> {{ $funcionario->cpf ?? '-' }}
        </div>

        <div class="texto-legal">
            Declaro que recebi treinamento teórico e prático quanto ao uso correto, higienização, guarda, conservação,
            limitações e substituição dos EPIs fornecidos, conforme exigências da NR-6.
            <span class="destaque-dds">
                Data do Treinamento DDS: {{ $dataUltimoDds ? $dataUltimoDds->format('d/m/Y') : 'NÃO INFORMADA' }}
            </span>.

            O uso dos EPIs é obrigatório conforme normas de segurança do trabalho; o descumprimento das normas poderá
            caracterizar ato faltoso, sujeito a medidas disciplinares, incluindo advertência, suspensão e outras sanções
            previstas na legislação trabalhista; a recusa injustificada ao uso dos EPIs poderá ser considerada falta grave.

            <strong>OBRIGAÇÕES DO EMPREGADO:</strong>
            Comprometo-me a:
            a) utilizar os EPIs apenas para a finalidade a que se destinam;
            b) usá-los de forma contínua e obrigatória durante toda a jornada de trabalho;
            c) responsabilizar-me pela guarda e conservação;
            d) comunicar imediatamente qualquer dano, desgaste, perda ou necessidade de substituição;
            e) não alterar as características dos equipamentos;
            f) cumprir integralmente as orientações recebidas no treinamento.

            Declaro que recebi todos os EPIs listados, bem como o treinamento adequado, estando apto a utilizá-los
            corretamente e ciente das minhas responsabilidades.
        </div>

        <div class="assinatura-area">
            <div class="assinatura-linha">Assinatura do funcionário</div>
        </div>
    </div>

    @foreach($entregas as $entrega)
        <div class="subtitulo-entrega">
            ENTREGA DE {{ $entrega->data_entrega ? $entrega->data_entrega->format('d/m/Y') : '-' }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">DATA</th>
                    <th style="width: 33%;">DESCRIÇÃO DO EQUIPAMENTO</th>
                    <th style="width: 8%;">QUANT.</th>
                    <th style="width: 8%;">UNID.</th>
                    <th style="width: 14%;">CA</th>
                    <th style="width: 25%;">ASS.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entrega->itens as $item)
                    @php
                        $descricao = $item->produto->nome ?? '-';

                        if ($item->variacao) {
                            $detalhes = trim(
                                ($item->variacao->nome_variacao ?? '') . ' ' .
                                ($item->variacao->cor ?? '') . ' ' .
                                ($item->variacao->tamanho ?? '')
                            );

                            if ($detalhes !== '') {
                                $descricao .= ' - ' . $detalhes;
                            }
                        }

                        $ca = $item->variacao->ca ?? $item->produto->ca ?? '-';
                    @endphp

                    <tr>
                        <td class="nowrap">
                            {{ $entrega->data_entrega ? $entrega->data_entrega->format('d/m/Y') : '-' }}
                        </td>
                        <td>{{ $descricao }}</td>
                        <td>{{ number_format((float) $item->quantidade, 0, ',', '.') }}</td>
                        <td>{{ $item->produto->unidade ?? '-' }}</td>
                        <td>{{ $ca }}</td>
                        <td style="height: 22px;"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>