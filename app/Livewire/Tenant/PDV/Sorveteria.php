<?php

namespace App\Livewire\Tenant\PDV;

use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Caixa;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\PedidoItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Livewire\Traits\WithToast;
use Livewire\Attributes\Computed;

class Sorveteria extends Component
{
    use WithToast;

    // Filtros
    public ?int $categoriaSelecionada = null;
    public string $busca = '';
    public string $codigoProduto = '';
    public float $quantidadeInput = 1;
    public float $peso = 0;

    // Carrinho
    public array $carrinho = [];

    // Venda
    public ?int $clienteId = null;
    public string $observacao = '';

    // Pagamento
    public bool $mostrarPagamento = false;
    public array $pagamentos = [];
    public float $valorPendente = 0;
    public float $valorPagamento = 0;
    public string $formaPagamento = 'dinheiro';
    public ?int $tenantUserId = null;

    public function mount(): void
    {
        $this->tenantUserId = \App\Models\Tenant\User::where('email', auth()->user()->email)->value('id');
        $this->carrinho = session('pdv_carrinho', []);
        $this->pagamentos = [];
        $this->valorPendente = 0;
        $this->valorPagamento = 0;
        $this->recalcularPendente();
    }

    private function recalcularPendente(): void
    {
        $totalPago = round((float) collect($this->pagamentos)->sum('valor'), 2);
        $this->valorPendente = max(0, round($this->totalCarrinho - $totalPago, 2));
        $this->valorPagamento = $this->valorPendente > 0 ? $this->valorPendente : 0;
    }

