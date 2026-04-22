<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Novo Cliente</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Cadastre um novo cliente
        </p>
    </div>

    <div class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">
            {{-- Dados Pessoais --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 p-6">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50 mb-4">Dados Pessoais</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1">Nome *</label>
                        <input type="text" wire:model="nome" class="w-full px-3 py-2 border rounded-lg">
                        @error('nome') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">E-mail</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 border rounded-lg">
                        @error('email') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Data de Nascimento</label>
                        <input type="date" wire:model="data_nascimento" class="w-full px-3 py-2 border rounded-lg">
                        @error('data_nascimento') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Telefone</label>
                        <input type="tel" wire:model="telefone" class="w-full px-3 py-2 border rounded-lg" placeholder="(11) 1234-5678">
                        @error('telefone') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Celular</label>
                        <input type="tel" wire:model="celular" class="w-full px-3 py-2 border rounded-lg" placeholder="(11) 91234-5678">
                        @error('celular') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">CPF/CNPJ</label>
                        <input type="text" wire:model="cpf_cnpj" class="w-full px-3 py-2 border rounded-lg" placeholder="000.000.000-00">
                        @error('cpf_cnpj') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Status</label>
                        <select wire:model="ativo" class="w-full px-3 py-2 border rounded-lg">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Endereço --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 p-6">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50 mb-4">Endereço</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">CEP</label>
                        <input type="text" wire:model="cep" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-ink-700 mb-1">Logradouro</label>
                        <input type="text" wire:model="logradouro" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Número</label>
                        <input type="text" wire:model="numero" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Complemento</label>
                        <input type="text" wire:model="complemento" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Bairro</label>
                        <input type="text" wire:model="bairro" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Cidade</label>
                        <input type="text" wire:model="cidade" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1">Estado</label>
                        <select wire:model="estado" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Selecione</option>
                            <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option>
                            <option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option>
                            <option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option>
                            <option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option>
                            <option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option>
                            <option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option>
                            <option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option>
                            <option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option>
                            <option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-ink-700 mb-1">Observações</label>
                        <textarea wire:model="observacoes" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('tenant.clientes.index') }}" wire:navigate class="px-4 py-2 border rounded-lg hover:bg-ink-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800">
                    Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>