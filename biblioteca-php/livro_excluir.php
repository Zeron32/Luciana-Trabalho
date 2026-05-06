<?php
require_once 'config/dados.php';
verificarLogin();

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    // Verificar se tem livros que estão emprestados
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM emprestimos WHERE livro_id = ? AND status = 'ativo'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $emprestimosAtivos = $result->fetch_assoc()['total'];
    $stmt->close();

    if ($emprestimosAtivos > 0) {
        $_SESSION['mensagem_erro'] = 'Não é possível excluir este livro pois existem empréstimos ativos!';
        header('Location: livros.php');
        exit();
    }

    // Excluir livro do banco de dados
    $stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = 'Livro excluído com sucesso!';
    } else {
        $_SESSION['mensagem_erro'] = 'Erro ao excluir livro!';
    }
    $stmt->close();
}

header('Location: livros.php');
exit();
?>
