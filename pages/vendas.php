<?php
require_once '../config/database.php';

if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, data_venda, total) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['cliente_id'], $_POST['data_venda'], $_POST['total']]);
            $venda_id = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$venda_id, $_POST['produto_id'], $_POST['quantidade'], $_POST['preco_unitario']]);
            
            $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
            $stmt->execute([$_POST['quantidade'], $_POST['produto_id']]);
            
            $message = "Venda registrada com sucesso!";
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $pdo->prepare("UPDATE vendas SET cliente_id = ?, data_venda = ?, total = ? WHERE id = ?");
            $stmt->execute([$_POST['cliente_id'], $_POST['data_venda'], $_POST['total'], $_POST['id']]);
            
            $stmt = $pdo->prepare("UPDATE itens_venda SET produto_id = ?, quantidade = ?, preco_unitario = ? WHERE venda_id = ?");
            $stmt->execute([$_POST['produto_id'], $_POST['quantidade'], $_POST['preco_unitario'], $_POST['id']]);
            
            $message = "Venda atualizada com sucesso!";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM itens_venda WHERE venda_id=?");
            $stmt->execute([$_POST['id']]);
            $stmt = $pdo->prepare("DELETE FROM vendas WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $message = "Venda excluída com sucesso!";
        }
    }
}

$venda_edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT v.*, iv.produto_id, iv.quantidade, iv.preco_unitario FROM vendas v LEFT JOIN itens_venda iv ON v.id = iv.venda_id WHERE v.id = ?");
    $stmt->execute([$_GET['edit']]);
    $venda_edit = $stmt->fetch();
}

$vendas = $pdo->query("SELECT v.*, c.nome as cliente_nome FROM vendas v LEFT JOIN clientes c ON v.cliente_id = c.id ORDER BY v.data_venda DESC")->fetchAll();
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome")->fetchAll();
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Gerenciamento de Vendas</h1>
        <nav>
            <a href="../index.php">Início</a>
            <a href="clientes.php">Clientes</a>
            <a href="produtos.php">Produtos</a>
            <a href="vendas.php">Vendas</a>
            <a href="consultas.php">Consultas</a>
            <a href="../reports/vendas_cliente.php">Relatório Vendas</a>
            <a href="../reports/estoque_baixo.php">Relatório Estoque</a>
        </nav>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <h2><?= $venda_edit ? 'Editar Venda' : 'Nova Venda' ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $venda_edit ? 'edit' : 'add' ?>">
            <?php if ($venda_edit): ?>
                <input type="hidden" name="id" value="<?= $venda_edit['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <select name="cliente_id" required>
                    <option value="">Selecione o Cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id'] ?>" <?= $venda_edit && $venda_edit['cliente_id'] == $cliente['id'] ? 'selected' : '' ?>><?= $cliente['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="date" name="data_venda" value="<?= $venda_edit ? $venda_edit['data_venda'] : '' ?>" required>
            </div>
            <div class="form-group">
                <select name="produto_id" required>
                    <option value="">Selecione o Produto</option>
                    <?php foreach ($produtos as $produto): ?>
                        <option value="<?= $produto['id'] ?>" <?= $venda_edit && $venda_edit['produto_id'] == $produto['id'] ? 'selected' : '' ?>><?= $produto['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="quantidade" placeholder="Quantidade" min="1" value="<?= $venda_edit ? $venda_edit['quantidade'] : '' ?>" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="preco_unitario" placeholder="Preço Unitário" value="<?= $venda_edit ? $venda_edit['preco_unitario'] : '' ?>" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="total" placeholder="Total" value="<?= $venda_edit ? $venda_edit['total'] : '' ?>" required>
            </div>
            <button type="submit"><?= $venda_edit ? 'Atualizar Venda' : 'Registrar Venda' ?></button>
            <?php if ($venda_edit): ?>
                <a href="vendas.php"><button type="button">Cancelar</button></a>
            <?php endif; ?>
        </form>

        <h2>Lista de Vendas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $venda): ?>
                <tr>
                    <td><?= $venda['id'] ?></td>
                    <td><?= $venda['cliente_nome'] ?></td>
                    <td><?= date('d/m/Y', strtotime($venda['data_venda'])) ?></td>
                    <td>R$ <?= number_format($venda['total'], 2, ',', '.') ?></td>
                    <td>
                        <a href="vendas.php?edit=<?= $venda['id'] ?>"><button type="button">Editar</button></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $venda['id'] ?>">
                            <button type="submit" class="btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
