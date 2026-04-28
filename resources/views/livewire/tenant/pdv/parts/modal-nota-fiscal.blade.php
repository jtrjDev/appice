@if($mostrarModalNF)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-ink-900 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="size-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-ink-900 dark:text-ink-50">Emitir Nota Fiscal</h2>
        </div>
        
        <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Preencha os dados do cliente para emitir a nota fiscal</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">CPF / CNPJ *</label>
                <input type="text" wire:model="cpfCnpjNF" 
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                    placeholder="000.000.000-00 ou 00.000.000/0000-00">
                @error('cpfCnpjNF')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Nome do Cliente *</label>
                <input type="text" wire:model="nomeClienteNF" 
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                    placeholder="Nome completo">
                @error('nomeClienteNF')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="finalizarSemNF" 
                class="px-5 py-2.5 border border-gray-300 dark:border-ink-600 rounded-xl text-ink-700 dark:text-ink-300 hover:bg-gray-50 dark:hover:bg-ink-800 transition-all">
                Não emitir
            </button>
            <button wire:click="emitirNotaDaVenda" 
                class="px-5 py-2.5 bg-gradient-to-r from-ink-800 to-ink-900 text-white rounded-xl font-medium hover:shadow-lg transition-all">
                Emitir Nota
            </button>
        </div>
    </div>
</div>
@endif