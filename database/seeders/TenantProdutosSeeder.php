<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantProdutosSeeder extends Seeder
{
    public function run(): void
    {
        // Pega todos os tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->warn('Nenhum tenant encontrado!');
            return;
        }
        
        foreach ($tenants as $tenant) {
            $this->command->info("\n==========================================");
            $this->command->info("Populando produtos no tenant: {$tenant->name} ({$tenant->id})");
            $this->command->info("==========================================");
            
            // Executa dentro do contexto do tenant
            $tenant->run(function () use ($tenant) {
                $this->popularProdutos($tenant);
            });
        }
        
        $this->command->info("\n🎉 Todos os tenants foram populados com produtos!");
    }
    
    private function popularProdutos($tenant)
    {
        // Verificar se já tem produtos
        if (Produto::count() > 0) {
            $this->command->warn("→ Tenant {$tenant->id} já possui " . Produto::count() . " produtos. Pulando...");
            return;
        }
        
        // ==================== CATEGORIAS ====================
        $categorias = [
            ['nome' => 'Sorvetes', 'slug' => 'sorvetes', 'icone' => 'ice-cream', 'cor' => '#FF6B6B', 'ordem' => 1],
            ['nome' => 'Casquinhas', 'slug' => 'casquinhas', 'icone' => 'cone', 'cor' => '#FFB347', 'ordem' => 2],
            ['nome' => 'Milkshakes', 'slug' => 'milkshakes', 'icone' => 'cup', 'cor' => '#FF6B9D', 'ordem' => 3],
            ['nome' => 'Açaí', 'slug' => 'acai', 'icone' => 'bowl', 'cor' => '#9B59B6', 'ordem' => 4],
            ['nome' => 'Picolés', 'slug' => 'picoles', 'icone' => 'ice-lolly', 'cor' => '#3498DB', 'ordem' => 5],
            ['nome' => 'Sundae', 'slug' => 'sundae', 'icone' => 'cup', 'cor' => '#E74C3C', 'ordem' => 6],
            ['nome' => 'Bebidas', 'slug' => 'bebidas', 'icone' => 'coffee', 'cor' => '#2ECC71', 'ordem' => 7],
            ['nome' => 'Acompanhamentos', 'slug' => 'acompanhamentos', 'icone' => 'sparkles', 'cor' => '#F39C12', 'ordem' => 8],
        ];

        $categoriasCriadas = [];
        foreach ($categorias as $categoria) {
            $cat = Categoria::create($categoria);
            $categoriasCriadas[$categoria['slug']] = $cat;
            $this->command->line("  → Categoria criada: {$categoria['nome']}");
        }

        // ==================== PRODUTOS ====================
        
        // Sorvetes
        $produtos = [
            ['categoria' => 'sorvetes', 'nome' => 'Chocolate Belga', 'preco' => 12.90, 'descricao' => 'Sorvete de chocolate belga 100g'],
            ['categoria' => 'sorvetes', 'nome' => 'Morango com Pedaços', 'preco' => 11.90, 'descricao' => 'Sorvete de morango com pedaços da fruta'],
            ['categoria' => 'sorvetes', 'nome' => 'Creme', 'preco' => 10.90, 'descricao' => 'Sorvete de creme tradicional'],
            ['categoria' => 'sorvetes', 'nome' => 'Flocos', 'preco' => 12.90, 'descricao' => 'Sorvete de flocos com chocolate'],
            ['categoria' => 'sorvetes', 'nome' => 'Doce de Leite', 'preco' => 12.90, 'descricao' => 'Sorvete de doce de leite argentino'],
            ['categoria' => 'sorvetes', 'nome' => 'Pistache', 'preco' => 14.90, 'descricao' => 'Sorvete de pistache importado'],
            ['categoria' => 'sorvetes', 'nome' => 'Café', 'preco' => 11.90, 'descricao' => 'Sorvete de café expresso'],
            ['categoria' => 'sorvetes', 'nome' => 'Coco Queimado', 'preco' => 13.90, 'descricao' => 'Sorvete de coco queimado'],
            
            // Casquinhas
            ['categoria' => 'casquinhas', 'nome' => 'Casquinha Simples', 'preco' => 5.90, 'descricao' => 'Casquinha com uma bola de sorvete'],
            ['categoria' => 'casquinhas', 'nome' => 'Casquinha Dupla', 'preco' => 8.90, 'descricao' => 'Casquinha com duas bolas de sorvete'],
            ['categoria' => 'casquinhas', 'nome' => 'Casquinha Especial', 'preco' => 12.90, 'descricao' => 'Casquinha com duas bolas + cobertura'],
            
            // Milkshakes
            ['categoria' => 'milkshakes', 'nome' => 'Milkshake Chocolate', 'preco' => 15.90, 'descricao' => 'Milkshake de chocolate 500ml'],
            ['categoria' => 'milkshakes', 'nome' => 'Milkshake Morango', 'preco' => 15.90, 'descricao' => 'Milkshake de morango 500ml'],
            ['categoria' => 'milkshakes', 'nome' => 'Milkshake Ovomaltine', 'preco' => 17.90, 'descricao' => 'Milkshake de ovomaltine 500ml'],
            ['categoria' => 'milkshakes', 'nome' => 'Milkshake Nutella', 'preco' => 22.90, 'descricao' => 'Milkshake de nutella 500ml'],
            
            // Açaí
            ['categoria' => 'acai', 'nome' => 'Açaí 300ml', 'preco' => 12.90, 'descricao' => 'Açaí tradicional 300ml'],
            ['categoria' => 'acai', 'nome' => 'Açaí 500ml', 'preco' => 18.90, 'descricao' => 'Açaí tradicional 500ml'],
            ['categoria' => 'acai', 'nome' => 'Açaí 700ml', 'preco' => 24.90, 'descricao' => 'Açaí tradicional 700ml'],
            ['categoria' => 'acai', 'nome' => 'Açaí Bowl', 'preco' => 28.90, 'descricao' => 'Açaí na tigela com frutas e granola'],
            
            // Picolés
            ['categoria' => 'picoles', 'nome' => 'Picolé de Chocolate', 'preco' => 4.90, 'descricao' => 'Picolé de chocolate ao leite'],
            ['categoria' => 'picoles', 'nome' => 'Picolé de Morango', 'preco' => 4.90, 'descricao' => 'Picolé de morango'],
            ['categoria' => 'picoles', 'nome' => 'Picolé de Limão', 'preco' => 4.50, 'descricao' => 'Picolé de limão'],
            ['categoria' => 'picoles', 'nome' => 'Picolé Especial', 'preco' => 6.90, 'descricao' => 'Picolé com cobertura especial'],
            
            // Sundae
            ['categoria' => 'sundae', 'nome' => 'Sundae Chocolate', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de chocolate'],
            ['categoria' => 'sundae', 'nome' => 'Sundae Morango', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de morango'],
            ['categoria' => 'sundae', 'nome' => 'Sundae Caramelo', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de caramelo'],
            ['categoria' => 'sundae', 'nome' => 'Banana Split', 'preco' => 22.90, 'descricao' => 'Banana split especial'],
            
            // Bebidas
            ['categoria' => 'bebidas', 'nome' => 'Água Mineral', 'preco' => 3.00, 'descricao' => 'Água mineral 500ml'],
            ['categoria' => 'bebidas', 'nome' => 'Refrigerante Lata', 'preco' => 5.00, 'descricao' => 'Refrigerante lata 350ml'],
            ['categoria' => 'bebidas', 'nome' => 'Suco Natural', 'preco' => 7.00, 'descricao' => 'Suco natural 300ml'],
            ['categoria' => 'bebidas', 'nome' => 'Água de Coco', 'preco' => 6.00, 'descricao' => 'Água de coco 500ml'],
            
            // Acompanhamentos
            ['categoria' => 'acompanhamentos', 'nome' => 'Granola', 'preco' => 2.00, 'descricao' => 'Porção extra de granola'],
            ['categoria' => 'acompanhamentos', 'nome' => 'Leite em Pó', 'preco' => 2.00, 'descricao' => 'Leite em pó'],
            ['categoria' => 'acompanhamentos', 'nome' => 'Chocolate Granulado', 'preco' => 2.00, 'descricao' => 'Chocolate granulado'],
            ['categoria' => 'acompanhamentos', 'nome' => 'Calda de Chocolate', 'preco' => 3.00, 'descricao' => 'Calda de chocolate extra'],
            ['categoria' => 'acompanhamentos', 'nome' => 'Calda de Morango', 'preco' => 3.00, 'descricao' => 'Calda de morango extra'],
            ['categoria' => 'acompanhamentos', 'nome' => 'Cobertura de Caramelo', 'preco' => 3.00, 'descricao' => 'Cobertura de caramelo'],
        ];

        foreach ($produtos as $produto) {
            $categoria = $categoriasCriadas[$produto['categoria']];
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => Str::slug($produto['nome']),
                'categoria_id' => $categoria->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        $this->command->line("  → Produtos criados: " . Produto::count());
        $this->command->line("  → Categorias criadas: " . Categoria::count());
    }
}