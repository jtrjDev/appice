<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Configuração da Empresa</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Configure os dados da sua empresa para emissão de notas fiscais e cupons
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit="save">
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 overflow-hidden">
            {{-- Abas --}}
            <div class="border-b border-ink-200 dark:border-ink-800 bg-ink-50 dark:bg-ink-800/50">
                <nav class="flex gap-1 px-4 overflow-x-auto">
                    <button type="button" wire:click="$set('activeTab', 'empresa')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'empresa' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🏢 Empresa
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'endereco')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'endereco' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📍 Endereço
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'contato')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'contato' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📞 Contato
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'logo')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'logo' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🖼️ Logo
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'nf')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'nf' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📄 Nota Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'certificado')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'certificado' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🔐 Certificado
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'cupom')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'cupom' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🧾 Cupom Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'fiscal')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'fiscal' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        ⚖️ Regime Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'webhook')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'webhook' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🔗 Webhooks
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-6">
                {{-- Aba: Empresa --}}
                @if($activeTab == 'empresa')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Razão Social *</label>
                        <input type="text" wire:model="razao_social" class="w-full px-3 py-2 border rounded-lg">
                        @error('razao_social') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Nome Fantasia</label>
                        <input type="text" wire:model="nome_fantasia" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">CNPJ/CPF *</label>
                        <input type="text" wire:model="cpf_cnpj" class="w-full px-3 py-2 border rounded-lg" placeholder="00.000.000/0000-00">
                        @error('cpf_cnpj') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Inscrição Estadual</label>
                        <input type="text" wire:model="inscricao_estadual" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Inscrição Municipal</label>
                        <input type="text" wire:model="inscricao_municipal" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">RG (se aplicável)</label>
                        <input type="text" wire:model="rg" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                @endif

                {{-- Aba: Endereço --}}
                @if($activeTab == 'endereco')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">CEP *</label>
                        <input type="text" wire:model.live="cep" class="w-full px-3 py-2 border rounded-lg" placeholder="00000-000">
                        @error('cep') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Endereço *</label>
                        <input type="text" wire:model="endereco" class="w-full px-3 py-2 border rounded-lg">
                        @error('endereco') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Número *</label>
                        <input type="text" wire:model="numero" class="w-full px-3 py-2 border rounded-lg">
                        @error('numero') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Complemento</label>
                        <input type="text" wire:model="complemento" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Bairro *</label>
                        <input type="text" wire:model="bairro" class="w-full px-3 py-2 border rounded-lg">
                        @error('bairro') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Cidade *</label>
                        <input type="text" wire:model="cidade" class="w-full px-3 py-2 border rounded-lg">
                        @error('cidade') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Estado *</label>
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
                        @error('estado') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                {{-- Aba: Contato --}}
                @if($activeTab == 'contato')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Telefone</label>
                        <input type="tel" wire:model="telefone" class="w-full px-3 py-2 border rounded-lg" placeholder="(11) 1234-5678">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">WhatsApp *</label>
                        <input type="tel" wire:model="whatsapp" class="w-full px-3 py-2 border rounded-lg" placeholder="(11) 91234-5678">
                        @error('whatsapp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">E-mail *</label>
                        <input type="email" wire:model="email_empresa" class="w-full px-3 py-2 border rounded-lg">
                        @error('email_empresa') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Site</label>
                        <input type="url" wire:model="site" class="w-full px-3 py-2 border rounded-lg" placeholder="https://www.exemplo.com.br">
                    </div>
                </div>
                @endif

                {{-- Aba: Logo --}}
                @if($activeTab == 'logo')
                <div>
                    <div class="flex items-center gap-6">
                        <div class="w-32 h-32 bg-ink-100 dark:bg-ink-800 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($logoPreview)
                                <img src="{{ $logoPreview }}" class="w-full h-full object-cover">
                            @else
                                <x-ui.icon name="image" class="size-12 text-ink-400" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Logo da Empresa</label>
                            <input type="file" wire:model="logo" accept="image/*" class="w-full">
                            <p class="text-xs text-ink-500 mt-1">Formatos: JPG, PNG, GIF. Máx: 2MB</p>
                            @error('logo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                @endif

                {{-- Aba: Nota Fiscal --}}
                @if($activeTab == 'nf')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Último Número NF</label>
                        <input type="text" wire:model="ultimo_numero_nf" class="w-full px-3 py-2 border rounded-lg" placeholder="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Número Série</label>
                        <input type="text" wire:model="numero_serie_nf" class="w-full px-3 py-2 border rounded-lg" placeholder="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Ambiente</label>
                        <select wire:model="ambiente_nf" class="w-full px-3 py-2 border rounded-lg">
                            <option value="homologacao">🏭 Homologação (Testes)</option>
                            <option value="producao">✅ Produção (Real)</option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- Aba: Certificado Digital --}}
                @if($activeTab == 'certificado')
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Arquivo do Certificado (.pfx ou .p12)</label>
                        <input type="file" wire:model="certificado" accept=".pfx,.p12" class="w-full">
                        <p class="text-xs text-ink-500 mt-1">Arquivo do certificado digital A1 (válido por 1 ano)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Senha do Certificado</label>
                        <input type="password" wire:model="certificado_senha" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Data de Validade</label>
                        <input type="date" wire:model="certificado_validade" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                @endif

                {{-- Aba: Cupom Fiscal --}}
                @if($activeTab == 'cupom')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Cabeçalho do Cupom</label>
                        <textarea wire:model="cabecalho_cupom" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="=========================================
          MINHA EMPRESA
          CNPJ: 00.000.000/0000-00
