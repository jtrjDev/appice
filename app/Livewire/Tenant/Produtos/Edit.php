<?php

namespace App\Livewire\Tenant\Produtos;

use App\Models\Tenant\Produto;
use App\Models\Tenant\Categoria;
use App\Helpers\IconsHelper;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithTenancy;

class Edit extends Component
{
    use WithFileUploads, WithTenancy;

    public $produtoId;
    
    // Dados básicos
    public $nome = '';
    public $codigo = '';
    public $categoria_id = '';
    public $descricao = '';
    
    public $icone = '';

    // Preços
    public $preco = '';
    public $preco_promocional = '';
    public $preco_custo = '';
    
    // Tipo de venda
    public $tipo_venda = 'unidade';
    public $permite_meio = false;
    public $preco_meio = '';
    public $tamanhos = [];
    public $adicionais = [];
    
    // Estoque
    public $estoque = 0;
    public $estoque_minimo = 0;
    
    // Dados fiscais
    public $ncm = '';
    public $cest = '';
    public $origem = '0';
    public $aliq_icms = '';
    public $aliq_ipi = '';
    public $aliq_pis = '';
    public $aliq_cofins = '';
    public $unidade_medida = 'UN';
    
    // Imagem
    public $imagem;
    public $imagemPreview;
    public $imagemAtual;
    
    // Status
    public $ativo = true;
    public $destaque = false;
    
    // Variáveis auxiliares
    public $novoTamanho = ['nome' => '', 'preco' => ''];
    public $novoAdicional = ['nome' => '', 'preco' => ''];
    
    // Aba ativa
    public $activeTab = 'basico';

    protected $rules = [
        'nome' => 'required|string|max:255',
        'codigo' => 'nullable|string|max:50',
        'categoria_id' => 'required|exists:categorias,id',
        'descricao' => 'nullable|string',
        'preco' => 'required|numeric|min:0',
        'preco_promocional' => 'nullable|numeric|min:0',
        'preco_custo' => 'nullable|numeric|min:0',
        'tipo_venda' => 'required|in:unidade,peso,fracionado',
        'permite_meio' => 'boolean',
        'preco_meio' => 'nullable|numeric|min:0',
        'estoque' => 'required|integer|min:0',
        'estoque_minimo' => 'required|integer|min:0',
        'ncm' => 'nullable|string|max:8',
        'cest' => 'nullable|string|max:7',
        'origem' => 'required|in:0,1,2,3,4,5,6,7,8',
        'aliq_icms' => 'nullable|numeric|min:0|max:100',
        'aliq_ipi' => 'nullable|numeric|min:0|max:100',
        'aliq_pis' => 'nullable|numeric|min:0|max:100',
        'aliq_cofins' => 'nullable|numeric|min:0|max:100',
        'unidade_medida' => 'required|string|max:5',
        'imagem' => 'nullable|image|max:2048',
        'ativo' => 'boolean',
        'destaque' => 'boolean',
    ];

    public function mount()
    {
        // Pegar o ID da URL
        $this->produtoId = request()->route('produto');
        
        // Buscar o produto (o tenancy já está inicializado pelo Trait)
        $produto = Produto::find($this->produtoId);
        
        if (!$produto) {
            session()->flash('error', 'Produto não encontrado.');
            return redirect()->route('tenant.produtos.index');
        }
        
        $this->nome = $produto->nome;
        $this->codigo = $produto->codigo;
        $this->categoria_id = $produto->categoria_id;
        $this->descricao = $produto->descricao;
        $this->preco = $produto->preco;
        $this->preco_promocional = $produto->preco_promocional;
        $this->preco_custo = $produto->preco_custo;
        $this->tipo_venda = $produto->tipo_venda;
        $this->permite_meio = $produto->permite_meio;
        $this->preco_meio = $produto->preco_meio;
        $this->tamanhos = $produto->tamanhos ?? [];
        $this->adicionais = $produto->adicionais ?? [];
        $this->estoque = $produto->estoque;
        $this->estoque_minimo = $produto->estoque_minimo;
        $this->ncm = $produto->ncm;
        $this->cest = $produto->cest;
        $this->origem = $produto->origem;
        $this->aliq_icms = $produto->aliq_icms;
        $this->aliq_ipi = $produto->aliq_ipi;
        $this->aliq_pis = $produto->aliq_pis;
        $this->aliq_cofins = $produto->aliq_cofins;
        $this->unidade_medida = $produto->unidade_medida;
        $this->imagemAtual = $produto->imagem;
        $this->icone = $produto->icone; // Carrega o ícone
        $this->ativo = $produto->ativo;
        $this->destaque = $produto->destaque;
    }

