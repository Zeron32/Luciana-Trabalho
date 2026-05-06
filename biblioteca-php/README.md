# Sistema de Biblioteca - PHP + MySQL + Bootstrap 5.3

## 📋 Instruções de Instalação

### 1. Pré-requisitos
- XAMPP instalado
- Navegador web

### 2. Instalação

1. **Copie a pasta `biblioteca-php` para:**
   ```
   C:\xampp\htdocs\biblioteca
   ```

2. **Inicie o XAMPP:**
   - Abra o XAMPP Control Panel
   - Inicie o Apache
   - Inicie o MySQL

3. **Crie o banco de dados:**
   - Acesse: http://localhost/phpmyadmin
   - Clique em "Novo" para criar um banco
   - Nome: `biblioteca_db`
   - Clique em "SQL" e execute o script do arquivo `database.sql`

4. **Configure a conexão:**
   - Abra o arquivo `config/dados.php`
   - Verifique se as credenciais estão corretas:
     - Servidor: localhost
     - Usuário: root
     - Senha: (deixe vazio)
     - Banco: biblioteca_db

5. **Acesse o sistema:**
   ```
   http://localhost/biblioteca
   ```

## 🚀 Funcionalidades

- ✅ Dashboard com indicadores
- ✅ CRUD completo de Livros
- ✅ Gerenciamento de Usuários
- ✅ Registro de Empréstimos
- ✅ Registro de Devoluções
- ✅ Interface responsiva com Bootstrap 5.3

## 📁 Estrutura do Projeto

```
biblioteca/
├── config/
│   └── dados.php          (conexão com banco)
├── includes/
│   └── menu.php           (menu de navegação)
├── database.sql           (script do banco)
├── index.php              (página de login)
├── dashboard.php          (painel principal)
├── livros.php             (listagem de livros)
├── livro_cadastrar.php    (cadastrar livro)
├── livro_editar.php       (editar livro)
├── livro_excluir.php      (excluir livro)
├── usuarios.php           (gerenciar usuários)
├── emprestimos.php        (listagem de empréstimos)
├── emprestimo_cadastrar.php (registrar empréstimo)
└── devolucao.php          (registrar devolução)
```

## 🔐 Acesso Padrão

Após importar o banco de dados:
- **Usuário:** admin
- **Senha:** admin123

## 🛠️ Tecnologias Utilizadas

- PHP 8.x
- MySQL/MariaDB
- Bootstrap 5.3
- HTML5
- XAMPP
