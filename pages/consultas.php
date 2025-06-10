<?php
require_once '../config/database.php';

$consultas = [
    '1' => [
        'titulo' => 'Vendas por Cliente em Período',
        'sql' => "SELECT c.nome, v.data_venda, v.total FROM vendas v JOIN clientes c ON v.cliente_id = c.id WHERE v.data_venda BETWEEN '2024-12-01' AND '2024-12-31' ORDER BY v.data_venda",
        'descricao' => 'Lista vendas de dezembro de 2024'
    ],
    '2' => [
        'titulo' => 'Total Vendido por Mês',
        'sql' => "SELECT YEAR(data_venda) as ano, MONTH(data_venda) as mes, SUM(total) as total_vendido FROM vendas GROUP BY YEAR(data_venda), MONTH(data_venda) ORDER BY ano, mes",
        'descricao' => 'Agrupamento de vendas por mês'
    ],
    '3' => [
        'titulo' => 'Vendas com Dados Completos (JOIN)',
        'sql' => "SELECT v.id, c.nome as cliente, p.nome as produto, iv.quantidade, iv.preco_unitario, v.data_venda FROM vendas v JOIN clientes c ON v.cliente_id = c.id JOIN itens_venda iv ON v.id = iv.venda_id JOIN produtos p ON iv.produto_id = p.id ORDER BY v.data_venda DESC",
        'descricao' => 'JOIN entre vendas, clientes e produtos'
    ],
    '4' => [
        'titulo' => 'Produtos Mais Vendidos',
        'sql' => "SELECT p.nome, SUM(iv.quantidade) as total_vendido FROM produtos p JOIN itens_venda iv ON p.id = iv.produto_id GROUP BY p.id, p.nome ORDER BY total_vendido DESC",
        'descricao' => 'Agrupamento por produto vendido'
    ],
    '5' => [
        'titulo' => 'Clientes com Mais Compras',
        'sql' => "SELECT c.nome, COUNT(v.id) as total_compras, SUM(v.total) as valor_total FROM clientes c JOIN vendas v ON c.id = v.cliente_id GROUP BY c.id, c.nome ORDER BY valor_total DESC",
        'descricao' => 'Agrupamento de compras por cliente'
    ],
    '6' => [
        'titulo' => 'Produtos por Categoria com Estoque',
        'sql' => "SELECT cat.nome as categoria, p.nome as produto, p.estoque, p.preco FROM produtos p JOIN categorias cat ON p.categoria_id = cat.id WHERE p.estoque > 0 ORDER BY cat.nome, p.nome",
        'descricao' => 'JOIN produtos e categorias com filtro de estoque'
    ]
];

$resultados = [];
if (isset($_GET['consulta']) && isset($consultas[$_GET['consulta']])) {
    $consulta_selecionada = $consultas[$_GET['consulta']];
    $stmt = $pdo->query($consulta_selecionada['sql']);
    $resultados = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Consultas SQL Avançadas</h1>
        <nav>
            <a href="../index.php">Início</a>
            <a href="clientes.php">Clientes</a>
            <a href="produtos.php">Produtos</a>
            <a href="vendas.php">Vendas</a>
            <a href="consultas.php">Consultas</a>
            <a href="../reports/vendas_cliente.php">Relatório Vendas</a>
            <a href="../reports/estoque_baixo.php">Relatório Estoque</a>
        </nav>

        <h2>Consultas Disponíveis</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0;">
            <?php foreach ($consultas as $key => $consulta): ?>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4><?= $consulta['titulo'] ?></h4>
                    <p><?= $consulta['descricao'] ?></p>
                    <a href="?consulta=<?= $key ?>"><button>Executar</button></a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($consulta_selecionada)): ?>
            <h2>Resultado: <?= $consulta_selecionada['titulo'] ?></h2>
            <div style="background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 4px;">
                <strong>SQL:</strong> <code><?= $consulta_selecionada['sql'] ?></code>
            </div>
            
            <?php if (!empty($resultados)): ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($resultados[0]) as $coluna): ?>
                                <?php if (!is_numeric($coluna)): ?>
                                    <th><?= ucfirst(str_replace('_', ' ', $coluna)) ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $linha): ?>
                            <tr>
                                <?php foreach ($linha as $key => $valor): ?>
                                    <?php if (!is_numeric($key)): ?>
                                        <td>
                                            <?php if (strpos($key, 'data') !== false): ?>
                                                <?= date('d/m/Y', strtotime($valor)) ?>
                                            <?php elseif (strpos($key, 'total') !== false || strpos($key, 'preco') !== false || strpos($key, 'valor') !== false): ?>
                                                R$ <?= number_format($valor, 2, ',', '.') ?>
                                            <?php else: ?>
                                                <?= $valor ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum resultado encontrado.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>