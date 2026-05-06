# 👨‍💻 Guia do Desenvolvedor

## 📁 Estrutura do Projeto

```
biblioteca/
│
├── config/
│   └── dados.php              # Configuração de banco e funções utilitárias
│
├── includes/
│   └── menu.php               # Menu de navegação (incluído em todas as páginas)
│
├── database.sql               # Script SQL para criar banco e tabelas
│
├── index.php                  # Página de login
├── logout.php                 # Logout do sistema
├── dashboard.php              # Painel principal com estatísticas
│
├── livros.php                 # Listagem de livros
├── livro_cadastrar.php        # Cadastro de livro
├── livro_editar.php           # Edição de livro
├── livro_excluir.php          # Exclusão de livro
│
├── usuarios.php               # Listagem de usuários
│
├── emprestimos.php            # Listagem de empréstimos
├── emprestimo_cadastrar.php   # Registro de empréstimo
├── devolucao.php              # Registro de devolução
│
├── .htaccess                  # Configurações do Apache
├── README.md                  # Documentação geral
├── INSTALACAO.md              # Guia de instalação
└── DESENVOLVEDOR.md           # Este arquivo
```

---

## 🗄️ Modelo de Dados

### Tabela: `livros`
```sql
- id (INT, PK, AUTO_INCREMENT)
- titulo (VARCHAR 255)
- autor (VARCHAR 255)
- isbn (VARCHAR 20)
- editora (VARCHAR 100)
- ano_publicacao (INT)
- quantidade (INT)
- quantidade_disponivel (INT)
- categoria (VARCHAR 100)
- localizacao (VARCHAR 50)
- data_cadastro (TIMESTAMP)
```

### Tabela: `usuarios`
```sql
- id (INT, PK, AUTO_INCREMENT)
- nome (VARCHAR 255)
- email (VARCHAR 255, UNIQUE)
- telefone (VARCHAR 20)
- cpf (VARCHAR 14, UNIQUE)
- endereco (TEXT)
- tipo (ENUM: 'administrador', 'bibliotecario', 'leitor')
- senha (VARCHAR 255, MD5)
- ativo (BOOLEAN)
- data_cadastro (TIMESTAMP)
```

### Tabela: `emprestimos`
```sql
- id (INT, PK, AUTO_INCREMENT)
- livro_id (INT, FK → livros.id)
- usuario_id (INT, FK → usuarios.id)
- data_emprestimo (DATE)
- data_prevista_devolucao (DATE)
- data_devolucao (DATE, NULL)
- status (ENUM: 'ativo', 'devolvido', 'atrasado')
- observacoes (TEXT)
```

### Relacionamentos:
- `emprestimos.livro_id` → `livros.id` (CASCADE DELETE)
- `emprestimos.usuario_id` → `usuarios.id` (CASCADE DELETE)

---

## 🔐 Sistema de Autenticação

### Login (`index.php`)
```php
// Validação simples usando MD5
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = MD5(?) AND ativo = 1");
```

**⚠️ Importante para Produção:**
- O MD5 **não é seguro** para senhas
- Recomenda-se usar `password_hash()` e `password_verify()`
- Exemplo:
  ```php
  // Ao cadastrar:
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

  // Ao validar:
  if (password_verify($senha_digitada, $senha_hash_banco)) {
      // Login OK
  }
  ```

### Sessões
```php
// Iniciada em dados.php
session_start();

// Armazenadas:
$_SESSION['usuario_id']
$_SESSION['usuario_nome']
$_SESSION['usuario_email']
$_SESSION['usuario_tipo']
```

### Verificação de Login
```php
// Função em dados.php
verificarLogin(); // Redireciona para index.php se não logado
```

---

## 🎨 Framework CSS - Bootstrap 5.3

### CDN Utilizado:
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
```

### Classes Principais Usadas:

#### Layout:
- `container` - Container responsivo
- `row` - Linha do grid
- `col-md-*` - Colunas responsivas

#### Componentes:
- `navbar` - Barra de navegação
- `card` - Cards de conteúdo
- `table table-striped` - Tabelas estilizadas
- `btn btn-primary` - Botões
- `alert alert-success` - Alertas

#### Cores:
- `bg-primary` - Azul
- `bg-success` - Verde
- `bg-warning` - Amarelo
- `bg-danger` - Vermelho
- `bg-dark` - Preto

#### Ícones (Bootstrap Icons):
```html
<i class="bi bi-book"></i>       <!-- Livro -->
<i class="bi bi-people"></i>     <!-- Usuários -->
<i class="bi bi-plus-circle"></i> <!-- Adicionar -->
<i class="bi bi-pencil"></i>     <!-- Editar -->
<i class="bi bi-trash"></i>      <!-- Excluir -->
```

---

## 🔄 Fluxo CRUD de Livros

### CREATE (livro_cadastrar.php)
1. Exibe formulário
2. Recebe POST
3. Valida dados
4. INSERT no banco
5. Redireciona para listagem

### READ (livros.php)
1. SELECT * FROM livros
2. Opcional: filtro de busca (LIKE)
3. Exibe em tabela

### UPDATE (livro_editar.php)
1. Recebe ID via GET
2. SELECT para buscar dados
3. Exibe formulário preenchido
4. Recebe POST
5. UPDATE no banco
6. Ajusta quantidade disponível

### DELETE (livro_excluir.php)
1. Recebe ID via GET
2. Verifica se há empréstimos ativos
3. Se não houver, DELETE
4. Redireciona

---

## 📚 Lógica de Empréstimos

### Registrar Empréstimo
```php
BEGIN TRANSACTION;

