<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Caixa as CaixaModel;
use App\Models\Tenant\Pedido;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Caixa extends Component
{
    public ?int $caixaId = null;
    public float $saldoFinalInformado = 0;
    public string $observacao = '';
    public bool $mostrarFechamento = false;

    public function mount(): void
    {
        $caixa = CaixaModel::caixaAberto();
        $this->caixaId = $caixa?->id;
    }

    #[Computed]
    public function caixa(): ?CaixaModel
    {
        if (!$this->caixaId) return null;
        return CaixaModel::find($this->caixaId);
    }

    #[Computed]
    public function pedidosHoje()
    {
        if (!$this->caixaId) return collect();

        return Pedido::where('caixa_id', $this->caixaId)
            ->with('itens')
            ->latest()
            ->get();
    }

    #[Computed]
public function totalPorForma(): array
{
    if (!$this->caixaId || !$this->caixa) {
        return [
            'dinheiro'       => 0,
            'cartao_credito' => 0,
            'cartao_debito'  => 0,
            'pix'            => 0,
        ];
    }

    $caixa = $this->caixa;

    return [
        'dinheiro'       => (float) $caixa->total_dinheiro,
        'cartao_credito' => (float) $caixa->total_credito,
        'cartao_debito'  => (float) $caixa->total_debito,
        'pix'            => (float) $caixa->total_pix,
    ];
}

    #[Computed]
    public function saldoEsperado(): float
    {
        if (!$this->caixa) return 0;
        return (float) $this->caixa->saldo_inicial + (float) $this->caixa->total_dinheiro;
    }

    #[Computed]
    public function diferenca(): float
    {
        return $this->saldoFinalInformado - $this->saldoEsperado;
    }

    public function abrirCaixa(float $saldoInicial = 0): void
    {
        $caixa = CaixaModel::create([
            'user_id'       => \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id'),
            'saldo_inicial' => $saldoInicial,
            'status'        => 'aberto',
        ]);

        $this->caixaId = $caixa->id;
        $this->dispatch('pdv-sucesso', mensagem: 'Caixa aberto com sucesso!');
    }

    public function abrirModalFechamento(): void
    {
        $this->saldoFinalInformado = $this->saldoEsperado;
        $this->mostrarFechamento = true;
    }

    public function fecharCaixa(): void
    {
        if (!$this->caixa) return;

        DB::beginTransaction();

        try {
            $this->caixa->update([
                'saldo_final'  => $this->saldoFinalInformado,
                'status'       => 'fechado',
                'fechado_em'   => now(),
                'observacao'   => $this->observacao,
            ]);

            DB::commit();

            $this->caixaId = null;
            $this->mostrarFechamento = false;
            $this->dispatch('pdv-sucesso', mensagem: 'Caixa fechado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('pdv-aviso', mensagem: 'Erro ao fechar caixa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.pdv.caixa')
            ->layout('layouts.tenant');
    }
}