    public function adicionarTamanho()
    {
        if (!empty($this->novoTamanho['nome']) && !empty($this->novoTamanho['preco'])) {
            $this->tamanhos[] = [
                'nome' => $this->novoTamanho['nome'],
                'preco' => floatval($this->novoTamanho['preco'])
            ];
            $this->novoTamanho = ['nome' => '', 'preco' => ''];
        }
    }

    public function removerTamanho($index)
    {
        unset($this->tamanhos[$index]);
        $this->tamanhos = array_values($this->tamanhos);
    }

    public function adicionarAdicional()
    {
        if (!empty($this->novoAdicional['nome']) && !empty($this->novoAdicional['preco'])) {
            $this->adicionais[] = [
                'nome' => $this->novoAdicional['nome'],
                'preco' => floatval($this->novoAdicional['preco'])
            ];
            $this->novoAdicional = ['nome' => '', 'preco' => ''];
        }
    }

    public function removerAdicional($index)
    {
        unset($this->adicionais[$index]);
        $this->adicionais = array_values($this->adicionais);
    }

    public function updatedImagem()
    {
        $this->validate([
            'imagem' => 'image|max:2048',
        ]);
        $this->imagemPreview = $this->imagem->temporaryUrl();
    }

    public function update()
{
    try {
        $produto = Produto::find($this->produtoId);
        
        if (!$produto) {
            session()->flash('error', 'Produto não encontrado.');
            return redirect()->route('tenant.produtos.index');
        }
        
        // Campos básicos
        $produto->nome = $this->nome;
        $produto->codigo = $this->codigo;
        $produto->categoria_id = $this->categoria_id;
        $produto->descricao = $this->descricao;
        $produto->preco = $this->preco;
        $produto->preco_promocional = $this->preco_promocional;
        $produto->ativo = $this->ativo;
        $produto->destaque = $this->destaque;
        $produto->estoque = $this->estoque ?? 0;
        
        // Campos com valor padrão (evitar null)
        $produto->unidade_medida = $this->unidade_medida ?: 'UN';
        $produto->origem = $this->origem ?: '0';
        $produto->estoque_minimo = $this->estoque_minimo ?? 0;
        
        $produto->icone = $this->icone;
        // Campos opcionais (podem ser null)
        $produto->preco_custo = $this->preco_custo ?: null;
        $produto->preco_meio = $this->preco_meio ?: null;
        $produto->tamanhos = !empty($this->tamanhos) ? $this->tamanhos : null;
        $produto->adicionais = !empty($this->adicionais) ? $this->adicionais : null;
        $produto->tipo_venda = $this->tipo_venda ?? 'unidade';
        $produto->permite_meio = $this->permite_meio ?? false;
        $produto->ncm = $this->ncm ?: null;
        $produto->cest = $this->cest ?: null;
        $produto->aliq_icms = $this->aliq_icms ?: null;
        $produto->aliq_ipi = $this->aliq_ipi ?: null;
        $produto->aliq_pis = $this->aliq_pis ?: null;
        $produto->aliq_cofins = $this->aliq_cofins ?: null;
        
        // Upload da imagem
        if ($this->imagem) {
            if ($this->imagemAtual && Storage::disk('public')->exists($this->imagemAtual)) {
                Storage::disk('public')->delete($this->imagemAtual);
            }
            $produto->imagem = $this->imagem->store('produtos', 'public');
        }
        
        $produto->save();
        
        session()->flash('success', 'Produto atualizado com sucesso!');
        return redirect()->route('tenant.produtos.index');
        
    } catch (\Exception $e) {
        session()->flash('error', 'Erro ao atualizar: ' . $e->getMessage());
        \Log::error('Erro ao atualizar produto: ' . $e->getMessage());
    }
}

    public function render()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $icones = IconsHelper::getIcons();

        return view('livewire.tenant.produtos.edit', [
            'categorias' => $categorias,
            'icones' => $icones,
        ])->layout('layouts.tenant');
    }
}