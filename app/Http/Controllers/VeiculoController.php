<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VeiculoController extends Controller
{
    public function index()
    {
        $veiculos = Veiculo::with('usuario')
            ->orderBy('placa')
            ->get();

        $usuariosDisponiveis = User::with('cargo')
            ->where('status', 'ativo')
            ->where('pode_ter_veiculo', true)
            ->orderBy('name')
            ->get();

        return view('abastecimento.veiculos.index', compact('veiculos', 'usuariosDisponiveis'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'placa' => ['required', 'string', 'max:10', 'unique:veiculos,placa'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'ano' => ['nullable', 'string', 'max:4'],
            'cor' => ['nullable', 'string', 'max:50'],
            'tipo_combustivel' => ['required', Rule::in(['gasolina', 'etanol', 'diesel', 'flex', 'gnv'])],
            'km_atual' => ['nullable', 'numeric', 'min:0'],
            'observacao' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $veiculo = Veiculo::create([
            'placa' => strtoupper($request->placa),
            'marca' => strtoupper($request->marca),
            'modelo' => strtoupper($request->modelo),
            'ano' => $request->ano,
            'cor' => $request->cor ? strtoupper($request->cor) : null,
            'tipo_combustivel' => $request->tipo_combustivel,
            'km_atual' => $request->km_atual ?? 0,
            'status' => 'ativo',
            'observacao' => $request->observacao,
        ]);

        if ($request->filled('user_id')) {
            User::where('veiculo_id', $veiculo->id)->update(['veiculo_id' => null]);

            User::where('id', $request->user_id)->update([
                'pode_ter_veiculo' => true,
            ]);

            User::where('id', $request->user_id)->update([
                'veiculo_id' => $veiculo->id,
            ]);
        }

        return redirect()->route('veiculos.index')->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function update(Request $request, Veiculo $veiculo)
    {
        $validator = Validator::make($request->all(), [
            'placa' => [
                'required',
                'string',
                'max:10',
                Rule::unique('veiculos', 'placa')->ignore($veiculo->id),
            ],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'ano' => ['nullable', 'string', 'max:4'],
            'cor' => ['nullable', 'string', 'max:50'],
            'tipo_combustivel' => ['required', Rule::in(['gasolina', 'etanol', 'diesel', 'flex', 'gnv'])],
            'km_atual' => ['nullable', 'numeric', 'min:0'],
            'observacao' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $veiculo->update([
            'placa' => strtoupper($request->placa),
            'marca' => strtoupper($request->marca),
            'modelo' => strtoupper($request->modelo),
            'ano' => $request->ano,
            'cor' => $request->cor ? strtoupper($request->cor) : null,
            'tipo_combustivel' => $request->tipo_combustivel,
            'km_atual' => $request->km_atual ?? 0,
            'observacao' => $request->observacao,
        ]);

        $usuarioAtualDoVeiculo = User::where('veiculo_id', $veiculo->id)->first();
        $novoUserId = $request->user_id;

        if ($usuarioAtualDoVeiculo && (!$novoUserId || (int) $usuarioAtualDoVeiculo->id !== (int) $novoUserId)) {
            $usuarioAtualDoVeiculo->update([
                'veiculo_id' => null,
            ]);
        }

        if ($novoUserId) {
            User::where('veiculo_id', $veiculo->id)
                ->where('id', '!=', $novoUserId)
                ->update(['veiculo_id' => null]);

            User::where('id', $novoUserId)->update([
                'pode_ter_veiculo' => true,
            ]);

            User::where('id', $novoUserId)->update([
                'veiculo_id' => $veiculo->id,
            ]);
        }

        return redirect()->route('veiculos.index')->with('success', 'Veículo atualizado com sucesso!');
    }

    public function toggleStatus(Veiculo $veiculo)
    {
        $novoStatus = $veiculo->status === 'ativo' ? 'inativo' : 'ativo';

        $veiculo->update([
            'status' => $novoStatus,
        ]);

        return back()->with('success', 'Status atualizado!');
    }
}