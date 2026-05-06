<?php
require_once 'config/dados.php';
verificarLogin();

// Buscar estatísticas dos livros
$totalLivros = $conn->query("SELECT COUNT(*) as total FROM livros")->fetch_assoc()['total'];
$totalUsuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'leitor'")->fetch_assoc()['total'];
$emprestimosAtivos = $conn->query("SELECT COUNT(*) as total FROM emprestimos WHERE status = 'ativo'")->fetch_assoc()['total'];
$emprestimosAtrasados = $conn->query("SELECT COUNT(*) as total FROM emprestimos WHERE status = 'atrasado' OR (status = 'ativo' AND data_prevista_devolucao < CURDATE())")->fetch_assoc()['total'];

// Empréstimos recentes por usúario
$emprestimosRecentes = $conn->query("
    SELECT e.*, l.titulo, u.nome as usuario_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN usuarios u ON e.usuario_id = u.id
    ORDER BY e.data_emprestimo DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total de Livros</h6>
                                <h2 class="mb-0"><?php echo $totalLivros; ?></h2>
                            </div>
                            <i class="bi bi-book-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Usuários</h6>
                                <h2 class="mb-0"><?php echo $totalUsuarios; ?></h2>
                            </div>
                            <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Empréstimos Ativos</h6>
                                <h2 class="mb-0"><?php echo $emprestimosAtivos; ?></h2>
                            </div>
                            <i class="bi bi-arrow-left-right" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Atrasados</h6>
                                <h2 class="mb-0"><?php echo $emprestimosAtrasados; ?></h2>
                            </div>
                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empréstimos Recentes -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Empréstimos Recentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Usuário</th>
                                <th>Data Empréstimo</th>
                                <th>Devolução Prevista</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($emp = $emprestimosRecentes->fetch_assoc()): ?>
                                <?php
                                $diasAtraso = calcularDiasAtraso($emp['data_prevista_devolucao']);
                                $statusClass = $emp['status'] === 'devolvido' ? 'success' : ($diasAtraso > 0 ? 'danger' : 'warning');
                                $statusTexto = $emp['status'] === 'devolvido' ? 'Devolvido' : ($diasAtraso > 0 ? "Atrasado ({$diasAtraso}d)" : 'Ativo');
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['usuario_nome']); ?></td>
                                    <td><?php echo formatarData($emp['data_emprestimo']); ?></td>
                                    <td><?php echo formatarData($emp['data_prevista_devolucao']); ?></td>
                                    <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusTexto; ?></span></td>
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
