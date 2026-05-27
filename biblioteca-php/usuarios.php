<?php
require_once 'config/dados.php';
verificarLogin();

// Buscar usuários que está no sistema
$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-people"></i> Gerenciar Usuários</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Data Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefone']); ?></td>
                                    <td>
                                        <?php
                                        $tipoClass = [
                                            'administrador' => 'danger',
                                            'bibliotecario' => 'warning',
                                            'leitor' => 'primary'
                                        ];
                                        $classe = $tipoClass[$usuario['tipo']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $classe; ?>">
                                            <?php echo ucfirst($usuario['tipo']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $usuario['ativo'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatarData($usuario['data_cadastro']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
