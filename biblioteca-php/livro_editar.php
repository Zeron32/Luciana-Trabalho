<?php
require_once 'config/dados.php';
verificarLogin();

$id = $_GET['id'] ?? 0;
$sucesso = '';
$erro = '';

// Buscar livro no banco de dados
$stmt = $conn->prepare("SELECT * FROM livros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: livros.php');
    exit();
}

$livro = $result->fetch_assoc();
$stmt->close();

// Atualizar situação do livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $editora = $_POST['editora'] ?? '';
    $ano_publicacao = $_POST['ano_publicacao'] ?? '';
    $quantidade = $_POST['quantidade'] ?? 1;
    $categoria = $_POST['categoria'] ?? '';
    $localizacao = $_POST['localizacao'] ?? '';

    if (!empty($titulo) && !empty($autor)) {
        // Calcular quantidade dos itens que estão disponíveveis
        $diferenca = $quantidade - $livro['quantidade'];
        $nova_disponivel = $livro['quantidade_disponivel'] + $diferenca;

        $stmt = $conn->prepare("UPDATE livros SET titulo = ?, autor = ?, isbn = ?, editora = ?, ano_publicacao = ?, quantidade = ?, quantidade_disponivel = ?, categoria = ?, localizacao = ? WHERE id = ?");
        $stmt->bind_param("ssssiiissi", $titulo, $autor, $isbn, $editora, $ano_publicacao, $quantidade, $nova_disponivel, $categoria, $localizacao, $id);

        if ($stmt->execute()) {
            $sucesso = 'Livro atualizado com sucesso!';
            header('refresh:2;url=livros.php');
        } else {
            $erro = 'Erro ao atualizar livro: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $erro = 'Preencha os campos obrigatórios!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Livro</h4>
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
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($livro['titulo']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="isbn" class="form-label">ISBN</label>
                                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($livro['isbn']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="autor" class="form-label">Autor *</label>
                                    <input type="text" class="form-control" id="autor" name="autor" value="<?php echo htmlspecialchars($livro['autor']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editora" class="form-label">Editora</label>
                                    <input type="text" class="form-control" id="editora" name="editora" value="<?php echo htmlspecialchars($livro['editora']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                                    <input type="number" class="form-control" id="ano_publicacao" name="ano_publicacao" value="<?php echo $livro['ano_publicacao']; ?>" min="1000" max="2100">
                                </div>
                                <div class="col-md-4">
                                    <label for="quantidade" class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo $livro['quantidade']; ?>" min="1">
                                    <small class="text-muted">Disponível: <?php echo $livro['quantidade_disponivel']; ?></small>
                                </div>
                                <div class="col-md-4">
                                    <label for="localizacao" class="form-label">Localização</label>
                                    <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?php echo htmlspecialchars($livro['localizacao']); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Selecione...</option>
                                    <option value="Literatura Brasileira" <?php echo $livro['categoria'] === 'Literatura Brasileira' ? 'selected' : ''; ?>>Literatura Brasileira</option>
                                    <option value="Literatura Estrangeira" <?php echo $livro['categoria'] === 'Literatura Estrangeira' ? 'selected' : ''; ?>>Literatura Estrangeira</option>
                                    <option value="Ficção Científica" <?php echo $livro['categoria'] === 'Ficção Científica' ? 'selected' : ''; ?>>Ficção Científica</option>
                                    <option value="Fantasia" <?php echo $livro['categoria'] === 'Fantasia' ? 'selected' : ''; ?>>Fantasia</option>
                                    <option value="Romance" <?php echo $livro['categoria'] === 'Romance' ? 'selected' : ''; ?>>Romance</option>
                                    <option value="Tecnologia" <?php echo $livro['categoria'] === 'Tecnologia' ? 'selected' : ''; ?>>Tecnologia</option>
                                    <option value="História" <?php echo $livro['categoria'] === 'História' ? 'selected' : ''; ?>>História</option>
                                    <option value="Filosofia" <?php echo $livro['categoria'] === 'Filosofia' ? 'selected' : ''; ?>>Filosofia</option>
                                    <option value="Infantil" <?php echo $livro['categoria'] === 'Infantil' ? 'selected' : ''; ?>>Infantil</option>
                                    <option value="Juvenil" <?php echo $livro['categoria'] === 'Juvenil' ? 'selected' : ''; ?>>Juvenil</option>
                                    <option value="Educação" <?php echo $livro['categoria'] === 'Educação' ? 'selected' : ''; ?>>Educação</option>
                                    <option value="Outros" <?php echo $livro['categoria'] === 'Outros' ? 'selected' : ''; ?>>Outros</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Salvar Alterações
                                </button>
                                <a href="livros.php" class="btn btn-secondary">
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
