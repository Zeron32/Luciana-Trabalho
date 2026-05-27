<?php
require_once 'config/dados.php';
verificarLogin();

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    # Função para encontrar emprestimos
    $stmt = $conn->prepare("
        SELECT e.*, l.titulo, u.nome as usuario_nome
        FROM emprestimos e
        JOIN livros l ON e.livro_id = l.id
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.id = ? AND e.status != 'devolvido'
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: emprestimos.php');
        exit();
    }

    $emprestimo = $result->fetch_assoc();
    $stmt->close();

     # Função para devolver o livro
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data_devolucao = date('Y-m-d');

         # Começar a transação
        $conn->begin_transaction();

        try {
            // Atualizar empréstimo
            $stmt = $conn->prepare("UPDATE emprestimos SET data_devolucao = ?, status = 'devolvido' WHERE id = ?");
            $stmt->bind_param("si", $data_devolucao, $id);
            $stmt->execute();
            $stmt->close();

            // Atualizar quantidade disponível
            $stmt = $conn->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?");
            $stmt->bind_param("i", $emprestimo['livro_id']);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header('Location: emprestimos.php');
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $erro = 'Erro ao registrar devolução: ' . $e->getMessage();
        }
    }

    $diasAtraso = calcularDiasAtraso($emprestimo['data_prevista_devolucao']);
} else {
    header('Location: emprestimos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Devolução - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-check-circle"></i> Registrar Devolução</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5>Informações do Empréstimo</h5>
                            <hr>
                            <p><strong>Livro:</strong> <?php echo htmlspecialchars($emprestimo['titulo']); ?></p>
                            <p><strong>Usuário:</strong> <?php echo htmlspecialchars($emprestimo['usuario_nome']); ?></p>
                            <p><strong>Data Empréstimo:</strong> <?php echo formatarData($emprestimo['data_emprestimo']); ?></p>
                            <p><strong>Devolução Prevista:</strong> <?php echo formatarData($emprestimo['data_prevista_devolucao']); ?></p>
                            <p><strong>Data Devolução:</strong> <?php echo formatarData(date('Y-m-d')); ?></p>

                            <?php if ($diasAtraso > 0): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Atenção!</strong> Este livro está com <strong><?php echo $diasAtraso; ?> dia(s)</strong> de atraso.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i>
                                    Devolução dentro do prazo!
                                </div>
                            <?php endif; ?>
                        </div>

                        <form method="POST" action="">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Confirmar Devolução
                                </button>
                                <a href="emprestimos.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
