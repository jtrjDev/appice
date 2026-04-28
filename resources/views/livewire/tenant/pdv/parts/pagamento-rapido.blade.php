<div class="bg-white dark:bg-ink-900 rounded-xl border shadow-sm p-3">
    <div class="grid grid-cols-4 gap-1.5 mb-2">
        @foreach([['dinheiro','💰','Dinheiro'],['cartao_credito','💳','Crédito'],['cartao_debito','💳','Débito'],['pix','📱','PIX']] as [$val,$icon,$label])
            <button wire:click="$set('formaPagamento', '{{ $val }}')"
                class="py-2 rounded-lg text-center text-xs font-semibold transition-all
                    {{ $formaPagamento === $val ? 'bg-ink-900 text-white shadow-md' : 'bg-gray-100 text-ink-700 border border-gray-200' }}">
                {{ $icon }} {{ $label }}
            </button>
        @endforeach
    </div>
    <div class="grid grid-cols-2 gap-2">
        <input type="number" wire:model.live="valorPagamento" step="0.01" placeholder="Valor" 
            class="px-2 py-2 border rounded-lg text-center text-sm">
        <button wire:click="adicionarPagamento" @if(empty($carrinho) || $valorPagamento <= 0) disabled @endif
            class="py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold disabled:opacity-40">
            Adicionar
        </button>
    </div>
</div>