// 1. Inserir registro de empréstimo
INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_prevista_devolucao, status)
VALUES (?, ?, ?, ?, 'ativo');

// 2. Decrementar quantidade disponível
UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?;

COMMIT;
```

### Registrar Devolução
```php
BEGIN TRANSACTION;

// 1. Atualizar empréstimo
UPDATE emprestimos SET data_devolucao = CURDATE(), status = 'devolvido' WHERE id = ?;

// 2. Incrementar quantidade disponível
UPDATE livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?;

COMMIT;
```

### Cálculo de Atraso
```php
function calcularDiasAtraso($dataPrevisao) {
    $hoje = new DateTime();
    $previsao = new DateTime($dataPrevisao);
    $diff = $hoje->diff($previsao);

    if ($hoje > $previsao) {
        return $diff->days;
    }
    return 0;
}
```

---

## 🛠️ Funções Utilitárias (`config/dados.php`)

```php
verificarLogin()           // Verifica se usuário está logado
getNomeUsuario()           // Retorna nome do usuário da sessão
isAdmin()                  // Verifica se é administrador
formatarData($data)        // Converte Y-m-d para d/m/Y
calcularDiasAtraso($data)  // Calcula dias de atraso
```

---

## ✨ Melhorias Sugeridas

### 🔒 Segurança:
1. Migrar de MD5 para `password_hash()`
2. Implementar proteção CSRF
3. Validar e sanitizar TODOS os inputs
4. Usar prepared statements em TODAS as queries (já implementado)
5. Implementar rate limiting no login

### 📊 Funcionalidades:
1. Histórico de empréstimos por usuário
2. Relatórios em PDF
3. Sistema de multas por atraso
4. Reserva de livros
5. Pesquisa avançada com filtros
6. Upload de capa do livro
7. Sistema de renovação de empréstimo
8. Notificações por email
9. Dashboard com gráficos (Chart.js)
10. Exportação de dados (Excel, CSV)

### 🎨 Interface:
1. Paginação nas listagens
2. Ordenação de colunas
3. Modo escuro
4. Responsividade mobile aprimorada
5. Loading states
6. Confirmações visuais melhores

### 🗄️ Banco de Dados:
1. Índices para melhorar performance
2. Backup automático
3. Soft delete (ao invés de DELETE físico)
4. Logs de auditoria

---

## 🐛 Debugging

### Habilitar erros do PHP:
```php
// Adicione no topo dos arquivos:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Ver erros do MySQL:
```php
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

// Após queries:
if (!$stmt->execute()) {
    echo "Erro: " . $stmt->error;
}
```

### Logs:
- Apache: `C:\xampp\apache\logs\error.log`
- MySQL: `C:\xampp\mysql\data\mysql_error.log`
- PHP: Configurável em `php.ini`

---

## 📖 Recursos de Aprendizado

### Bootstrap 5.3:
- Documentação: https://getbootstrap.com/docs/5.3/
- Exemplos: https://getbootstrap.com/docs/5.3/examples/

### PHP + MySQL:
- PHP Manual: https://www.php.net/manual/pt_BR/
- MySQLi: https://www.php.net/manual/pt_BR/book.mysqli.php
- W3Schools: https://www.w3schools.com/php/

### Boas Práticas:
- PSR-12: https://www.php-fig.org/psr/psr-12/
- OWASP Top 10: https://owasp.org/www-project-top-ten/

---

## 🤝 Contribuindo

### Padrões de Código:
1. Indentação: 4 espaços
2. Encoding: UTF-8
3. Line endings: LF
4. Sempre use prepared statements
5. Comente código complexo
6. Nomes em português para consistência

### Convenções de Nomes:
- Arquivos: `snake_case.php`
- Variáveis: `$snake_case`
- Funções: `camelCase()`
- Classes: `PascalCase`
- Tabelas: `plural_minusculo`
- Colunas: `snake_case`

---

**Bom desenvolvimento! 🚀**
