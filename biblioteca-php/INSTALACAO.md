# 📚 Guia de Instalação - Sistema de Biblioteca

## 🎯 Passo a Passo Completo

### 1️⃣ Instalar o XAMPP

1. **Download:**
   - Acesse: https://www.apachefriends.org/
   - Baixe a versão mais recente para Windows
   - Execute o instalador

2. **Instalação:**
   - Escolha o local de instalação (padrão: `C:\xampp`)
   - Selecione os componentes:
     - ✅ Apache
     - ✅ MySQL
     - ✅ PHP
     - ✅ phpMyAdmin
   - Conclua a instalação

---

### 2️⃣ Copiar os Arquivos do Projeto

1. **Localize a pasta:**
   ```
   C:\xampp\htdocs\
   ```

2. **Copie a pasta `biblioteca-php` para dentro de `htdocs`:**
   ```
   C:\xampp\htdocs\biblioteca\
   ```

3. **Estrutura final:**
   ```
   C:\xampp\htdocs\biblioteca\
   ├── config/
   │   └── dados.php
   ├── includes/
   │   └── menu.php
   ├── database.sql
   ├── index.php
   ├── dashboard.php
   ├── livros.php
   ├── (demais arquivos...)
   └── README.md
   ```

---

### 3️⃣ Iniciar os Serviços do XAMPP

1. **Abra o XAMPP Control Panel**
   - Procure por "XAMPP Control Panel" no menu iniciar

2. **Inicie os serviços:**
   - Clique em **"Start"** ao lado de **Apache**
   - Clique em **"Start"** ao lado de **MySQL**

3. **Verificar:**
   - Os botões devem ficar verdes
   - Se aparecer erro de porta, veja a seção "Solução de Problemas" abaixo

---

### 4️⃣ Criar o Banco de Dados

#### Opção 1: Via phpMyAdmin (Recomendado)

1. **Acesse o phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Criar banco de dados:**
   - Clique em **"Novo"** no menu lateral esquerdo
   - Nome do banco: `biblioteca_db`
   - Cotejamento: `utf8mb4_unicode_ci`
   - Clique em **"Criar"**

3. **Importar as tabelas:**
   - Clique no banco `biblioteca_db` no menu lateral
   - Clique na aba **"SQL"** no topo
   - Clique em **"Escolher arquivo"**
   - Selecione o arquivo `database.sql` da pasta do projeto
   - Clique em **"Executar"**

4. **Verificar:**
   - No menu lateral, você deve ver as tabelas:
     - ✅ livros
     - ✅ usuarios
     - ✅ emprestimos

#### Opção 2: Via Linha de Comando

```bash
# Abra o CMD e navegue até:
cd C:\xampp\mysql\bin

# Execute:
mysql -u root -p
# (pressione Enter quando pedir senha)

# Dentro do MySQL, execute:
CREATE DATABASE biblioteca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_db;
SOURCE C:/xampp/htdocs/biblioteca/database.sql;
exit;
```

---

### 5️⃣ Acessar o Sistema

1. **Abra o navegador** (Chrome, Firefox, Edge, etc.)

2. **Digite o endereço:**
   ```
   http://localhost/biblioteca
   ```

3. **Fazer login:**
   - **Email:** `admin@biblioteca.com`
   - **Senha:** `admin123`

---

## 🔧 Solução de Problemas

### ❌ Erro: Apache não inicia (Porta 80 em uso)

**Causa:** Outro programa está usando a porta 80 (geralmente Skype ou IIS)

**Solução 1 - Mudar a porta do Apache:**
1. No XAMPP Control Panel, clique em **"Config"** ao lado do Apache
2. Selecione **"httpd.conf"**
3. Procure por `Listen 80` e mude para `Listen 8080`
4. Procure por `ServerName localhost:80` e mude para `ServerName localhost:8080`
5. Salve e reinicie o Apache
6. Acesse: `http://localhost:8080/biblioteca`

**Solução 2 - Desabilitar o Skype:**
1. Abra o Skype
2. Ferramentas → Opções → Avançado → Conexão
3. Desmarque "Usar portas 80 e 443"

---

### ❌ Erro: MySQL não inicia (Porta 3306 em uso)

**Solução:**
1. Abra o Gerenciador de Tarefas (Ctrl + Shift + Esc)
2. Procure por processos MySQL
3. Finalize os processos
4. Reinicie o MySQL no XAMPP

---

### ❌ Erro: "Access denied for user 'root'@'localhost'"

**Solução:**
1. Abra o arquivo `C:\xampp\htdocs\biblioteca\config\dados.php`
2. Verifique as configurações:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Deixe vazio
   define('DB_NAME', 'biblioteca_db');
   ```

---

### ❌ Erro: Página em branco

**Solução:**
1. Ative a exibição de erros no PHP:
   - Abra `C:\xampp\php\php.ini`
   - Procure por `display_errors = Off`
   - Mude para `display_errors = On`
   - Reinicie o Apache

---

### ❌ Erro: "Call to undefined function mysqli_connect()"

**Solução:**
1. Abra `C:\xampp\php\php.ini`
2. Procure por `;extension=mysqli`
3. Remova o `;` para ficar: `extension=mysqli`
4. Salve e reinicie o Apache

---

## 🎓 Dados de Teste Incluídos

### 👤 Usuários:
- **Administrador:**
  - Email: `admin@biblioteca.com`
  - Senha: `admin123`

- **Leitor:**
  - Email: `joao.silva@email.com`
  - Senha: `123456`

### 📚 Livros:
- 8 livros cadastrados com diferentes categorias
- Exemplos: Dom Casmurro, 1984, O Senhor dos Anéis, Clean Code, etc.

### 📊 Empréstimos:
- 4 empréstimos de exemplo (alguns ativos, alguns atrasados)

---

## 🔐 Segurança

### Recomendações para Produção:

1. **Alterar a senha do administrador:**
   - Faça login
   - Acesse o banco de dados via phpMyAdmin
   - Tabela `usuarios` → Edite o registro do admin
   - Use um gerador de MD5: `UPDATE usuarios SET senha = MD5('nova_senha_segura') WHERE id = 1;`

2. **Configurar senha do MySQL:**
   ```bash
   # No CMD:
   cd C:\xampp\mysql\bin
   mysqladmin -u root password "sua_senha_aqui"
   ```
   
   Depois, atualize `config/dados.php`:
   ```php
   define('DB_PASS', 'sua_senha_aqui');
   ```

3. **Desabilitar acesso externo:**
   - O XAMPP só deve ser acessível localmente
   - Não exponha para a internet sem configurações adequadas

---

## 📞 Suporte

Em caso de dúvidas:
1. Verifique os logs de erro do Apache: `C:\xampp\apache\logs\error.log`
2. Verifique os logs do MySQL: `C:\xampp\mysql\data\mysql_error.log`
3. Consulte a documentação do XAMPP: https://www.apachefriends.org/

---

## ✅ Checklist de Instalação

- [ ] XAMPP instalado
- [ ] Apache iniciado (luz verde)
- [ ] MySQL iniciado (luz verde)
- [ ] Pasta copiada para `C:\xampp\htdocs\biblioteca`
- [ ] Banco de dados `biblioteca_db` criado
- [ ] Arquivo `database.sql` importado
- [ ] Sistema acessível em `http://localhost/biblioteca`
- [ ] Login funcionando com admin@biblioteca.com

---

**Pronto! Seu sistema está funcionando! 🎉**
