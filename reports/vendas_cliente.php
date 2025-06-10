<?php
require_once '../config/database.php';
use Dompdf\Dompdf;

if (isset($_GET['pdf'])) {
    $vendas = $pdo->query("
        SELECT c.nome as cliente, COUNT(v.id) as total_vendas, SUM(v.total) as valor_total,
               MIN(v.data_venda) as primeira_compra, MAX(v.data_venda) as ultima_compra
        FROM clientes c 
        LEFT JOIN vendas v ON c.id = v.cliente_id 
        GROUP BY c.id, c.nome 
        ORDER BY valor_total DESC
    ")->fetchAll();
    
    $linha = "";
    while($registro = array_shift($vendas)) {
        $linha .= "<tr><td>".$registro['cliente']."</td><td>".($registro['total_vendas'] ?: '0')."</td><td>";
        $linha .= "R$ ".number_format($registro['valor_total'] ?: 0, 2, ',', '.')."</td><td>";
        $linha .= ($registro['primeira_compra'] ? date("d/m/Y", strtotime($registro['primeira_compra'])) : '-')."</td><td>";
        $linha .= ($registro['ultima_compra'] ? date("d/m/Y", strtotime($registro['ultima_compra'])) : '-')."</td></tr>";
    }
    
    require_once("dompdf/autoload.inc.php");
    
    $dompdf = new DOMPDF();
    
    $dompdf->load_html('
        <h1 style="text-align: center;">SEWN - Relatório de Vendas por Cliente</h1>
        <hr>
        <p><strong>Data de Geração:</strong> '.date('d/m/Y H:i:s').'</p>
        <table width="100%">
            <tr>
                <td><strong>Cliente</strong></td><td><strong>Total Vendas</strong></td><td><strong>Valor Total</strong></td><td><strong>Primeira Compra</strong></td><td><strong>Última Compra</strong></td>
            </tr>'.$linha.'</table>');
    
    $dompdf->setPaper('A4','portrait');
    
    $dompdf->render();
    
    $dompdf->stream(
        "relatorio_vendas_cliente.pdf",
        array(
            "Attachment" => false
        )
    );
    exit;
}

$vendas = $pdo->query("
    SELECT c.nome as cliente, COUNT(v.id) as total_vendas, SUM(v.total) as valor_total,
           MIN(v.data_venda) as primeira_compra, MAX(v.data_venda) as ultima_compra
    FROM clientes c 
    LEFT JOIN vendas v ON c.id = v.cliente_id 
    GROUP BY c.id, c.nome 
    ORDER BY valor_total DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Vendas por Cliente - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Relatório de Vendas por Cliente</h1>
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
            <h2>Relatório de Vendas por Cliente</h2>
            <a href="?pdf=1" target="_blank"><button>Gerar PDF</button></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Total de Vendas</th>
                    <th>Valor Total</th>
                    <th>Primeira Compra</th>
                    <th>Última Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $venda): ?>
                <tr>
                    <td><?= htmlspecialchars($venda['cliente']) ?></td>
                    <td><?= $venda['total_vendas'] ?: '0' ?></td>
                    <td>R$ <?= number_format($venda['valor_total'] ?: 0, 2, ',', '.') ?></td>
                    <td><?= $venda['primeira_compra'] ? date('d/m/Y', strtotime($venda['primeira_compra'])) : '-' ?></td>
                    <td><?= $venda['ultima_compra'] ? date('d/m/Y', strtotime($venda['ultima_compra'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>Resumo</h3>
            <p><strong>Total de Clientes:</strong> <?= count($vendas) ?></p>
            <p><strong>Total Geral de Vendas:</strong> R$ <?= number_format(array_sum(array_column($vendas, 'valor_total')), 2, ',', '.') ?></p>
            <p><strong>Data de Geração:</strong> <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>