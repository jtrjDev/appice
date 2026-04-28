<div x-show="showPayment" x-trap.noscroll="showPayment" x-transition
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
    <div @click.away="showPayment = false" class="bg-white dark:bg-ink-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-primary-50 to-indigo-50 flex justify-between items-center">
            <h3 class="font-bold">💳 Finalizar Pagamento</h3>
            <button @click="showPayment = false" class="p-2 hover:bg-gray-200 rounded-full">✕</button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold mb-2">Formas de Pagamento</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach([['dinheiro','💰','Dinheiro'],['cartao_credito','💳','Crédito'],['cartao_debito','💳','Débito'],['pix','📱','PIX']] as [$val,$icon,$label])
                            <button wire:click="$set('formaPagamento', '{{ $val }}')"
                                class="p-3 rounded-xl border-2 transition-all flex flex-col items-center
                                    {{ $formaPagamento === $val ? 'border-primary-600 bg-primary-50' : 'border-gray-200' }}">
                                <span class="text-2xl">{{ $icon }}</span>
                                <span class="text-xs">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" wire:model.live="valorPagamento" step="0.01" placeholder="Valor" 
                            class="px-3 py-2 border rounded-xl text-center">
                        <button wire:click="adicionarPagamento" 
                            class="px-3 py-2 bg-amber-500 text-white rounded-xl font-semibold hover:bg-amber-600">
                            Adicionar
                        </button>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold mb-2">Pagamentos</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto mb-4">
                        @forelse($pagamentos as $index => $pag)
                            <div class="flex justify-between items-center bg-green-50 p-2 rounded-lg">
                                <span>{{ $pag['forma'] === 'dinheiro' ? '💰' : ($pag['forma'] === 'pix' ? '📱' : '💳') }} 
                                    {{ ucfirst(str_replace('_',' ',$pag['forma'])) }}</span>
                                <span>R$ {{ number_format($pag['valor'],2,',','.') }}</span>
                                <button wire:click="removerPagamento({{ $index }})" class="text-red-500">✕</button>
                            </div>
                        @empty
                            <p class="text-center text-ink-400 py-4">Nenhum pagamento</p>
                        @endforelse
                    </div>
                    @php
                        $totalPagoMelhorado = round(collect($pagamentos)->sum('valor'), 2);
                        $pendenteMelhorado = max(0, $this->totalCarrinho - $totalPagoMelhorado);
                    @endphp
                    <div class="border-t pt-3">
                        <div class="flex justify-between font-bold">
                            <span>Total:</span>
                            <span>R$ {{ number_format($this->totalCarrinho,2,',','.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span>Pago:</span>
                            <span class="text-green-600">R$ {{ number_format($totalPagoMelhorado,2,',','.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Restante:</span>
                            <span class="{{ $pendenteMelhorado > 0 ? 'text-red-600' : 'text-green-600' }}">
                                R$ {{ number_format($pendenteMelhorado,2,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button wire:click="finalizarVenda" @if($pendenteMelhorado > 0) disabled @endif
                    class="w-full py-3 bg-green-600 text-white rounded-xl font-bold disabled:opacity-40 hover:bg-green-700">
                    ✅ FINALIZAR VENDA
                </button>
            </div>
        </div>
    </div>
</div>