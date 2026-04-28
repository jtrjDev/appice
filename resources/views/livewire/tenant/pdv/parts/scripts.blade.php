<style>
    .scrollbar-thin::-webkit-scrollbar { width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }
</style>

<script>
    function pdvApp() {
        return {
            showPayment: false,
            init() {
                setTimeout(() => document.getElementById('campo-codigo')?.focus(), 100);
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        document.getElementById('campo-codigo')?.focus();
                    }
                    if (e.key === 'F5') {
                        e.preventDefault();
                        const wire = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                        wire?.call('acaoF5');
                    }
                    if (e.key === 'F6') {
                        e.preventDefault();
                        if (confirm('Nova venda?')) {
                            const wire = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                            wire?.set('mesa', '');
                            wire?.set('modoComanda', false);
                            wire?.set('comandaId', null);
                            wire?.call('limparCarrinho');
                        }
                    }
                    if (e.key === 'F8') {
                        e.preventDefault();
                        this.showPayment = true;
                    }
                });
            }
        };
    }
</script>