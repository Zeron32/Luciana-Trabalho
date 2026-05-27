<?php
require_once 'config/dados.php';
verificarLogin();
# Local para cadastrar livros

$sucesso = '';
$erro = '';

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
        $stmt = $conn->prepare("INSERT INTO livros (titulo, autor, isbn, editora, ano_publicacao, quantidade, quantidade_disponivel, categoria, localizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiisss", $titulo, $autor, $isbn, $editora, $ano_publicacao, $quantidade, $quantidade, $categoria, $localizacao);

        if ($stmt->execute()) {
            $sucesso = 'Livro cadastrado com sucesso!';
            header('refresh:2;url=livros.php');
        } else {
            $erro = 'Erro ao cadastrar livro: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $erro = 'Preencha os campos obrigatórios (Título e Autor)!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Livro - Sistema Biblioteca</title>
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
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Cadastrar Novo Livro</h4>
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
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="isbn" class="form-label">ISBN</label>
                                    <input type="text" class="form-control" id="isbn" name="isbn">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="autor" class="form-label">Autor *</label>
                                    <input type="text" class="form-control" id="autor" name="autor" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editora" class="form-label">Editora</label>
                                    <input type="text" class="form-control" id="editora" name="editora">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                                    <input type="number" class="form-control" id="ano_publicacao" name="ano_publicacao" min="1000" max="2100">
                                </div>
                                <div class="col-md-4">
                                    <label for="quantidade" class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="quantidade" name="quantidade" value="1" min="1">
                                </div>
                                <div class="col-md-4">
                                    <label for="localizacao" class="form-label">Localização</label>
                                    <input type="text" class="form-control" id="localizacao" name="localizacao" placeholder="Ex: A-12">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Selecione...</option>
                                    <option value="Literatura Brasileira">Literatura Brasileira</option>
                                    <option value="Literatura Estrangeira">Literatura Estrangeira</option>
                                    <option value="Ficção Científica">Ficção Científica</option>
                                    <option value="Fantasia">Fantasia</option>
                                    <option value="Romance">Romance</option>
                                    <option value="Tecnologia">Tecnologia</option>
                                    <option value="História">História</option>
                                    <option value="Filosofia">Filosofia</option>
                                    <option value="Infantil">Infantil</option>
                                    <option value="Juvenil">Juvenil</option>
                                    <option value="Educação">Educação</option>
                                    <option value="Outros">Outros</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Cadastrar
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
