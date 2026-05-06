<?php
require_once 'config/dados.php';
verificarLogin();

$sucesso = '';
$erro = '';

// Buscar livros disponíveis
$livrosDisponiveis = $conn->query("SELECT id, titulo, autor, quantidade_disponivel FROM livros WHERE quantidade_disponivel > 0 ORDER BY titulo ASC");

// Buscar usuários leitores
$usuarios = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo = 'leitor' AND ativo = 1 ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livro_id = $_POST['livro_id'] ?? 0;
    $usuario_id = $_POST['usuario_id'] ?? 0;
    $data_emprestimo = $_POST['data_emprestimo'] ?? date('Y-m-d');
    $dias_emprestimo = $_POST['dias_emprestimo'] ?? 14;

    if ($livro_id > 0 && $usuario_id > 0) {
        // Calcular data de devolução
        $data_prevista = date('Y-m-d', strtotime($data_emprestimo . " + {$dias_emprestimo} days"));

        // Iniciar transação
        $conn->begin_transaction();

        try {
            // Inserir empréstimo
            $stmt = $conn->prepare("INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_prevista_devolucao, status) VALUES (?, ?, ?, ?, 'ativo')");
            $stmt->bind_param("iiss", $livro_id, $usuario_id, $data_emprestimo, $data_prevista);
            $stmt->execute();
            $stmt->close();

            // Atualizar quantidade disponível
            $stmt = $conn->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?");
            $stmt->bind_param("i", $livro_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $sucesso = 'Empréstimo registrado com sucesso!';
            header('refresh:2;url=emprestimos.php');
        } catch (Exception $e) {
            $conn->rollback();
            $erro = 'Erro ao registrar empréstimo: ' . $e->getMessage();
        }
    } else {
        $erro = 'Selecione o livro e o usuário!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Empréstimo - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Registrar Novo Empréstimo</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($sucesso): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> <?php echo $sucesso; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($erro): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="livro_id" class="form-label">Livro *</label>
                                <select class="form-select" id="livro_id" name="livro_id" required>
                                    <option value="">Selecione um livro...</option>
                                    <?php while ($livro = $livrosDisponiveis->fetch_assoc()): ?>
                                        <option value="<?php echo $livro['id']; ?>">
                                            <?php echo htmlspecialchars($livro['titulo']); ?> - <?php echo htmlspecialchars($livro['autor']); ?>
                                            (Disponível: <?php echo $livro['quantidade_disponivel']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="usuario_id" class="form-label">Usuário *</label>
                                <select class="form-select" id="usuario_id" name="usuario_id" required>
                                    <option value="">Selecione um usuário...</option>
                                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                        <option value="<?php echo $usuario['id']; ?>">
                                            <?php echo htmlspecialchars($usuario['nome']); ?> (<?php echo htmlspecialchars($usuario['email']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_emprestimo" class="form-label">Data do Empréstimo *</label>
                                    <input type="date" class="form-control" id="data_emprestimo" name="data_emprestimo" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dias_emprestimo" class="form-label">Período (dias) *</label>
                                    <select class="form-select" id="dias_emprestimo" name="dias_emprestimo">
                                        <option value="7">7 dias</option>
                                        <option value="14" selected>14 dias</option>
                                        <option value="21">21 dias</option>
                                        <option value="30">30 dias</option>
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Atenção:</strong> O período padrão de empréstimo é de 14 dias. Certifique-se de que o usuário está ciente da data de devolução.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Registrar Empréstimo
                                </button>
                                <a href="emprestimos.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar
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
