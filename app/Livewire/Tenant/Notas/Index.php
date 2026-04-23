<?php

namespace App\Livewire\Tenant\Notas;

use App\Models\Tenant\NotaFiscal;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithTenancy;

class Index extends Component
{
    use WithPagination, WithTenancy;

    public $search = '';
    public $statusFilter = '';
    public $modeloFilter = '';
    public $dataInicio = '';
    public $dataFim = '';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'modeloFilter' => ['except' => ''],
        'dataInicio' => ['except' => ''],
        'dataFim' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedModeloFilter()
    {
        $this->resetPage();
    }

    public function limparFiltros()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->modeloFilter = '';
        $this->dataInicio = '';
        $this->dataFim = '';
        $this->resetPage();
    }

    public function render()
    {
        $notas = NotaFiscal::query()
            ->with('pedido')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero_nota', 'like', '%' . $this->search . '%')
                      ->orWhere('chave_acesso', 'like', '%' . $this->search . '%')
                      ->orWhere('referencia', 'like', '%' . $this->search . '%')
                      ->orWhereHas('pedido', function ($pedido) {
                          $pedido->where('numero_pedido', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->modeloFilter, function ($query) {
                $query->where('modelo', $this->modeloFilter);
            })
            ->when($this->dataInicio, function ($query) {
                $query->whereDate('created_at', '>=', $this->dataInicio);
            })
            ->when($this->dataFim, function ($query) {
                $query->whereDate('created_at', '<=', $this->dataFim);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $totais = [
            'total' => NotaFiscal::count(),
            'autorizadas' => NotaFiscal::where('status', 'autorizada')->count(),
            'processando' => NotaFiscal::where('status', 'processando')->count(),
            'rejeitadas' => NotaFiscal::where('status', 'rejeitada')->count(),
            'erro' => NotaFiscal::where('status', 'erro')->count(),
        ];

        $statusOptions = [
            'processando' => 'Processando',
            'autorizada' => 'Autorizada',
            'rejeitada' => 'Rejeitada',
            'erro' => 'Erro',
        ];

        $modeloOptions = [
            'nfe' => 'NF-e',
            'nfce' => 'NFC-e',
            'nfse' => 'NFSe',
        ];

        return view('livewire.tenant.notas.index', [
            'notas' => $notas,
            'totais' => $totais,
            'statusOptions' => $statusOptions,
            'modeloOptions' => $modeloOptions,
        ])->layout('layouts.tenant');
    }
}