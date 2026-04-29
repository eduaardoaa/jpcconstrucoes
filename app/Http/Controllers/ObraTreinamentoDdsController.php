<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\ObraTreinamentoDds;
use App\Models\ObraTreinamentoDdsAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObraTreinamentoDdsController extends Controller
{
    public function historico(Obra $obra)
    {
        $obra->load([
            'treinamentosDds' => function ($query) {
                $query->with(['usuario', 'anexos'])
                    ->orderByDesc('data_treinamento')
                    ->orderByDesc('id');
            },
            'ultimoTreinamentoDds',
        ]);

        return view('obras.dds-historico', compact('obra'));
    }

    public function store(Request $request, Obra $obra)
    {
        $request->validate([
            'data_treinamento' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
            'anexos' => ['nullable', 'array'],
            'anexos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'foto_camera_base64' => ['nullable', 'string'],
            'foto_camera_nome' => ['nullable', 'string', 'max:255'],
            'foto_camera_mime' => ['nullable', 'string', 'max:100'],
        ], [
            'data_treinamento.required' => 'Informe a data do DDS.',
            'data_treinamento.date' => 'Informe uma data válida.',
            'anexos.*.mimes' => 'Os anexos devem ser JPG, JPEG, PNG ou PDF.',
            'anexos.*.max' => 'Cada arquivo pode ter no máximo 10MB.',
        ]);

        $temArquivo = $request->hasFile('anexos');
        $temFotoCamera = filled($request->foto_camera_base64);

        DB::transaction(function () use ($request, $obra, $temArquivo, $temFotoCamera) {
            $treinamento = ObraTreinamentoDds::create([
                'obra_id' => $obra->id,
                'user_id' => auth()->id(),
                'data_treinamento' => $request->data_treinamento,
                'observacoes' => $request->observacoes,
            ]);

            if ($temArquivo) {
                foreach ($request->file('anexos', []) as $arquivo) {
                    if (!$arquivo) {
                        continue;
                    }

                    $caminho = $arquivo->store('dds_obras', 'public');

                    $treinamento->anexos()->create([
                        'arquivo' => $caminho,
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'mime_type' => $arquivo->getClientMimeType(),
                    ]);
                }
            }

            if ($temFotoCamera) {
                $base64 = $request->foto_camera_base64;
                $mimeRecebido = $request->foto_camera_mime ?: null;
                $nomeRecebido = $request->foto_camera_nome ?: null;

                if (!str_contains($base64, ',')) {
                    throw new \RuntimeException('Arquivo da câmera inválido.');
                }

                [$meta, $conteudo] = explode(',', $base64, 2);

                $mimeType = 'image/jpeg';
                $extensao = 'jpg';

                if (
                    str_contains($meta, 'application/pdf') ||
                    $mimeRecebido === 'application/pdf' ||
                    ($nomeRecebido && str_ends_with(strtolower($nomeRecebido), '.pdf'))
                ) {
                    $mimeType = 'application/pdf';
                    $extensao = 'pdf';
                } elseif (
                    str_contains($meta, 'image/png') ||
                    $mimeRecebido === 'image/png'
                ) {
                    $mimeType = 'image/png';
                    $extensao = 'png';
                }

                $binario = base64_decode($conteudo, true);

                if ($binario === false) {
                    throw new \RuntimeException('Falha ao processar o arquivo capturado.');
                }

                $nomeArquivo = 'dds-obra-' . Str::uuid() . '.' . $extensao;
                $caminho = 'dds_obras/' . $nomeArquivo;

                Storage::disk('public')->put($caminho, $binario);

                $treinamento->anexos()->create([
                    'arquivo' => $caminho,
                    'nome_original' => $nomeRecebido ?: $nomeArquivo,
                    'mime_type' => $mimeType,
                ]);
            }
        });

        return redirect()
            ->route('obras.dds.historico', $obra)
            ->with('success', 'DDS registrado com sucesso.');
    }

    public function destroy(ObraTreinamentoDds $treinamento)
    {
        foreach ($treinamento->anexos as $anexo) {
            if ($anexo->arquivo && Storage::disk('public')->exists($anexo->arquivo)) {
                Storage::disk('public')->delete($anexo->arquivo);
            }
        }

        $obraId = $treinamento->obra_id;
        $treinamento->delete();

        return redirect()
            ->route('obras.dds.historico', $obraId)
            ->with('success', 'DDS excluído com sucesso.');
    }

    public function abrirAnexo(ObraTreinamentoDdsAnexo $anexo)
    {
        $caminho = storage_path('app/public/' . $anexo->arquivo);

        if (!file_exists($caminho)) {
            abort(404);
        }

        return response()->file($caminho, [
            'Content-Type' => $anexo->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . ($anexo->nome_original ?: basename($caminho)) . '"',
        ]);
    }
}