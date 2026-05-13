<?php
# Configuração para saber se todos os bancos de dados estão conectadis
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'biblioteca_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");

// Isso e para pemitir se o usario está conectados
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para permitir o usuário estar logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit();
    }
}

function getNomeUsuario() {
    return isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
}

function isAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'administrador';
}

function formatarData($data) {
    if (empty($data)) return '-';
    $dt = new DateTime($data);
    return $dt->format('d/m/Y');
}

# Isso e só para saber se algum dos livros vai atrasar
function calcularDiasAtraso($dataPrevisao) {
    $hoje = new DateTime();
    $previsao = new DateTime($dataPrevisao);
    $diff = $hoje->diff($previsao);

    if ($hoje > $previsao) {
        return $diff->days;
    }
    return 0;
}
?>
