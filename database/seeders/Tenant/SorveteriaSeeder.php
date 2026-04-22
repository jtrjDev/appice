<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Categoria;
use App\Models\Tenant\Produto;
use Illuminate\Database\Seeder;

class SorveteriaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar dados existentes (opcional)
        // Produto::truncate();
        // Categoria::truncate();

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

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        $this->command->info('✓ Categorias criadas com sucesso!');

        // ==================== PRODUTOS ====================
        
        // Sorvetes
        $sorvetes = Categoria::where('slug', 'sorvetes')->first();
        $produtosSorvetes = [
            ['nome' => 'Chocolate Belga', 'preco' => 12.90, 'descricao' => 'Sorvete de chocolate belga 100g'],
            ['nome' => 'Morango com Pedaços', 'preco' => 11.90, 'descricao' => 'Sorvete de morango com pedaços da fruta'],
            ['nome' => 'Creme', 'preco' => 10.90, 'descricao' => 'Sorvete de creme tradicional'],
            ['nome' => 'Flocos', 'preco' => 12.90, 'descricao' => 'Sorvete de flocos com chocolate'],
            ['nome' => 'Doce de Leite', 'preco' => 12.90, 'descricao' => 'Sorvete de doce de leite argentino'],
            ['nome' => 'Pistache', 'preco' => 14.90, 'descricao' => 'Sorvete de pistache importado'],
            ['nome' => 'Café', 'preco' => 11.90, 'descricao' => 'Sorvete de café expresso'],
            ['nome' => 'Coco Queimado', 'preco' => 13.90, 'descricao' => 'Sorvete de coco queimado'],
        ];

        foreach ($produtosSorvetes as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $sorvetes->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Casquinhas
        $casquinhas = Categoria::where('slug', 'casquinhas')->first();
        $produtosCasquinhas = [
            ['nome' => 'Casquinha Simples', 'preco' => 5.90, 'descricao' => 'Casquinha com uma bola de sorvete'],
            ['nome' => 'Casquinha Dupla', 'preco' => 8.90, 'descricao' => 'Casquinha com duas bolas de sorvete'],
            ['nome' => 'Casquinha Especial', 'preco' => 12.90, 'descricao' => 'Casquinha com duas bolas + cobertura'],
        ];

        foreach ($produtosCasquinhas as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $casquinhas->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Milkshakes
        $milkshakes = Categoria::where('slug', 'milkshakes')->first();
        $produtosMilkshakes = [
            ['nome' => 'Milkshake Chocolate', 'preco' => 15.90, 'descricao' => 'Milkshake de chocolate 500ml'],
            ['nome' => 'Milkshake Morango', 'preco' => 15.90, 'descricao' => 'Milkshake de morango 500ml'],
            ['nome' => 'Milkshake Ovomaltine', 'preco' => 17.90, 'descricao' => 'Milkshake de ovomaltine 500ml'],
            ['nome' => 'Milkshake Nutella', 'preco' => 22.90, 'descricao' => 'Milkshake de nutella 500ml'],
        ];

        foreach ($produtosMilkshakes as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $milkshakes->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Açaí
        $acai = Categoria::where('slug', 'acai')->first();
        $produtosAcai = [
            ['nome' => 'Açaí 300ml', 'preco' => 12.90, 'descricao' => 'Açaí tradicional 300ml'],
            ['nome' => 'Açaí 500ml', 'preco' => 18.90, 'descricao' => 'Açaí tradicional 500ml'],
            ['nome' => 'Açaí 700ml', 'preco' => 24.90, 'descricao' => 'Açaí tradicional 700ml'],
            ['nome' => 'Açaí Bowl', 'preco' => 28.90, 'descricao' => 'Açaí na tigela com frutas e granola'],
        ];

        foreach ($produtosAcai as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $acai->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Picolés
        $picoles = Categoria::where('slug', 'picoles')->first();
        $produtosPicoles = [
            ['nome' => 'Picolé de Chocolate', 'preco' => 4.90, 'descricao' => 'Picolé de chocolate ao leite'],
            ['nome' => 'Picolé de Morango', 'preco' => 4.90, 'descricao' => 'Picolé de morango'],
            ['nome' => 'Picolé de Limão', 'preco' => 4.50, 'descricao' => 'Picolé de limão'],
            ['nome' => 'Picolé Especial', 'preco' => 6.90, 'descricao' => 'Picolé com cobertura especial'],
        ];

        foreach ($produtosPicoles as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $picoles->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Sundae
        $sundae = Categoria::where('slug', 'sundae')->first();
        $produtosSundae = [
            ['nome' => 'Sundae Chocolate', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de chocolate'],
            ['nome' => 'Sundae Morango', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de morango'],
            ['nome' => 'Sundae Caramelo', 'preco' => 14.90, 'descricao' => 'Sorvete com calda de caramelo'],
            ['nome' => 'Banana Split', 'preco' => 22.90, 'descricao' => 'Banana split especial'],
        ];

        foreach ($produtosSundae as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $sundae->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Bebidas
        $bebidas = Categoria::where('slug', 'bebidas')->first();
        $produtosBebidas = [
            ['nome' => 'Água Mineral', 'preco' => 3.00, 'descricao' => 'Água mineral 500ml'],
            ['nome' => 'Refrigerante Lata', 'preco' => 5.00, 'descricao' => 'Refrigerante lata 350ml'],
            ['nome' => 'Suco Natural', 'preco' => 7.00, 'descricao' => 'Suco natural 300ml'],
            ['nome' => 'Água de Coco', 'preco' => 6.00, 'descricao' => 'Água de coco 500ml'],
        ];

        foreach ($produtosBebidas as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $bebidas->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        // Acompanhamentos
        $acompanhamentos = Categoria::where('slug', 'acompanhamentos')->first();
        $produtosAcompanhamentos = [
            ['nome' => 'Granola', 'preco' => 2.00, 'descricao' => 'Porção extra de granola'],
            ['nome' => 'Leite em Pó', 'preco' => 2.00, 'descricao' => 'Leite em pó'],
            ['nome' => 'Chocolate Granulado', 'preco' => 2.00, 'descricao' => 'Chocolate granulado'],
            ['nome' => 'Calda de Chocolate', 'preco' => 3.00, 'descricao' => 'Calda de chocolate extra'],
            ['nome' => 'Calda de Morango', 'preco' => 3.00, 'descricao' => 'Calda de morango extra'],
            ['nome' => 'Cobertura de Caramelo', 'preco' => 3.00, 'descricao' => 'Cobertura de caramelo'],
        ];

        foreach ($produtosAcompanhamentos as $produto) {
            Produto::create([
                'nome' => $produto['nome'],
                'slug' => \Str::slug($produto['nome']),
                'categoria_id' => $acompanhamentos->id,
                'preco' => $produto['preco'],
                'descricao' => $produto['descricao'],
                'ativo' => true,
            ]);
        }

        $this->command->info('✓ Produtos criados com sucesso!');
        $this->command->info('Total de produtos: ' . Produto::count());
        $this->command->info('Total de categorias: ' . Categoria::count());
    }
}