========================================="></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Rodapé do Cupom</label>
                        <textarea wire:model="rodape_cupom" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="=========================================
    OBRIGADO PELA PREFERÊNCIA!
    Volte sempre!
========================================="></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Tema do Cupom</label>
                        <select wire:model="tema_cupom" class="w-full px-3 py-2 border rounded-lg">
                            <option value="padrao">Padrão</option>
                            <option value="moderno">Moderno</option>
                            <option value="minimalista">Minimalista</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="exibir_logo_cupom">
                            <span class="text-sm text-ink-700 dark:text-ink-300">Exibir logo no cupom</span>
                        </label>
                    </div>
                </div>
                @endif

                {{-- Aba: Regime Fiscal --}}
                @if($activeTab == 'fiscal')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Regime Tributário</label>
                        <select wire:model="regime_tributario" class="w-full px-3 py-2 border rounded-lg">
                            <option value="simples_nacional">Simples Nacional</option>
                            <option value="lucro_presumido">Lucro Presumido</option>
                            <option value="lucro_real">Lucro Real</option>
                            <option value="mei">MEI</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">CNAE (Código de Atividade)</label>
                        <input type="text" wire:model="codigo_atividade" class="w-full px-3 py-2 border rounded-lg" placeholder="63.19-4-00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Código do Município (IBGE)</label>
                        <input type="text" wire:model="codigo_municipio" class="w-full px-3 py-2 border rounded-lg" placeholder="4119905">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Código do País</label>
                        <input type="text" wire:model="codigo_pais" class="w-full px-3 py-2 border rounded-lg" placeholder="1058 (Brasil)">
                    </div>
                </div>
                @endif

                {{-- Aba: Webhooks --}}
                @if($activeTab == 'webhook')
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Webhook NFe</label>
                        <input type="url" wire:model="webhook_nfe" class="w-full px-3 py-2 border rounded-lg" placeholder="https://seudominio.com/webhook/nfe">
                        <p class="text-xs text-ink-500 mt-1">Receba notificações de atualização de NFe</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Webhook NFSe</label>
                        <input type="url" wire:model="webhook_nfse" class="w-full px-3 py-2 border rounded-lg" placeholder="https://seudominio.com/webhook/nfse">
                        <p class="text-xs text-ink-500 mt-1">Receba notificações de atualização de NFSe</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Botões --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('tenant.configuracoes.index') }}" wire:navigate
                class="px-4 py-2 border border-ink-300 dark:border-ink-700 rounded-lg text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove>Salvar Configuração</span>
                <span wire:loading>Salvando...</span>
            </button>
        </div>
    </form>
</div>