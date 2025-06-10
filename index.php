<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEWN - Sistema de Gerenciamento</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>SEWN - Loja de Roupas</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="pages/clientes.php">Clientes</a>
            <a href="pages/produtos.php">Produtos</a>
            <a href="pages/vendas.php">Vendas</a>
            <a href="pages/consultas.php">Consultas</a>
            <a href="reports/vendas_cliente.php">Relatório Vendas</a>
            <a href="reports/estoque_baixo.php">Relatório Estoque</a>
        </nav>
        
        <h2>Sistema de Gerenciamento - SEWN</h2>
        <p>Bem-vindo ao sistema de gerenciamento da loja SEWN. Use o menu acima para navegar pelas funcionalidades:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                <h3>Clientes</h3>
                <p>Gerenciar cadastro de clientes</p>
                <a href="pages/clientes.php"><button>Acessar</button></a>
            </div>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                <h3>Produtos</h3>
                <p>Gerenciar estoque de produtos</p>
                <a href="pages/produtos.php"><button>Acessar</button></a>
            </div>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                <h3>Vendas</h3>
                <p>Registrar e gerenciar vendas</p>
                <a href="pages/vendas.php"><button>Acessar</button></a>
            </div>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                <h3>Consultas</h3>
                <p>Consultas SQL avançadas</p>
                <a href="pages/consultas.php"><button>Acessar</button></a>
            </div>
        </div>
    </div>
</body>
</html>