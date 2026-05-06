<?php
// Configurações de conexão com o banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Deixe vazio se não tiver senha no XAMPP
define('DB_NAME', 'biblioteca_db');

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit();
    }
}

// Função para obter nome do usuário logado
function getNomeUsuario() {
    return isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
}

// Função para verificar se é administrador
function isAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'administrador';
}

// Função para formatar data para exibição
function formatarData($data) {
    if (empty($data)) return '-';
    $dt = new DateTime($data);
    return $dt->format('d/m/Y');
}

// Função para calcular dias de atraso
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
