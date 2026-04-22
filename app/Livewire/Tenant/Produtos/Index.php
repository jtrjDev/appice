<?php

namespace App\Livewire\Tenant\Produtos;

use App\Models\Tenant\Produto;
use App\Models\Tenant\Categoria;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $categoriaFilter = '';
    public $statusFilter = '';
    public $estoqueFilter = '';
    public $confirmingDelete = false;
    public $produtoToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoriaFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoriaFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->produtoToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $produto = Produto::findOrFail($this->produtoToDelete);
        $produto->delete();

        $this->confirmingDelete = false;
        $this->produtoToDelete = null;
        
        session()->flash('success', 'Produto excluído com sucesso!');
    }

    public function render()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        
        $produtos = Produto::query()
            ->with('categoria')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('ncm', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoriaFilter, function ($query) {
                $query->where('categoria_id', $this->categoriaFilter);
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'ativo') {
                    $query->where('ativo', true);
                } elseif ($this->statusFilter === 'inativo') {
                    $query->where('ativo', false);
                } elseif ($this->statusFilter === 'destaque') {
                    $query->where('destaque', true);
                } elseif ($this->statusFilter === 'estoque_baixo') {
                    $query->whereColumn('estoque', '<=', 'estoque_minimo');
                }
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.tenant.produtos.index', [
            'produtos' => $produtos,
            'categorias' => $categorias,
        ])->layout('layouts.tenant');
    }
}