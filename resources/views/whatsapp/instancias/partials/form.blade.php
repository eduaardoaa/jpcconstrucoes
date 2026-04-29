@php
    $bag = $errors->{$errorBag};
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nome interno *</label>
        <input
            type="text"
            name="nome"
            class="form-control-custom"
            value="{{ old('nome', optional($instancia)->nome) }}"
            placeholder="Ex: Comercial JPC"
            required
        >

        @if($bag->has('nome'))
            <div class="text-danger small mt-1">{{ $bag->first('nome') }}</div>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Nome da instância Evolution *</label>
        <input
            type="text"
            name="instance_name"
            class="form-control-custom"
            value="{{ old('instance_name', optional($instancia)->instance_name) }}"
            placeholder="Ex: comercial_jpc"
            required
        >

        @if($bag->has('instance_name'))
            <div class="text-danger small mt-1">{{ $bag->first('instance_name') }}</div>
        @endif
    </div>

    <div class="col-md-8">
        <label class="form-label">URL da Evolution API</label>
        <input
            type="url"
            name="api_url"
            class="form-control-custom"
            value="{{ old('api_url', optional($instancia)->api_url) }}"
            placeholder="Ex: https://evolution.seudominio.com"
        >

        @if($bag->has('api_url'))
            <div class="text-danger small mt-1">{{ $bag->first('api_url') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select-custom" required>
            <option value="ativa" @selected(old('status', optional($instancia)->status ?? 'ativa') === 'ativa')>
                Ativa
            </option>
            <option value="inativa" @selected(old('status', optional($instancia)->status) === 'inativa')>
                Inativa
            </option>
        </select>

        @if($bag->has('status'))
            <div class="text-danger small mt-1">{{ $bag->first('status') }}</div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">
            API Key
            @if($instancia)
                <span class="muted">(preencha somente se quiser alterar)</span>
            @endif
        </label>

        <input
            type="password"
            name="api_key"
            class="form-control-custom"
            value=""
            placeholder="{{ $instancia ? 'Deixe vazio para manter a chave atual' : 'Cole a API Key da Evolution' }}"
        >

        @if($bag->has('api_key'))
            <div class="text-danger small mt-1">{{ $bag->first('api_key') }}</div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">Observações</label>
        <textarea
            name="observacoes"
            rows="3"
            class="form-textarea-custom"
            placeholder="Ex: número usado pelo comercial, atendimento inicial, vendas..."
        >{{ old('observacoes', optional($instancia)->observacoes) }}</textarea>

        @if($bag->has('observacoes'))
            <div class="text-danger small mt-1">{{ $bag->first('observacoes') }}</div>
        @endif
    </div>
</div>