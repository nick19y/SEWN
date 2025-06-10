<?php
require_once '../config/database.php';

if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['endereco']]);
            $message = "Cliente adicionado com sucesso!";
        } elseif ($_POST['action'] === 'update') {
            $stmt = $pdo->prepare("UPDATE clientes SET nome=?, email=?, telefone=?, endereco=? WHERE id=?");
            $stmt->execute([$_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['endereco'], $_POST['id']]);
            $message = "Cliente atualizado com sucesso!";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $message = "Cliente excluído com sucesso!";
        }
    }
}

$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome")->fetchAll();
$editCliente = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editCliente = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - SEWN</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Gerenciamento de Clientes</h1>
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

        <h2><?= $editCliente ? 'Editar Cliente' : 'Novo Cliente' ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editCliente ? 'update' : 'add' ?>">
            <?php if ($editCliente): ?>
                <input type="hidden" name="id" value="<?= $editCliente['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <input type="text" name="nome" placeholder="Nome" value="<?= $editCliente['nome'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" value="<?= $editCliente['email'] ?? '' ?>">
            </div>
            <div class="form-group">
                <input type="text" name="telefone" placeholder="Telefone" value="<?= $editCliente['telefone'] ?? '' ?>">
            </div>
            <div class="form-group">
                <textarea name="endereco" placeholder="Endereço"><?= $editCliente['endereco'] ?? '' ?></textarea>
            </div>
            <button type="submit"><?= $editCliente ? 'Atualizar' : 'Adicionar' ?></button>
            <?php if ($editCliente): ?>
                <a href="clientes.php"><button type="button">Cancelar</button></a>
            <?php endif; ?>
        </form>

        <h2>Lista de Clientes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente['id'] ?></td>
                    <td><?= $cliente['nome'] ?></td>
                    <td><?= $cliente['email'] ?></td>
                    <td><?= $cliente['telefone'] ?></td>
                    <td><?= $cliente['endereco'] ?></td>
                    <td>
                        <a href="?edit=<?= $cliente['id'] ?>"><button>Editar</button></a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
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