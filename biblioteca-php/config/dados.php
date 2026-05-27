<?php
// Configurações de conexão com o banco de dados
define('DB_HOST', '127.0.0.1');  // Use IP ao invés de localhost
define('DB_PORT', 3307);
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'biblioteca_db');

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit();
    }
}

// Saber nome do usuario
function getNomeUsuario() {
    return isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
}

// Saber se é admin
function isAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'administrador';
}

// Função para formatar data 
function formatarData($data) {
    if (empty($data) || $data == '0000-00-00') return '-';
    $dt = new DateTime($data);
    return $dt->format('d/m/Y');
}

// Calcular dias de atraso
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