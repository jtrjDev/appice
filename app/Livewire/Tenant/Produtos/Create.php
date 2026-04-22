<?php

namespace App\Livewire\Tenant\Produtos;

use App\Models\Tenant\Produto;
use App\Models\Tenant\Categoria;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    // Dados básicos
    public $nome = '';
    public $codigo = '';
    public $categoria_id = '';
    public $descricao = '';
    
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
    
    // Status
    public $ativo = true;
    public $destaque = false;
    
    // Aba ativa
    public $activeTab = 'basico';

    // Variáveis auxiliares para tamanhos e adicionais
    public $novoTamanho = ['nome' => '', 'preco' => ''];
    public $novoAdicional = ['nome' => '', 'preco' => ''];

    protected $rules = [
        'nome' => 'required|string|max:255',
        'codigo' => 'nullable|string|max:50|unique:produtos,codigo',
        'categoria_id' => 'required|exists:categorias,id',
        'descricao' => 'nullable|string',
        'preco' => 'required|numeric|min:0',
        'preco_promocional' => 'nullable|numeric|min:0|lt:preco',
        'preco_custo' => 'nullable|numeric|min:0',
        'tipo_venda' => 'required|in:unidade,peso,fracionado',
        'permite_meio' => 'boolean',
        'preco_meio' => 'required_if:permite_meio,true|nullable|numeric|min:0',
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

    protected $messages = [
        'nome.required' => 'O nome do produto é obrigatório.',
        'categoria_id.required' => 'Selecione uma categoria.',
        'preco.required' => 'Informe o preço do produto.',
        'preco_promocional.lt' => 'O preço promocional deve ser menor que o preço normal.',
        'codigo.unique' => 'Este código já está em uso.',
    ];

    public function mount()
    {
        $this->tamanhos = [];
        $this->adicionais = [];
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

    public function save()
{
    try {
        // Validação básica apenas
        $this->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'preco' => 'required|numeric|min:0',
        ]);
        
        // Preparar dados
        $data = [
            'nome' => $this->nome,
            'slug' => Str::slug($this->nome),
            'codigo' => $this->codigo,
            'categoria_id' => $this->categoria_id,
            'descricao' => $this->descricao,
            'preco' => $this->preco,
            'preco_promocional' => $this->preco_promocional ?: null,
            'preco_custo' => $this->preco_custo ?: null,
            'tipo_venda' => $this->tipo_venda,
            'permite_meio' => $this->permite_meio,
            'preco_meio' => $this->preco_meio ?: null,
            'tamanhos' => !empty($this->tamanhos) ? $this->tamanhos : null,
            'adicionais' => !empty($this->adicionais) ? $this->adicionais : null,
            'estoque' => $this->estoque ?? 0,
            'estoque_minimo' => $this->estoque_minimo ?? 0,
            'ncm' => $this->ncm ?: null,
            'cest' => $this->cest ?: null,
            'origem' => $this->origem,
            'aliq_icms' => $this->aliq_icms ?: null,
            'aliq_ipi' => $this->aliq_ipi ?: null,
            'aliq_pis' => $this->aliq_pis ?: null,
            'aliq_cofins' => $this->aliq_cofins ?: null,
            'unidade_medida' => $this->unidade_medida,
            'ativo' => $this->ativo,
            'destaque' => $this->destaque,
        ];

        // Remover campos vazios que podem causar erro
        $data = array_filter($data, function($value) {
            return !is_null($value);
        });

        if ($this->imagem) {
            $data['imagem'] = $this->imagem->store('produtos', 'public');
        }

        $produto = Produto::create($data);
        
        session()->flash('success', 'Produto "' . $this->nome . '" criado com sucesso! ID: ' . $produto->id);
        return redirect()->route('tenant.produtos.index');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Erro de validação - vai mostrar automaticamente
        throw $e;
    } catch (\Exception $e) {
        session()->flash('error', 'Erro: ' . $e->getMessage());
        \Log::error('Erro criar produto: ' . $e->getMessage());
    }
}

    public function render()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        
        return view('livewire.tenant.produtos.create', [
            'categorias' => $categorias,
        ])->layout('layouts.tenant');
    }
}