<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Caixa as CaixaModel;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Sangria;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithToast; // Importar o trait
use Livewire\Component;
use Livewire\Attributes\Computed;

class Caixa extends Component
{
    use WithToast; // Usar o trait
    public ?int $caixaId = null;
    public float $saldoFinalInformado = 0;
    public string $observacao = '';
    public bool $mostrarFechamento = false;
  
    public $mostrarModalSangria = false;
    public $tipoSangria = 'sangria';
    public $valorSangria = '';
    public $motivoSangria = '';

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

public function abrirModalSangria($tipo)
{
    $this->tipoSangria = $tipo;
    $this->valorSangria = '';
    $this->motivoSangria = '';
    $this->mostrarModalSangria = true;
}

public function salvarSangria()
{
    $this->validate([
        'valorSangria' => 'required|numeric|min:0.01',
        'motivoSangria' => 'required|string|min:3',
    ]);

    try {
        DB::beginTransaction();

        // Buscar o usuário do tenant
        $tenantUser = \App\Models\Tenant\User::where('email', auth()->user()->email)->first();
        
        if (!$tenantUser) {
            throw new \Exception('Usuário não encontrado no tenant');
        }

        Sangria::create([
            'caixa_id' => $this->caixa->id,
            'valor' => $this->valorSangria,
            'tipo' => $this->tipoSangria,
            'motivo' => $this->motivoSangria,
            'user_id' => $tenantUser->id,
        ]);

        // Atualizar totais do caixa
        if ($this->tipoSangria == 'sangria') {
            $this->caixa->increment('total_sangrias', $this->valorSangria);
        } else {
            $this->caixa->increment('total_suprimentos', $this->valorSangria);
        }

        DB::commit();

        $this->mostrarModalSangria = false;
          $this->toastSuccess('Ação realizada com sucesso!');
    } catch (\Exception $e) {
        DB::rollBack();
        $this->toastError('Erro ao finalizar sangria!');
    }
}

#[Computed]
public function totalSangrias()
{
    return $this->caixa->total_sangrias ?? 0;
}

#[Computed]
public function totalSuprimentos()
{
    return $this->caixa->total_suprimentos ?? 0;
}

#[Computed]
public function saldoEsperado()
{
    return $this->caixa->saldo_inicial + $this->totalVendas - $this->totalSangrias + $this->totalSuprimentos;
}

   
   #[Computed]
public function diferenca(): float
{
    return ($this->saldoFinalInformado ?? 0) - $this->saldoEsperado;
}

    public function abrirCaixa(float $saldoInicial = 0): void
    {
        $caixa = CaixaModel::create([
            'user_id'       => \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id'),
            'saldo_inicial' => $saldoInicial,
            'status'        => 'aberto',
        ]);

        $this->caixaId = $caixa->id;
        $this->toastSuccess('Caixa aberto com sucesso!');
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
            $this->toastSuccess('Caixa fechado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao fechar caixa!');
        }
    }

    #[Computed]
public function totalVendas()
{
    return $this->caixa->total_vendas;
}


#[Computed]
public function totaisMaquinas()
{
    // Se você tiver uma tabela de máquinas, pode agrupar por máquina
    // Exemplo fictício - adapte conforme sua necessidade
    return [];
}

    public function render()
    {
        return view('livewire.tenant.pdv.caixa')
            ->layout('layouts.tenant');
    }
}