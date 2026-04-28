@if($this->caixaAberto)
    <div class="mb-3 flex flex-wrap items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm">
        <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
        <span class="font-semibold text-green-700 text-sm">💰 Caixa aberto</span>
        <span class="text-xs text-green-600">•</span>
        <span class="text-xs text-green-600">{{ $this->caixaAberto->aberto_em->format('d/m H:i') }}</span>
        <span class="text-xs font-bold text-green-700">{{ $this->caixaAberto->operador->name ?? 'N/A' }}</span>
        <span class="text-xs text-green-600">{{ $this->caixaAberto->quantidade_vendas }} vendas</span>
        <span class="text-xs font-bold text-green-700">R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</span>
    </div>
@else
    <div class="mb-3 flex items-center justify-between px-4 py-2 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200">
        <div class="flex items-center gap-3">
            <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
            <span class="font-semibold text-red-700 text-sm">⚠️ Nenhum caixa aberto</span>
        </div>
        <a href="{{ route('tenant.caixa') }}" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs">Abrir Caixa</a>
    </div>
@endif