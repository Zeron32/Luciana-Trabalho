<?php
require_once 'config/dados.php';
verificarLogin();

// Buscar livros que estão no banco de dados
$busca = $_GET['busca'] ?? '';
$query = "SELECT * FROM livros";
if (!empty($busca)) {
    $query .= " WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?";
}
$query .= " ORDER BY titulo ASC";

$stmt = $conn->prepare($query);
if (!empty($busca)) {
    $buscaParam = "%{$busca}%";
    $stmt->bind_param("sss", $buscaParam, $buscaParam, $buscaParam);
}
$stmt->execute();
$livros = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros - Sistema Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/menu.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-book"></i> Gerenciar Livros</h1>
            <a href="livro_cadastrar.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Cadastrar Livro
            </a>
        </div>

        <!-- Barra de Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="busca" placeholder="Buscar por título, autor ou ISBN..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <?php if ($busca): ?>
                            <a href="livros.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Livros -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>ISBN</th>
                                <th>Editora</th>
                                <th>Ano</th>
                                <th>Disponível</th>
                                <th>Localização</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($livros->num_rows > 0): ?>
                                <?php while ($livro = $livros->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $livro['id']; ?></td>
                                        <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                                        <td><?php echo htmlspecialchars($livro['isbn']); ?></td>
                                        <td><?php echo htmlspecialchars($livro['editora']); ?></td>
                                        <td><?php echo $livro['ano_publicacao']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $livro['quantidade_disponivel'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $livro['quantidade_disponivel']; ?>/<?php echo $livro['quantidade']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($livro['localizacao']); ?></td>
                                        <td>
                                            <a href="livro_editar.php?id=<?php echo $livro['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="livro_excluir.php?id=<?php echo $livro['id']; ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Deseja realmente excluir este livro?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        <i class="bi bi-inbox"></i> Nenhum livro encontrado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
