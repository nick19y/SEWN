<?php
require_once '../config/database.php';

use Dompdf\Dompdf;

if (isset($_GET['pdf'])) {
    $produtos = $pdo->query("
        SELECT p.id, p.nome, c.nome as categoria, p.estoque, p.preco, p.tamanho, p.cor
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.estoque <= 10 
        ORDER BY p.estoque ASC, p.nome
    ")->fetchAll();
    
    $linha = "";
    while($registro = array_shift($produtos)) {
        $linha .= "<tr><td>".$registro['id']."</td><td>".$registro['nome']."</td><td>";
        $linha .= $registro['categoria']."</td><td><strong>".$registro['estoque']."</strong></td><td>";
        $linha .= "R$ ".number_format($registro['preco'], 2, ',', '.')."</td><td>";
        $linha .= $registro['tamanho']."</td><td>".$registro['cor']."</td></tr>";
    }
    
    require_once("dompdf/autoload.inc.php");
    
    $dompdf = new DOMPDF();
    
    $dompdf->load_html('
        <h1 style="text-align: center;">SEWN - Relatório de Estoque Baixo</h1>
        <hr>
        <p><strong>Data de Geração:</strong> '.date('d/m/Y H:i:s').'</p>
        <p><strong>Critério:</strong> Produtos com estoque ≤ 10 unidades</p>
        <table width="100%">
            <tr>
                <td><strong>ID</strong></td><td><strong>Produto</strong></td><td><strong>Categoria</strong></td><td><strong>Estoque</strong></td><td><strong>Preço</strong></td><td><strong>Tamanho</strong></td><td><strong>Cor</strong></td>
            </tr>'.$linha.'</table>');
    
    $dompdf->setPaper('A4','portrait');
    
    $dompdf->render();
    
    $dompdf->stream(
        "relatorio_estoque_baixo.pdf",
        array(
            "Attachment" => false
        )
    );
    exit;
}

$produtos = $pdo->query("
    SELECT p.id, p.nome, c.nome as categoria, p.estoque, p.preco, p.tamanho, p.cor
    FROM produtos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.estoque <= 10 
    ORDER BY p.estoque ASC, p.nome
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Estoque Baixo - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .estoque-critico { background-color: #ffebee; color: #c62828; }
        .estoque-baixo { background-color: #fff3e0; color: #ef6c00; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SEWN - Relatório de Estoque Baixo</h1>
        <nav>
            <a href="../index.php">Início</a>
            <a href="../pages/clientes.php">Clientes</a>
            <a href="../pages/produtos.php">Produtos</a>
            <a href="../pages/vendas.php">Vendas</a>
            <a href="../pages/consultas.php">Consultas</a>
            <a href="vendas_cliente.php">Relatório Vendas</a>
            <a href="estoque_baixo.php">Relatório Estoque</a>
        </nav>

        <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <h2>Relatório de Estoque Baixo</h2>
            <a href="?pdf=1" target="_blank"><button>Gerar PDF</button></a>
        </div>

        <div class="alert alert-info" style="background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb;">
            <strong>Critério:</strong> Produtos com estoque ≤ 10 unidades<br>
            <strong>Legenda:</strong> Estoque ≤ 5 = Crítico (vermelho) | Estoque 6-10 = Baixo (laranja)
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Tamanho</th>
                    <th>Cor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr class="<?= $produto['estoque'] <= 5 ? 'estoque-critico' : 'estoque-baixo' ?>">
                    <td><?= $produto['id'] ?></td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= htmlspecialchars($produto['categoria']) ?></td>
                    <td><strong><?= $produto['estoque'] ?></strong></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($produto['tamanho']) ?></td>
                    <td><?= htmlspecialchars($produto['cor']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($produtos)): ?>
            <div class="alert alert-success">
                Nenhum produto com estoque baixo encontrado!
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>Resumo</h3>
            <p><strong>Total de produtos com estoque baixo:</strong> <?= count($produtos) ?></p>
            <p><strong>Produtos em estoque crítico (≤ 5):</strong> <?= count(array_filter($produtos, fn($p) => $p['estoque'] <= 5)) ?></p>
            <p><strong>Data de Geração:</strong> <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
