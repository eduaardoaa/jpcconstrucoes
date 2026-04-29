<?php

namespace App\Http\Controllers;

use App\Models\WhatsappInstancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WhatsappInstanciaController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermissao('gerenciar_whatsapp')) {
            abort(403);
        }

        $busca = trim((string) $request->get('busca'));
        $status = $request->get('status');

        $query = WhatsappInstancia::query()
            ->withCount('usuarios')
            ->orderBy('nome');

        if ($busca !== '') {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('instance_name', 'like', "%{$busca}%")
                    ->orWhere('api_url', 'like', "%{$busca}%");
            });
        }

        if (in_array($status, ['ativa', 'inativa'])) {
            $query->where('status', $status);
        }

        $instancias = $query->get();

        return view('whatsapp.instancias.index', compact('instancias', 'busca', 'status'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissao('gerenciar_whatsapp')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'instance_name' => ['required', 'string', 'max:255', 'unique:whatsapp_instancias,instance_name'],
            'api_url' => ['nullable', 'url', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['ativa', 'inativa'])],
            'observacoes' => ['nullable', 'string'],
        ], [
            'nome.required' => 'Informe o nome da instância.',
            'instance_name.required' => 'Informe o nome técnico da instância.',
            'instance_name.unique' => 'Já existe uma instância com esse nome técnico.',
            'api_url.url' => 'Informe uma URL válida.',
            'status.required' => 'Informe o status.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'storeInstancia')
                ->withInput()
                ->with('abrir_modal_criar_instancia', true);
        }

        WhatsappInstancia::create([
            'nome' => $request->nome,
            'instance_name' => $request->instance_name,
            'api_url' => $request->api_url,
            'api_key' => $request->api_key,
            'webhook_token' => Str::random(64),
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()
            ->route('whatsapp.instancias.index')
            ->with('success', 'Instância WhatsApp cadastrada com sucesso.');
    }

    public function update(Request $request, WhatsappInstancia $instancia)
    {
        if (!auth()->user()->hasPermissao('gerenciar_whatsapp')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'instance_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('whatsapp_instancias', 'instance_name')->ignore($instancia->id),
            ],
            'api_url' => ['nullable', 'url', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['ativa', 'inativa'])],
            'observacoes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'updateInstancia_' . $instancia->id)
                ->withInput()
                ->with('abrir_modal_editar_instancia', $instancia->id);
        }

        $dados = [
            'nome' => $request->nome,
            'instance_name' => $request->instance_name,
            'api_url' => $request->api_url,
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ];

        if ($request->filled('api_key')) {
            $dados['api_key'] = $request->api_key;
        }

        $instancia->update($dados);

        return redirect()
            ->route('whatsapp.instancias.index')
            ->with('success', 'Instância WhatsApp atualizada com sucesso.');
    }

    public function toggleStatus(WhatsappInstancia $instancia)
    {
        if (!auth()->user()->hasPermissao('gerenciar_whatsapp')) {
            abort(403);
        }

        $instancia->update([
            'status' => $instancia->status === 'ativa' ? 'inativa' : 'ativa',
        ]);

        return back()->with('success', 'Status da instância atualizado com sucesso.');
    }

    public function regenerarWebhook(WhatsappInstancia $instancia)
    {
        if (!auth()->user()->hasPermissao('gerenciar_whatsapp')) {
            abort(403);
        }

        $instancia->update([
            'webhook_token' => Str::random(64),
        ]);

        return back()->with('success', 'Webhook regenerado com sucesso.');
    }
}