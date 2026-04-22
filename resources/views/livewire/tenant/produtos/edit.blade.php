<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Editar Produto</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Editando: {{ $nome }}
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 overflow-hidden">
            
            {{-- Abas --}}
            <div class="border-b border-ink-200 dark:border-ink-800 bg-ink-50 dark:bg-ink-800/50 overflow-x-auto">
                <nav class="flex gap-1 px-4 min-w-max">
                    <button type="button" wire:click="$set('activeTab', 'basico')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'basico' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📦 Básico
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'precos')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'precos' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        💰 Preços
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'venda')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'venda' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🛒 Tipo de Venda
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'estoque')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'estoque' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📊 Estoque
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'fiscal')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'fiscal' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📄 Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'imagem')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'imagem' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🖼️ Imagem
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-6">
                
                {{-- Aba: Básico --}}
                @if($activeTab == 'basico')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Nome do Produto *</label>
                        <input type="text" wire:model="nome" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-ink-500">
                        @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Código / SKU</label>
                        <input type="text" wire:model="codigo" class="w-full px-3 py-2 border rounded-lg" placeholder="Código interno ou código de barras">
                        @error('codigo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Categoria *</label>
                        <select wire:model="categoria_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Selecione uma categoria</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Descrição</label>
                        <textarea wire:model="descricao" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Descrição detalhada do produto..."></textarea>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="ativo">
                            <span class="text-sm text-ink-700 dark:text-ink-300">Produto Ativo</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="destaque">
                            <span class="text-sm text-ink-700 dark:text-ink-300">Produto em Destaque</span>
                        </label>
                    </div>
                </div>
                @endif

                {{-- Aba: Preços --}}
                @if($activeTab == 'precos')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Preço de Venda *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-500">R$</span>
                            <input type="number" step="0.01" wire:model="preco" class="w-full pl-10 pr-3 py-2 border rounded-lg">
                        </div>
                        @error('preco') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Preço Promocional</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-500">R$</span>
                            <input type="number" step="0.01" wire:model="preco_promocional" class="w-full pl-10 pr-3 py-2 border rounded-lg">
                        </div>
                        @error('preco_promocional') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Preço de Custo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-500">R$</span>
                            <input type="number" step="0.01" wire:model="preco_custo" class="w-full pl-10 pr-3 py-2 border rounded-lg">
                        </div>
                        @error('preco_custo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                {{-- Aba: Tipo de Venda --}}
                @if($activeTab == 'venda')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-2">Tipo de Venda</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="tipo_venda" value="unidade">
                                <span>Unidade</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="tipo_venda" value="peso">
                                <span>Peso (kg)</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="tipo_venda" value="fracionado">
                                <span>Fracionado (meio, etc)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="permite_meio">
                            <span class="text-sm text-ink-700 dark:text-ink-300">Permite vender meia porção</span>
                        </label>
                    </div>

                    @if($permite_meio)
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Preço da Meia Porção</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-500">R$</span>
                            <input type="number" step="0.01" wire:model="preco_meio" class="w-64 pl-10 pr-3 py-2 border rounded-lg">
                        </div>
                    </div>
                    @endif

                    {{-- Tamanhos --}}
                    <div class="border-t pt-4">
                        <h3 class="text-md font-semibold mb-3">Tamanhos (ex: Pequeno, Médio, Grande)</h3>
                        <div class="flex gap-2 mb-3">
                            <input type="text" wire:model="novoTamanho.nome" placeholder="Nome" class="px-3 py-2 border rounded-lg w-40">
                            <input type="number" step="0.01" wire:model="novoTamanho.preco" placeholder="Preço" class="px-3 py-2 border rounded-lg w-32">
                            <button type="button" wire:click="adicionarTamanho" class="px-4 py-2 bg-ink-900 text-white rounded-lg">Adicionar</button>
                        </div>
                        @if(count($tamanhos) > 0)
                            <div class="space-y-2">
                                @foreach($tamanhos as $index => $tamanho)
                                    <div class="flex justify-between items-center p-2 bg-ink-50 dark:bg-ink-800 rounded">
                                        <span>{{ $tamanho['nome'] }} - R$ {{ number_format($tamanho['preco'], 2, ',', '.') }}</span>
                                        <button type="button" wire:click="removerTamanho({{ $index }})" class="text-red-500">Remover</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Adicionais --}}
                    <div class="border-t pt-4">
                        <h3 class="text-md font-semibold mb-3">Adicionais (ex: Queijo extra, Bacon)</h3>
                        <div class="flex gap-2 mb-3">
                            <input type="text" wire:model="novoAdicional.nome" placeholder="Nome" class="px-3 py-2 border rounded-lg w-40">
                            <input type="number" step="0.01" wire:model="novoAdicional.preco" placeholder="Preço" class="px-3 py-2 border rounded-lg w-32">
                            <button type="button" wire:click="adicionarAdicional" class="px-4 py-2 bg-ink-900 text-white rounded-lg">Adicionar</button>
                        </div>
                        @if(count($adicionais) > 0)
                            <div class="space-y-2">
                                @foreach($adicionais as $index => $adicional)
                                    <div class="flex justify-between items-center p-2 bg-ink-50 dark:bg-ink-800 rounded">
                                        <span>{{ $adicional['nome'] }} - R$ {{ number_format($adicional['preco'], 2, ',', '.') }}</span>
                                        <button type="button" wire:click="removerAdicional({{ $index }})" class="text-red-500">Remover</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Aba: Estoque --}}
                @if($activeTab == 'estoque')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Estoque Atual</label>
                        <input type="number" wire:model="estoque" class="w-full px-3 py-2 border rounded-lg">
                        @error('estoque') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Estoque Mínimo</label>
                        <input type="number" wire:model="estoque_minimo" class="w-full px-3 py-2 border rounded-lg">
                        @error('estoque_minimo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-ink-500 mt-1">Alertar quando estoque atingir este valor</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Unidade de Medida</label>
                        <select wire:model="unidade_medida" class="w-full px-3 py-2 border rounded-lg">
                            <option value="UN">Unidade (UN)</option>
                            <option value="KG">Quilograma (KG)</option>
                            <option value="G">Grama (G)</option>
                            <option value="L">Litro (L)</option>
                            <option value="ML">Mililitro (ML)</option>
                            <option value="M">Metro (M)</option>
                            <option value="PCT">Pacote (PCT)</option>
                            <option value="CX">Caixa (CX)</option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- Aba: Fiscal --}}
                @if($activeTab == 'fiscal')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">NCM</label>
                        <input type="text" wire:model="ncm" class="w-full px-3 py-2 border rounded-lg" placeholder="0000.00.00" maxlength="8">
                        <p class="text-xs text-ink-500 mt-1">Código NCM para nota fiscal</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">CEST</label>
                        <input type="text" wire:model="cest" class="w-full px-3 py-2 border rounded-lg" placeholder="00.000.00" maxlength="7">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Origem</label>
                        <select wire:model="origem" class="w-full px-3 py-2 border rounded-lg">
                            <option value="0">Nacional</option>
                            <option value="1">Estrangeira - Importação direta</option>
                            <option value="2">Estrangeira - Adquirida no mercado interno</option>
                            <option value="3">Nacional - Conteúdo importado &gt; 40%</option>
                            <option value="4">Nacional - Produção conforme processo produtivo básico</option>
                            <option value="5">Nacional - Conteúdo importado &lt; 40%</option>
                            <option value="6">Estrangeira - Importação direta sem similar nacional</option>
                            <option value="7">Estrangeira - Adquirida no mercado interno sem similar nacional</option>
                            <option value="8">Nacional - Conteúdo importado &gt; 70%</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Alíquota ICMS (%)</label>
                        <input type="number" step="0.01" wire:model="aliq_icms" class="w-full px-3 py-2 border rounded-lg" placeholder="18.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Alíquota IPI (%)</label>
                        <input type="number" step="0.01" wire:model="aliq_ipi" class="w-full px-3 py-2 border rounded-lg" placeholder="5.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Alíquota PIS (%)</label>
                        <input type="number" step="0.01" wire:model="aliq_pis" class="w-full px-3 py-2 border rounded-lg" placeholder="1.65">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Alíquota COFINS (%)</label>
                        <input type="number" step="0.01" wire:model="aliq_cofins" class="w-full px-3 py-2 border rounded-lg" placeholder="7.60">
                    </div>
                </div>
                @endif

                {{-- Aba: Imagem --}}
                @if($activeTab == 'imagem')
                <div>
                    <div class="flex items-center gap-6">
                        <div class="w-32 h-32 bg-ink-100 dark:bg-ink-800 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($imagemPreview)
                                <img src="{{ $imagemPreview }}" class="w-full h-full object-cover">
                            @elseif($imagemAtual)
                                <img src="{{ Storage::url($imagemAtual) }}" class="w-full h-full object-cover">
                            @else
                                <x-ui.icon name="image" class="size-12 text-ink-400" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Imagem do Produto</label>
                            <input type="file" wire:model="imagem" accept="image/*" class="w-full">
                            <p class="text-xs text-ink-500 mt-1">Formatos: JPG, PNG, GIF. Máx: 2MB</p>
                            @if($imagemAtual && !$imagem)
                                <p class="text-xs text-ink-500 mt-1">Imagem atual: {{ basename($imagemAtual) }}</p>
                            @endif
                            @error('imagem') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Botões --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('tenant.produtos.index') }}" wire:navigate
                class="px-4 py-2 border border-ink-300 dark:border-ink-700 rounded-lg text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove>Atualizar Produto</span>
                <span wire:loading>Atualizando...</span>
            </button>
        </div>
    </form>
</div>