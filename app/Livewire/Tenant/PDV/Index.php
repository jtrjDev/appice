<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Comanda;
use App\Models\Tenant\Configuracao;
use App\Models\Tenant\ComandaItem;
use App\Models\Tenant\ComandaPagamento;
use App\Models\Tenant\PedidoItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Livewire\Traits\WithToast;
use Livewire\Attributes\Computed;
use App\Jobs\EmitirNotaFiscal;

/**
 * Componente principal do PDV (Ponto de Venda)
 * Gerencia todo o fluxo de vendas, carrinho, pagamentos e comandas
 */
class Index extends Component
{
    use WithToast;

    // ==========================================
    // PROPRIEDADES - MODAL DE NOTA FISCAL
    // ==========================================
    
    /** @var bool Controla exibição do modal de nota fiscal */
    public $mostrarModalNF = false;
    
    /** @var string CPF/CNPJ para emissão de nota */
    public $cpfCnpjNF = '';
    
    /** @var string Nome do cliente para nota fiscal */
    public $nomeClienteNF = '';
    
    /** @var string Tipo do documento (CPF/CNPJ) */
    public $tipoDocumentoNF = 'CPF';
    
    /** @var int|null ID temporário do pedido antes de emitir NF */
    public $pedidoTempId = null;

    // ==========================================
    // PROPRIEDADES - OPÇÕES DO PRODUTO (PESO, MEIA, TAMANHOS, ADICIONAIS)
    // ==========================================
    
    /** @var float Peso do produto (para sorveteria) */
    public $peso = 0;
    
    /** @var bool Indica se é meia porção (pizza, açaí) */
    public $meiaPorcao = false;
    
    /** @var string|null Tamanho selecionado do produto */
    public $tamanhoSelecionado = null;
    
    /** @var array Adicionais selecionados para o produto */
    public $adicionaisSelecionados = [];
    
    /** @var array Lista de tamanhos disponíveis por tipo de negócio */
    public $tamanhosDisponiveis = [];
    
    /** @var array Lista de adicionais disponíveis */
    public $adicionaisDisponiveis = [];

    // ==========================================
    // PROPRIEDADES - VALIDAÇÃO
    // ==========================================
    
    /** @var array Regras de validação para nota fiscal */
    protected $rules = [
        'cpfCnpjNF' => 'required|string|min:11|max:18',
        'nomeClienteNF' => 'required|string|min:3',
    ];

    // ==========================================
    // PROPRIEDADES - FILTROS E BUSCA
    // ==========================================
    
    /** @var int|null Categoria selecionada para filtro */
    public ?int $categoriaSelecionada = null;
    
    /** @var string Termo de busca de produtos */
    public string $busca = '';
    
    /** @var string Código do produto para busca rápida */
    public string $codigoProduto = '';
    
    /** @var float Quantidade padrão (suporta até 3 casas decimais) */
    public float $quantidadeInput = 1;

    // ==========================================
    // PROPRIEDADES - CARRINHO
    // ==========================================
    
    /** @var array Itens adicionados ao carrinho */
    public array $carrinho = [];

    // ==========================================
    // PROPRIEDADES - COMANDA / MESA
    // ==========================================
    
    /** @var int|null ID da comanda ativa */
    public ?int $comandaId = null;
    
    /** @var bool Indica se está em modo comanda (mesa) */
    public bool $modoComanda = false;

    // ==========================================
    // PROPRIEDADES - VENDA
    // ==========================================
    
    /** @var string|null Número da mesa (vem da URL) */
    #[Url]
    public ?string $mesa = null;
    
    /** @var string Número da comanda */
    public string $comanda = '';
    
    /** @var int|null ID do cliente selecionado */
    public ?int $clienteId = null;
    
    /** @var string Observações da venda */
    public string $observacao = '';

    // ==========================================
    // PROPRIEDADES - PAGAMENTO
    // ==========================================
    
    /** @var bool Controla exibição do modal de pagamento */
    public bool $mostrarPagamento = false;
    
    /** @var array Lista de pagamentos realizados (split) */
    public array $pagamentos = [];
    
    /** @var float Valor pendente a pagar */
    public float $valorPendente = 0;
    
    /** @var float Valor atual sendo pago */
    public float $valorPagamento = 0;
    
    /** @var string Forma de pagamento selecionada */
    public string $formaPagamento = 'dinheiro';
    
    /** @var int|null ID do usuário logado no tenant */
    public ?int $tenantUserId = null;

    // ==========================================
    // PROPRIEDADES - CONFIGURAÇÕES DO TENANT
    // ==========================================
    
