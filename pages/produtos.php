<?php
require_once '../config/database.php';

if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, categoria_id, preco, estoque, tamanho, cor) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['nome'], $_POST['categoria_id'], $_POST['preco'], $_POST['estoque'], $_POST['tamanho'], $_POST['cor']]);
            $message = "Produto adicionado com sucesso!";
        } elseif ($_POST['action'] === 'update') {
            $stmt = $pdo->prepare("UPDATE produtos SET nome=?, categoria_id=?, preco=?, estoque=?, tamanho=?, cor=? WHERE id=?");
            $stmt->execute([$_POST['nome'], $_POST['categoria_id'], $_POST['preco'], $_POST['estoque'], $_POST['tamanho'], $_POST['cor'], $_POST['id']]);
            $message = "Produto atualizado com sucesso!";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $message = "Produto excluído com sucesso!";
        }
    }
}

$produtos = $pdo->query("SELECT p.*, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.nome")->fetchAll();
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();

$editProduto = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editProduto = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Gerenciamento de Produtos</h1>
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

        <h2><?= $editProduto ? 'Editar Produto' : 'Novo Produto' ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editProduto ? 'update' : 'add' ?>">
            <?php if ($editProduto): ?>
                <input type="hidden" name="id" value="<?= $editProduto['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <input type="text" name="nome" placeholder="Nome do Produto" value="<?= $editProduto['nome'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <select name="categoria_id" required>
                    <option value="">Selecione a Categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" <?= ($editProduto && $editProduto['categoria_id'] == $categoria['id']) ? 'selected' : '' ?>>
                            <?= $categoria['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="preco" placeholder="Preço" value="<?= $editProduto['preco'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <input type="number" name="estoque" placeholder="Quantidade em Estoque" value="<?= $editProduto['estoque'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <input type="text" name="tamanho" placeholder="Tamanho" value="<?= $editProduto['tamanho'] ?? '' ?>">
            </div>
            <div class="form-group">
                <input type="text" name="cor" placeholder="Cor" value="<?= $editProduto['cor'] ?? '' ?>">
            </div>
            <button type="submit"><?= $editProduto ? 'Atualizar' : 'Adicionar' ?></button>
            <?php if ($editProduto): ?>
                <a href="produtos.php"><button type="button">Cancelar</button></a>
            <?php endif; ?>
        </form>

        <h2>Lista de Produtos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Tamanho</th>
                    <th>Cor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $produto['id'] ?></td>
                    <td><?= $produto['nome'] ?></td>
                    <td><?= $produto['categoria'] ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td><?= $produto['estoque'] ?></td>
                    <td><?= $produto['tamanho'] ?></td>
                    <td><?= $produto['cor'] ?></td>
                    <td>
                        <a href="?edit=<?= $produto['id'] ?>"><button>Editar</button></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $produto['id'] ?>">
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