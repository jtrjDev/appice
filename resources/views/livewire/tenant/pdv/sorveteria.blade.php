<div>
    {{-- Cabeçalho específico para pizzaria --}}
    <div class="mb-3 flex justify-between items-center">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-red-700 to-orange-600 bg-clip-text text-transparent">
            🍕 Pizzaria
        </h1>
    </div>

    {{-- Botão para montar pizza meio a meio --}}
    <div class="mb-3 p-3 bg-amber-50 rounded-lg">
        <button wire:click="abrirModalMeiaPizza" class="px-4 py-2 bg-red-600 text-white rounded-lg">
            🍕 Montar Pizza Meio a Meio
        </button>
    </div>

    {{-- Grid de produtos (pizzas prontas) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
        @foreach($produtos as $produto)
            <button wire:click="adicionarProduto({{ $produto->id }})" class="border rounded-lg p-2 text-center hover:shadow-md">
                <span class="text-2xl">🍕</span>
                <p class="text-xs font-semibold">{{ $produto->nome }}</p>
                <p class="text-sm font-bold text-primary-600">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</p>
            </button>
        @endforeach
    </div>

    {{-- Modal para montar pizza meio a meio --}}
    @if($mostrarModalMeiaPizza)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold mb-4">🍕 Pizza Meio a Meio</h2>
            
            {{-- Primeira metade --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Metade A</label>
                <select wire:model="pizzaMetadeA.id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Selecione</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Segunda metade --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Metade B</label>
                <select wire:model="pizzaMetadeB.id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Selecione</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Tamanho --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Tamanho</label>
                <select wire:model="tamanhoPizza" class="w-full px-3 py-2 border rounded-lg">
                    <option value="pequena">Pequena</option>
                    <option value="media">Média</option>
                    <option value="grande">Grande</option>
                </select>
            </div>
            
            {{-- Regra de preço --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Regra de Preço</label>
                <select wire:model="regraPrecoMeiaPizza" class="w-full px-3 py-2 border rounded-lg">
                    <option value="media">Média dos dois sabores</option>
                    <option value="maior">Maior preço</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button wire:click="$set('mostrarModalMeiaPizza', false)" class="flex-1 py-2 border rounded-lg">Cancelar</button>
                <button wire:click="adicionarMeiaPizza" class="flex-1 py-2 bg-red-600 text-white rounded-lg">Adicionar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Carrinho --}}
    @include('livewire.tenant.pdv.parts.carrinho')
    
    {{-- Modal de pagamento --}}
    @include('livewire.tenant.pdv.parts.modal-pagamento')
</div>