    /** @var object|null Dados de configuração do tenant */
    public $configuracao;


    public array $pizzaMetadeA = [];
    public array $pizzaMetadeB = [];

    public string $tamanhoPizza = 'media';
    public string $regraPrecoMeiaPizza = 'maior';
    public string $observacaoPizza = '';

    // ==========================================
    // MÉTODOS - INICIALIZAÇÃO
    // ==========================================
    
    /**
     * Inicializa o componente PDV
     * Carrega sessão, configurações do tenant e opções de produto
     */
    public function mount(): void
    {
        // Busca o ID do usuário logado no tenant
        $this->tenantUserId = \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id');
        
        // Carrega carrinho da sessão
        $this->carrinho = session('pdv_carrinho', []);
        
        // Reseta pagamentos
        $this->pagamentos = [];
        $this->valorPendente = 0;
        $this->valorPagamento = 0;
        $this->formaPagamento = 'dinheiro';
        $this->mostrarModalNF = false;
        
        // Carrega configurações do tenant (tipo de negócio)
        $this->configuracao = Configuracao::first();

        // Configura tamanhos padrão baseado no tipo de negócio
        if ($this->configuracao && $this->configuracao->tipo_negocio == 'pizzaria') {
            $this->tamanhosDisponiveis = ['Pequena', 'Média', 'Grande', 'Família'];
        } elseif ($this->configuracao && $this->configuracao->tipo_negocio == 'sorveteria') {
            $this->tamanhosDisponiveis = ['Pequeno', 'Médio', 'Grande'];
        }
        
        // Adicionais padrão (futuramente podem vir do banco)
        $this->adicionaisDisponiveis = [
            ['nome' => 'Queijo extra', 'preco' => 2.00],
            ['nome' => 'Bacon', 'preco' => 3.00],
            ['nome' => 'Cheddar', 'preco' => 2.50],
        ];

        // Se veio com parâmetro de mesa na URL, carrega a comanda
        if ($this->mesa) {
            $this->updatedMesa();
        }

        // Calcula valores pendentes iniciais
        $this->recalcularPendente();
    }

    public function selecionarMetadePizza($produtoId, string $lado): void
{
    $produto = Produto::findOrFail($produtoId);

    $dados = [
        'id' => $produto->id,
        'nome' => $produto->nome,
        'preco' => (float) $produto->preco_atual,
    ];

    if ($lado === 'A') {
        $this->pizzaMetadeA = $dados;
    }

    if ($lado === 'B') {
        $this->pizzaMetadeB = $dados;
    }
}

public function limparMeiaPizza(): void
{
    $this->pizzaMetadeA = [];
    $this->pizzaMetadeB = [];
    $this->observacaoPizza = '';
    $this->tamanhoPizza = 'media';
    $this->regraPrecoMeiaPizza = 'maior';
}

public function adicionarMeiaPizza(): void
{
    if (empty($this->pizzaMetadeA) || empty($this->pizzaMetadeB)) {
        return;
    }

    $precoA = (float) $this->pizzaMetadeA['preco'];
    $precoB = (float) $this->pizzaMetadeB['preco'];

    $valor = $this->regraPrecoMeiaPizza === 'media'
        ? (($precoA + $precoB) / 2)
        : max($precoA, $precoB);

    $chave = 'meia_' . $this->pizzaMetadeA['id'] . '_' . $this->pizzaMetadeB['id'] . '_' . uniqid();

    $nome = 'Pizza Meio a Meio: '
        . $this->pizzaMetadeA['nome']
        . ' / '
        . $this->pizzaMetadeB['nome']
        . ' - '
        . ucfirst($this->tamanhoPizza);

    $this->carrinho[$chave] = [
        'id' => $chave,
        'produto_id' => null,
        'tipo' => 'meia_pizza',
        'nome' => $nome,
        'quantidade' => 1,
        'preco' => $valor,
        'preco_formatado' => 'R$ ' . number_format($valor, 2, ',', '.'),
        'subtotal' => $valor,
        'observacao' => $this->observacaoPizza,
        'metades' => [
            'a' => $this->pizzaMetadeA,
            'b' => $this->pizzaMetadeB,
        ],
        'tamanho' => $this->tamanhoPizza,
        'regra_preco' => $this->regraPrecoMeiaPizza,
    ];

    $this->limparMeiaPizza();

    $this->dispatch('focar-codigo');
}

    // ==========================================
    // MÉTODOS - CÁLCULOS
    // ==========================================
    