    #[Computed]
    public function totalCarrinho(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'subtotal'));
    }

    #[Computed]
    public function caixaAberto(): ?Caixa
    {
        return Caixa::caixaAberto();
    }

    #[Computed]
    public function totalItens(): float
    {
        return (float) array_sum(array_column($this->carrinho, 'quantidade'));
    }

    public function selecionarCategoria(?int $categoriaId): void
    {
        $this->categoriaSelecionada = $categoriaId;
    }

    public function adicionarProduto(int $produtoId, ?float $quantidade = null): void
    {
        $produto = Produto::find($produtoId);
        if (!$produto) {
            $this->toastError('Produto não encontrado!');
            return;
        }

        $quantidade = $quantidade ?? $this->quantidadeInput;
        $quantidade = max(0.01, (float) $quantidade);
        
        // Se tem peso, usa o peso como quantidade
        if ($this->peso > 0) {
            $quantidade = $this->peso;
        }
        
        $preco = (float) $produto->preco_atual;
        $valorTotal = $preco * $quantidade;
        
        $chave = (string) $produtoId;
        
        if (isset($this->carrinho[$chave])) {
            $this->carrinho[$chave]['quantidade'] += $quantidade;
            $this->carrinho[$chave]['subtotal'] = round($this->carrinho[$chave]['preco'] * $this->carrinho[$chave]['quantidade'], 2);
        } else {
            $this->carrinho[$chave] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'preco' => $preco,
                'preco_formatado' => 'R$ ' . number_format($preco, 2, ',', '.'),
                'quantidade' => $quantidade,
                'subtotal' => round($valorTotal, 2),
            ];
        }

        $this->salvarCarrinho();
        $this->quantidadeInput = 1;
        $this->peso = 0;
        $this->recalcularPendente();
        $this->toastSuccess("{$produto->nome} adicionado!");
    }

    public function removerProduto(int $produtoId): void
    {
        $chave = (string) $produtoId;
        unset($this->carrinho[$chave]);
        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    public function atualizarQuantidade(int $produtoId, mixed $quantidade): void
    {
        $chave = (string) $produtoId;
        $quantidade = (float) $quantidade;
        if (!isset($this->carrinho[$chave])) return;
        if ($quantidade <= 0) {
            $this->removerProduto($produtoId);
            return;
        }
        $this->carrinho[$chave]['quantidade'] = $quantidade;
        $this->carrinho[$chave]['subtotal'] = round($this->carrinho[$chave]['preco'] * $quantidade, 2);
        $this->salvarCarrinho();
        $this->recalcularPendente();
    }

    public function limparCarrinho(): void
    {
        $this->carrinho = [];
        $this->pagamentos = [];
        $this->valorPendente = 0;
        session()->forget('pdv_carrinho');
        $this->toastInfo('Carrinho limpo!');
    }

    private function salvarCarrinho(): void
    {
        session()->put('pdv_carrinho', $this->carrinho);
    }

    public function buscarPorCodigo(): void
    {
        $codigo = trim($this->codigoProduto);
        if (strlen($codigo) < 1) return;
        
        $produto = Produto::where('codigo', $codigo)->first();
        if ($produto) {
            $this->adicionarProduto($produto->id);
            $this->codigoProduto = '';
            $this->dispatch('focar-codigo');
        } else {
            $this->toastWarning("Produto não encontrado: {$codigo}");
        }
    }

    public function adicionarPagamento(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio!');
            return;
        }

        $valor = round((float) $this->valorPagamento, 2);
        if ($valor <= 0) {
            $this->toastWarning('Informe um valor!');
            return;
        }

        if ($this->valorPendente <= 0) {
            $this->toastInfo('Valores inseridos, finalize a venda.');
            return;
        }

        $troco = 0;
        if ($this->formaPagamento === 'dinheiro' && $valor > $this->valorPendente) {
            $troco = round($valor - $this->valorPendente, 2);
            $valorEfetivo = $this->valorPendente;
        } else {
            $valorEfetivo = min($valor, $this->valorPendente);
        }

        $this->pagamentos[] = [
            'forma' => $this->formaPagamento,
            'valor' => round($valorEfetivo, 2),
            'troco' => $troco,
        ];

        $this->recalcularPendente();
        
        if ($this->valorPendente > 0) {
            $this->toastSuccess("Restante: R$ " . number_format($this->valorPendente, 2, ',', '.'));
        } else {
            $this->toastSuccess('Pagamento completo!');
        }
    }

    public function removerPagamento(int $index): void
    {
        if (!isset($this->pagamentos[$index])) return;
        array_splice($this->pagamentos, $index, 1);
        $this->recalcularPendente();
    }

    public function finalizarVenda(): void
    {
        if (empty($this->carrinho)) {
            $this->toastWarning('Carrinho vazio!');
            return;
        }

        $caixa = Caixa::caixaAberto();
        if (!$caixa) {
            $this->toastError('Nenhum caixa aberto!');
            $this->mostrarPagamento = false;
            return;
        }

        DB::beginTransaction();

        try {
            $subtotal = $this->totalCarrinho;

            $pedido = Pedido::create([
                'caixa_id' => $caixa->id,
                'numero_pedido' => Pedido::gerarNumero(),
                'cliente_id' => $this->clienteId ?: null,
                'tipo' => 'balcao',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => 'entregue',
                'pagamentos' => $this->pagamentos,
                'atendente_id' => $this->tenantUserId ?? 1,
            ]);

            foreach ($this->carrinho as $item) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['id'],
                    'produto_nome' => $item['nome'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $totais = collect($this->pagamentos)->groupBy('forma')->map(fn($g) => $g->sum('valor'));

            $caixa->increment('total_vendas', $subtotal);
            $caixa->increment('quantidade_vendas');
            $caixa->increment('total_dinheiro', $totais->get('dinheiro', 0));
            $caixa->increment('total_credito', $totais->get('cartao_credito', 0));
            $caixa->increment('total_debito', $totais->get('cartao_debito', 0));
            $caixa->increment('total_pix', $totais->get('pix', 0));

            DB::commit();

            $this->mostrarPagamento = false;
            $this->limparCarrinho();
            $this->toastSuccess("Pedido #{$pedido->numero_pedido} finalizado!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Erro: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        
        $produtos = Produto::where('ativo', true)
            ->when($this->categoriaSelecionada, fn($q) => $q->where('categoria_id', $this->categoriaSelecionada))
            ->when($this->busca, fn($q) => $q->where('nome', 'like', '%' . $this->busca . '%'))
            ->orderBy('nome')
            ->get();

        $clientes = Cliente::where('ativo', true)->orderBy('nome')->get();

        return view('livewire.tenant.pdv.sorveteria', compact('categorias', 'produtos', 'clientes'));
    }
}