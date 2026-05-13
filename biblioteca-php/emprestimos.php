<?php
require_once 'config/dados.php';
verificarLogin();

# Pedindo para o SGBD encontrar os emprestimos
$emprestimos = $conn->query("
    SELECT e.*, l.titulo, l.autor, u.nome as usuario_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN usuarios u ON e.usuario_id = u.id
    ORDER BY e.data_emprestimo DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimos - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-arrow-left-right"></i> Gerenciar Empréstimos</h1>
            <a href="emprestimo_cadastrar.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Empréstimo
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Livro</th>
                                <th>Usuário</th>
                                <th>Data Empréstimo</th>
                                <th>Devolução Prevista</th>
                                <th>Data Devolução</th>
                                <th>Status</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($emp = $emprestimos->fetch_assoc()): ?>
                                <?php
                                $diasAtraso = calcularDiasAtraso($emp['data_prevista_devolucao']);

                                if ($emp['status'] === 'devolvido') {
                                    $statusClass = 'success';
                                    $statusTexto = 'Devolvido';
                                } elseif ($diasAtraso > 0) {
                                    $statusClass = 'danger';
                                    $statusTexto = "Atrasado ({$diasAtraso}d)";
                                } else {
                                    $statusClass = 'warning';
                                    $statusTexto = 'Ativo';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $emp['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($emp['titulo']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($emp['autor']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($emp['usuario_nome']); ?></td>
                                    <td><?php echo formatarData($emp['data_emprestimo']); ?></td>
                                    <td><?php echo formatarData($emp['data_prevista_devolucao']); ?></td>
                                    <td><?php echo formatarData($emp['data_devolucao']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusTexto; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($emp['status'] !== 'devolvido'): ?>
                                            <a href="devolucao.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-success" title="Registrar Devolução">
                                                <i class="bi bi-check-circle"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
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