    /**
     * Recalcula o valor pendente baseado nos pagamentos e comanda
     * @param bool $ajustarValorPagamento Se deve ajustar o campo valorPagamento
     */
    private function recalcularPendente(bool $ajustarValorPagamento = true): void
    {
        // Soma dos pagamentos já feitos localmente
        $totalPagoLocal = round((float) collect($this->pagamentos)->sum('valor'), 2);
        $totalPagoComanda = 0;

        // Se está em modo comanda, busca pagamentos já realizados
        if ($this->modoComanda && $this->comandaId) {
            $comanda = Comanda::find($this->comandaId);
            $totalPagoComanda = (float) ($comanda?->total_pago ?? 0);
        }

        // Calcula pendente total
        $this->valorPendente = max(
            0,
            round($this->totalCarrinho - $totalPagoComanda - $totalPagoLocal, 2)
        );

        // Ajusta o campo de valor pagamento se necessário
        if ($ajustarValorPagamento) {
            $this->valorPagamento = $this->valorPendente > 0 ? $this->valorPendente : 0;
        }
    }

    // ==========================================
    // PROPRIEDADES COMPUTADAS
    // ==========================================
    
    /**
     * Calcula o total do carrinho
     * @return float
     */
    #[Computed]
    public function totalCarrinho(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'subtotal'));
    }

    /**
     * Verifica se existe um caixa aberto
     * @return Caixa|null
     */
    #[Computed]
    public function caixaAberto(): ?Caixa
    {
        return Caixa::caixaAberto();
    }

    /**
     * Calcula o total de itens no carrinho
     * @return float
     */
    #[Computed]
    public function totalItens(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'quantidade'));
    }

    // ==========================================
    // MÉTODOS - CATEGORIAS
    // ==========================================
    
    /**
     * Filtra produtos por categoria
     * @param int|null $categoriaId ID da categoria ou null para todos
     */
    public function selecionarCategoria(?int $categoriaId): void
    {
        $this->categoriaSelecionada = $categoriaId;
    }

    // ==========================================
    // MÉTODOS - ADICIONAR PRODUTO
    // ==========================================
    
    /**
     * Adiciona um produto ao carrinho com suas opções (peso, meia, tamanho, adicionais)
     * @param int $produtoId ID do produto
     * @param float|null $quantidade Quantidade (opcional)
     */
    public function adicionarProduto(int $produtoId, ?float $quantidade = null): void
    {
        $produto = Produto::find($produtoId);

        if (!$produto) {
            $this->toastError('Produto não encontrado!');
            return;
        }

        $quantidade = $quantidade ?? $this->quantidadeInput;
        $quantidade = max(0.01, (float) $quantidade);
        
        $precoUnitario = $produto->preco_atual;
        $observacao = '';
        $valorAdicionais = 0;

        // Ajusta preço baseado nas opções selecionadas
        $precoUnitario = $this->calcularPrecoComOpcoes($produto, $precoUnitario, $observacao, $valorAdicionais);
        
        // Calcula valor total do item
        $valorTotal = ($precoUnitario * $quantidade) + $valorAdicionais;
        
        $chave = (string) $produtoId;
        
        // Se produto já existe no carrinho, apenas incrementa
        if (isset($this->carrinho[$chave])) {
            $this->carrinho[$chave]['quantidade'] += $quantidade;
            $this->carrinho[$chave]['subtotal'] = round(
                $this->carrinho[$chave]['preco'] * $this->carrinho[$chave]['quantidade'],
                2
            );
            if ($observacao && !isset($this->carrinho[$chave]['observacao'])) {
                $this->carrinho[$chave]['observacao'] = $observacao;
            }
        } else {
            // Cria novo item no carrinho
            $this->carrinho[$chave] = [
                'id'              => $produto->id,
                'nome'            => $produto->nome,
                'preco'           => $precoUnitario,
                'preco_formatado' => 'R$ ' . number_format($precoUnitario, 2, ',', '.'),
                'quantidade'      => $quantidade,
                'subtotal'        => round($valorTotal, 2),
                'observacao'      => $observacao ?: null,
            ];
        }

        // Reseta campos do produto
        $this->resetarOpcoesProduto();

        $this->salvarCarrinho();
        $this->recalcularPendente();
        $this->toastSuccess("Produto {$produto->nome} adicionado!");
    }

    /**
     * Calcula o preço baseado nas opções selecionadas (peso, meia porção, tamanhos)
     * @param object $produto Produto atual
     * @param float $precoBase Preço base do produto
     * @param string $observacao Referência para montar observação
     * @param float $valorAdicionais Referência para somar adicionais
     * @return float Preço final calculado
     */
    private function calcularPrecoComOpcoes($produto, $precoBase, &$observacao, &$valorAdicionais)
    {
        // 1. Verifica venda por peso (sorveteria)
        if ($this->peso > 0) {
            $this->quantidadeInput = $this->peso;
            $observacao .= "⚖️ Peso: {$this->peso}kg";
            return $precoBase;
        }

        // 2. Verifica meia porção (pizzaria, açaí)
        if ($this->meiaPorcao && $produto->permite_meio && $produto->preco_meio) {
            $precoBase = $produto->preco_meio;
            $observacao .= ($observacao ? ' | ' : '') . '🍕 Meia porção';
        }

        // 3. Verifica tamanho selecionado
        if ($this->tamanhoSelecionado && $produto->tamanhos) {
            $tamanhoEncontrado = collect($produto->tamanhos)->firstWhere('nome', $this->tamanhoSelecionado);
            if ($tamanhoEncontrado) {
                $precoBase = $tamanhoEncontrado['preco'];
                $observacao .= ($observacao ? ' | ' : '') . "📏 Tamanho: {$this->tamanhoSelecionado}";
            }
        }

        // 4. Verifica adicionais
        if (!empty($this->adicionaisSelecionados)) {
            $adicionaisTexto = [];
            foreach ($this->adicionaisSelecionados as $adicionalNome) {
                $adicional = collect($this->adicionaisDisponiveis)->firstWhere('nome', $adicionalNome);
                if ($adicional) {
                    $valorAdicionais += $adicional['preco'];
                    $adicionaisTexto[] = $adicionalNome;
                }
            }
            if (!empty($adicionaisTexto)) {
                $observacao .= ($observacao ? ' | ' : '') . '➕ Adicionais: ' . implode(', ', $adicionaisTexto);
            }
        }

        return $precoBase;
    }

    /**
     * Reseta todas as opções do produto após adicionar ao carrinho
     */
    private function resetarOpcoesProduto()
    {
        $this->quantidadeInput = 1;
        $this->peso = 0;
        $this->meiaPorcao = false;
        $this->tamanhoSelecionado = null;
        $this->adicionaisSelecionados = [];
    }

    // ==========================================
    // MÉTODOS - CARRINHO
    // ==========================================
    
    /**
     * Remove um produto do carrinho
     * @param int $produtoId ID do produto
     */
    public function removerProduto(int $produtoId): void
    {
        $chave = (string) $produtoId;
        unset($this->carrinho[$chave]);

        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    /**
     * Atualiza a quantidade de um produto no carrinho
     * @param int $produtoId ID do produto
     * @param mixed $quantidade Nova quantidade
     */
    public function atualizarQuantidade(int $produtoId, mixed $quantidade): void
    {
        $chave = (string) $produtoId;
        $quantidade = (float) $quantidade;

        if (!isset($this->carrinho[$chave])) {
            return;
        }

        if ($quantidade <= 0) {
            $this->removerProduto($produtoId);
            return;
        }

        $this->carrinho[$chave]['quantidade'] = $quantidade;
        $this->carrinho[$chave]['subtotal'] = round(
            $this->carrinho[$chave]['preco'] * $quantidade,
            2
        );

        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    /**
     * Remove todos os itens do carrinho
     */
    public function limparCarrinho(): void
    {
        $this->carrinho = [];
        $this->pagamentos = [];
        $this->valorPendente = 0;
        $this->valorPagamento = 0;

        session()->forget('pdv_carrinho');
        $this->toastInfo('Carrinho limpo!');
    }

    /**
     * Salva o carrinho na sessão
     */
    private function salvarCarrinho(): void
    {
        session()->put('pdv_carrinho', $this->carrinho);
    }

    // ==========================================
    // MÉTODOS - BUSCA POR CÓDIGO
    // ==========================================
    
    /**
     * Busca produto por código ou ID e adiciona ao carrinho
     */
    public function buscarPorCodigo(): void
    {
        $codigo = trim($this->codigoProduto);
        
        if (strlen($codigo) < 1) {
            return;
        }
        
        $produto = Produto::where('codigo', $codigo)->first();
        
        if ($produto) {
            $this->adicionarProduto($produto->id, $this->quantidadeInput);
            $this->codigoProduto = '';
            $this->quantidadeInput = 1;
            $this->dispatch('focar-codigo');
        } else {
            $this->toastWarning("Produto não encontrado: {$codigo}");
        }
    }

    // ==========================================
    // MÉTODOS - ABRIR/FECHAR PAGAMENTO
    // ==========================================
    
    /**
     * Abre o modal de pagamento
     */
    public function abrirPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio! Adicione produtos primeiro.');
            return;
        }

        // Se está em modo comanda, calcula valores da comanda
        if ($this->modoComanda) {
            $this->salvarComanda(false);
            $comanda = Comanda::find($this->comandaId);
            $this->valorPendente = $comanda ? (float) $comanda->total_restante : $this->totalCarrinho;
            $this->valorPagamento = $this->valorPendente;
        } else {
            $this->recalcularPendente();
        }

        $this->mostrarPagamento = true;
    }

    /**
     * Fecha o modal de pagamento
     */
    public function fecharModalPagamento(): void
    {
        $this->mostrarPagamento = false;
    }

    /**
     * Ação do atalho F5 - Adiciona pagamento ou finaliza
     */
    public function acaoF5(): void
    {
        if ($this->modoComanda) {
            $this->adicionarPagamento();
            return;
        }

        $this->recalcularPendente(false);

        if ($this->valorPendente > 0) {
            $this->adicionarPagamento();
        } else {
            $this->finalizarVenda();
        }
    }

    // ==========================================
    // MÉTODOS - PAGAMENTO (SPLIT)
    // ==========================================
    
    /**
     * Adiciona um pagamento ao split
     */
    public function adicionarPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Insira ao menos um item no carrinho!');
            return;
        }

        // Tenta recuperar comanda pela mesa
        if (!$this->comandaId && $this->mesa) {
            $comanda = Comanda::buscarMesa($this->mesa);
            if ($comanda) {
                $this->comandaId = $comanda->id;
                $this->modoComanda = true;
            }
        }

        $this->recalcularPendente(false);

        $valor = round((float) $this->valorPagamento, 2);

        if ($valor <= 0) {
            $this->toastWarning('Informe um valor para pagamento!');
            return;
        }

        // Modo comanda
        if ($this->modoComanda && $this->comandaId) {
            $this->pagarParcialComanda();
            $this->recalcularPendente();
            return;
        }

        if ($this->valorPendente <= 0) {
            $this->toastInfo('Valores inseridos, agora finalize a venda.');
            return;
        }

        $troco = 0;

        // Calcula troco se for dinheiro e valor maior que o pendente
        if ($this->formaPagamento === 'dinheiro' && $valor > $this->valorPendente) {
            $troco = round($valor - $this->valorPendente, 2);
            $valorEfetivo = $this->valorPendente;
        } else {
            $valorEfetivo = min($valor, $this->valorPendente);
        }

        // Adiciona pagamento à lista
        $this->pagamentos[] = [
            'forma' => $this->formaPagamento,
            'valor' => round($valorEfetivo, 2),
            'troco' => $troco,
        ];

        $this->recalcularPendente();

        if ($this->valorPendente > 0) {
            $this->toastSuccess("Pagamento adicionado! Restante: R$ " . number_format($this->valorPendente, 2, ',', '.'));
        } else {
            $this->toastSuccess('Pagamento completo! Agora clique em Finalizar Venda.');
        }
    }

    /**
     * Remove um pagamento da lista
     * @param int $index Índice do pagamento na lista
     */
    public function removerPagamento(int $index): void
    {
        if (!isset($this->pagamentos[$index])) {
            return;
        }

        array_splice($this->pagamentos, $index, 1);
        $this->recalcularPendente();
        $this->toastInfo('Pagamento removido.');
    }

    // ==========================================
    // MÉTODOS - FINALIZAÇÃO DA VENDA
    // ==========================================
    
    /**
     * Finaliza a venda, cria pedido e atualiza caixa
     */
    public function finalizarVenda(): void
    {
        // Validação do carrinho
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio!');
            return;
        }

        $this->recalcularPendente(false);

        // Verifica se o pagamento está completo
        if (!$this->modoComanda && $this->valorPendente > 0) {
            $this->toastWarning('Ainda falta R$ ' . number_format($this->valorPendente, 2, ',', '.') . ' para concluir a venda.');
            return;
        }

        // Verifica se há caixa aberto
        $caixa = Caixa::caixaAberto();

        if (!$caixa) {
            $this->toastError('Nenhum caixa aberto! Abra o caixa antes de vender.');
            $this->mostrarPagamento = false;
            return;
        }

        DB::beginTransaction();

        try {
            $subtotal = $this->totalCarrinho;

            // Cria o pedido
            $pedido = Pedido::create([
                'caixa_id'      => $caixa->id,
                'numero_pedido' => Pedido::gerarNumero(),
                'cliente_id'    => $this->clienteId ?: null,
                'tipo'          => 'balcao',
                'mesa'          => $this->mesa ?: null,
                'subtotal'      => $subtotal,
                'taxa_entrega'  => 0,
                'desconto'      => 0,
                'total'         => $subtotal,
                'status'        => 'entregue',
                'pagamentos'    => $this->pagamentos,
                'atendente_id'  => $this->tenantUserId ?? 1,
            ]);

            // Cria os itens do pedido
            foreach ($this->carrinho as $item) {
                PedidoItem::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $item['id'],
                    'produto_nome'   => $item['nome'],
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'subtotal'       => $item['subtotal'],
                ]);
            }

            // Atualiza totais do caixa
            $totaisPagamentos = collect($this->pagamentos)
                ->groupBy('forma')
                ->map(fn($grupo) => $grupo->sum('valor'));

            $caixa->increment('total_vendas', $subtotal);
            $caixa->increment('quantidade_vendas');
            $caixa->increment('total_dinheiro', $totaisPagamentos->get('dinheiro', 0));
            $caixa->increment('total_credito', $totaisPagamentos->get('cartao_credito', 0));
            $caixa->increment('total_debito', $totaisPagamentos->get('cartao_debito', 0));
            $caixa->increment('total_pix', $totaisPagamentos->get('pix', 0));
            
            DB::commit();

            // Limpa o carrinho e reseta estados
            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->mesa = '';
            $this->comanda = '';
            $this->observacao = '';
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->formaPagamento = 'dinheiro';

            // Verifica se deve emitir nota fiscal
            $config = Configuracao::first();
            
            // Se já tem cliente com CPF/CNPJ, emite automaticamente
            if ($pedido->cliente_id && $pedido->cliente && $pedido->cliente->cpf_cnpj) {
                EmitirNotaFiscal::dispatch($pedido->id, tenant()->id);
                $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado! NF solicitada.");
            } 
            // Se configurado para emitir NF, pergunta os dados
            elseif ($config && $config->emitir_nf_automatico) {
                $this->pedidoTempId = $pedido->id;
                $this->mostrarModalNF = true;
                $this->toastInfo("Deseja emitir nota fiscal? Preencha os dados.");
            } 
            else {
                $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado!");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao finalizar venda: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS - NOTA FISCAL
    // ==========================================
    
    /**
     * Emite nota fiscal após finalizar a venda (modal)
     */
    public function emitirNotaDaVenda()
    {
        $this->validate([
            'cpfCnpjNF' => 'required|string|min:11|max:18',
            'nomeClienteNF' => 'required|string|min:3',
        ]);
        
        $cpfCnpj = preg_replace('/[^0-9]/', '', $this->cpfCnpjNF);
        
        // Cria ou busca cliente
        $cliente = Cliente::updateOrCreate(
            ['cpf_cnpj' => $cpfCnpj],
            ['nome' => $this->nomeClienteNF, 'ativo' => true]
        );
        
        // Associa ao pedido
        $pedido = Pedido::find($this->pedidoTempId);
        $pedido->cliente_id = $cliente->id;
        $pedido->save();
        
        // Dispara job de emissão
        EmitirNotaFiscal::dispatch($pedido->id, tenant()->id);
        
        $this->mostrarModalNF = false;
        $this->pedidoTempId = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
        
        $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado com NF solicitada!");
    }

    /**
     * Finaliza venda sem nota fiscal
     */
    public function finalizarSemNF()
    {
        $this->mostrarModalNF = false;
        $this->pedidoTempId = null;
        $this->reset(['cpfCnpjNF', 'nomeClienteNF']);
        $this->toastSuccess("Pedido finalizado sem nota fiscal!");
    }

    // ==========================================
    // MÉTODOS - COMANDA / MESA
    // ==========================================
    
    /**
     * Atualiza a mesa selecionada
     * Carrega itens da comanda se existir
     */
    public function updatedMesa(): void
    {
        $mesa = trim($this->mesa);
        
        if (strlen($mesa) < 1) {
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->carrinho = [];
            $this->pagamentos = [];
            $this->salvarCarrinho();
            $this->recalcularPendente();
            return;
        }

        $comanda = Comanda::buscarMesa($mesa);

        if ($comanda) {
            // Carrega comanda existente
            $this->comandaId = $comanda->id;
            $this->modoComanda = true;
            $this->carrinho = [];
            $this->pagamentos = [];

            foreach ($comanda->itens as $item) {
                $chave = (string) $item->produto_id;

                $this->carrinho[$chave] = [
                    'id'              => $item->produto_id,
                    'nome'            => $item->produto_nome,
                    'preco'           => (float) $item->preco_unitario,
                    'preco_formatado' => 'R$ ' . number_format($item->preco_unitario, 2, ',', '.'),
                    'quantidade'      => (float) $item->quantidade,
                    'subtotal'        => (float) $item->subtotal,
                ];
            }

            $this->salvarCarrinho();
            $this->recalcularPendente();
            $this->toastSuccess("Mesa {$mesa} carregada com sucesso!");
        } else {
            // Cria nova comanda
            $this->modoComanda = true;
            $this->comandaId = null;
            $this->carrinho = [];
            $this->pagamentos = [];
            $this->salvarCarrinho();
            $this->recalcularPendente();
            $this->toastInfo("Nova mesa {$mesa} criada. Adicione os itens.");
        }
    }

    /**
     * Salva a comanda atual
     * @param bool $limparAposalvar Se deve limpar o carrinho após salvar
     */
    public function salvarComanda(bool $limparAposalvar = true): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio! Adicione itens para salvar a mesa.');
            return;
        }

        $caixa = Caixa::caixaAberto();
        $total = $this->totalCarrinho;

        if ($this->comandaId) {
            $comanda = Comanda::find($this->comandaId);
            $comanda->itens()->delete();
        } else {
            $comanda = Comanda::buscarMesa($this->mesa);

            if (!$comanda) {
                $comanda = Comanda::create([
                    'caixa_id'   => $caixa?->id,
                    'mesa'       => $this->mesa,
                    'status'     => 'aberta',
                    'total'      => $total,
                    'total_pago' => 0,
                ]);
            } else {
                $comanda->itens()->delete();
            }

            $this->comandaId = $comanda->id;
        }

        // Salva itens da comanda
        foreach ($this->carrinho as $item) {
            ComandaItem::create([
                'comanda_id'     => $comanda->id,
                'produto_id'     => $item['id'],
                'produto_nome'   => $item['nome'],
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $item['preco'],
                'subtotal'       => $item['subtotal'],
            ]);
        }

        $comanda->update(['total' => $total]);

        if ($limparAposalvar) {
            $mesaSalva = $comanda->mesa;

            $this->limparCarrinho();
            $this->mesa = '';
            $this->modoComanda = false;
            $this->comandaId = null;
            $this->formaPagamento = 'dinheiro';

            $this->toastSuccess("Mesa {$mesaSalva} salva! Continue depois.");
        } else {
            $this->recalcularPendente();
            $this->toastSuccess("Itens adicionados à mesa {$comanda->mesa}.");
        }
    }

    /**
     * Registra pagamento parcial em uma comanda
     */
    public function pagarParcialComanda(): void
    {
        if (!$this->comandaId) {
            $this->salvarComanda(false);
        }

        $comanda = Comanda::find($this->comandaId);

        if (!$comanda) {
            $this->toastError('Comanda não encontrada.');
            return;
        }

        $valorInformado = round((float) $this->valorPagamento, 2);

        if ($valorInformado <= 0) {
            $this->toastWarning('Informe um valor para pagamento.');
            return;
        }

        $restante = (float) $comanda->total_restante;
        $troco = 0;

        if ($this->formaPagamento === 'dinheiro' && $valorInformado > $restante) {
            $troco = round($valorInformado - $restante, 2);
            $valorEfetivo = $restante;
        } else {
            $valorEfetivo = min($valorInformado, $restante);
        }

        if ($valorEfetivo <= 0) {
            $this->toastWarning('Valor inválido!');
            return;
        }

        // Registra pagamento
        ComandaPagamento::create([
            'comanda_id' => $comanda->id,
            'forma'      => $this->formaPagamento,
            'valor'      => $valorEfetivo,
        ]);

        $comanda->increment('total_pago', $valorEfetivo);
        $comanda->refresh();

        if ($comanda->total_restante <= 0) {
            $this->fecharComanda($comanda);
            return;
        }

        $this->valorPendente = (float) $comanda->total_restante;
        $this->valorPagamento = (float) $comanda->total_restante;

        $mensagem = "Pagamento lançado! Restante: R$ " . number_format($comanda->total_restante, 2, ',', '.');

        if ($troco > 0) {
            $mensagem .= " | Troco: R$ " . number_format($troco, 2, ',', '.');
        }

        $this->toastSuccess($mensagem);
    }

    /**
     * Fecha uma comanda e gera o pedido final
     * @param Comanda $comanda Comanda a ser fechada
     */
    private function fecharComanda(Comanda $comanda): void
    {
        DB::beginTransaction();

        try {
            $comanda->update([
                'status'     => 'fechada',
                'fechada_em' => now(),
            ]);

            $caixa = Caixa::caixaAberto();

            if ($caixa) {
                $pagamentos = $comanda->pagamentos->map(fn($p) => [
                    'forma' => $p->forma,
                    'valor' => (float) $p->valor,
                    'troco' => 0,
                ])->toArray();

                $pedido = Pedido::create([
                    'caixa_id'      => $caixa->id,
                    'numero_pedido' => Pedido::gerarNumero(),
                    'tipo'          => 'mesa',
                    'mesa'          => $comanda->mesa,
                    'subtotal'      => $comanda->total,
                    'taxa_entrega'  => 0,
                    'desconto'      => 0,
                    'total'         => $comanda->total,
                    'status'        => 'entregue',
                    'pagamentos'    => $pagamentos,
                    'atendente_id'  => $this->tenantUserId ?? 1,
                ]);

                foreach ($comanda->itens as $item) {
                    PedidoItem::create([
                        'pedido_id'      => $pedido->id,
                        'produto_id'     => $item->produto_id,
                        'produto_nome'   => $item->produto_nome,
                        'quantidade'     => $item->quantidade,
                        'preco_unitario' => $item->preco_unitario,
                        'subtotal'       => $item->subtotal,
                    ]);
                }

                $totais = $comanda->pagamentos
                    ->groupBy('forma')
                    ->map(fn($grupo) => $grupo->sum('valor'));

                $caixa->increment('total_vendas', (float) $comanda->total);
                $caixa->increment('quantidade_vendas');
                $caixa->increment('total_dinheiro', $totais->get('dinheiro', 0));
                $caixa->increment('total_credito', $totais->get('cartao_credito', 0));
                $caixa->increment('total_debito', $totais->get('cartao_debito', 0));
                $caixa->increment('total_pix', $totais->get('pix', 0));
            }

            DB::commit();

            $mesaFechada = $comanda->mesa;

            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->mesa = '';
            $this->comanda = '';
            $this->comandaId = null;
            $this->modoComanda = false;
            $this->formaPagamento = 'dinheiro';

            $this->toastSuccess("Mesa {$mesaFechada} fechada com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro ao fechar comanda: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS - RENDERIZAÇÃO
    // ==========================================
    
    /**
     * Renderiza a view do PDV
     * @return \Illuminate\View\View
     */
   public function render()
{
    // Busca categorias ativas
    $categorias = Categoria::query()
        ->where('ativo', true)
        ->orderBy('nome')
        ->get();

    // Busca produtos ativos com filtros
    $produtos = Produto::query()
        ->where('ativo', true)
        ->when($this->categoriaSelecionada, fn($q) => $q->where('categoria_id', $this->categoriaSelecionada))
        ->when($this->busca, fn($q) => $q->where(function ($q2) {
            $q2->where('nome', 'like', '%' . $this->busca . '%')
                ->orWhere('codigo', 'like', '%' . $this->busca . '%');
        }))
        ->orderBy('nome')
        ->get();

    // Busca clientes ativos
    $clientes = Cliente::query()
        ->where('ativo', true)
        ->orderBy('nome')
        ->get();

    // Carrega configurações
    $config = Configuracao::first();
    $tipo = $config->tipo_negocio ?? 'lanchonete';
    
    // Configurações do PDV (com tipo_negocio incluído!)
    $pdvConfig = [
        'tipo_venda_padrao' => $this->configuracao->tipo_venda_padrao ?? 'unidade',
        'unidade_padrao' => $this->configuracao->unidade_medida_padrao ?? 'UN',
        'permite_meio' => $this->configuracao->permite_meia_porcao ?? false,
        'exibir_tamanhos' => in_array($tipo, ['pizzaria', 'sorveteria']),
        'exibir_adicionais' => in_array($tipo, ['lanchonete', 'pizzaria']),
        'tipo_negocio' => $tipo, // <<< ADICIONE ESTA LINHA
    ];

    return view('livewire.tenant.pdv.index', [
        'categorias' => $categorias,
        'produtos' => $produtos,
        'clientes' => $clientes,
        'pdvConfig' => $pdvConfig,
    ])->layout('layouts.tenant');
}
}