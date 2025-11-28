# 📦 Instruções de Instalação no Servidor de Produção

## 🎯 Caminho de Instalação
```
/ssd/aliancaind/public_html/brindes.alianca.br/
```

## 📋 Passo a Passo de Instalação

### 1️⃣ Upload dos Arquivos

Faça upload de **TODOS** os arquivos e pastas para o caminho acima:

```
✅ Arquivos PHP principais:
   - config.php
   - index.php
   - rh.php
   - rh_login.php
   - rh_logout.php
   - rh_funcionarios.php
   - rh_logs.php
   - dar_baixa.php
   
✅ Banco de dados:
   - brindes.db
   - data_log.csv
   
✅ Pasta inc/:
   - inc/functions.php
   
✅ Pasta templates/:
   - templates/base.php
   - templates/funcionario_home.php
   - templates/qr_code_display.php
   - templates/status.php
   - templates/rh_home.php
   - templates/rh_status.php
   - templates/rh_confirmacao.php
   - templates/rh_funcionarios.php
   - templates/rh_logs.php
   
✅ Pasta imgs/:
   - imgs/logo.png (se tiver)
   
✅ Arquivos de configuração:
   - .htaccess (MUITO IMPORTANTE!)
```

### 2️⃣ Configuração de Permissões

Depois do upload, configure as permissões dos arquivos:

**Via interface do cPanel (Gerenciador de Arquivos):**

1. Clique com botão direito em `brindes.db` → Permissões → Defina como **666** (rw-rw-rw-)
2. Clique com botão direito em `data_log.csv` → Permissões → Defina como **666** (rw-rw-rw-)
3. Todos os outros arquivos `.php` devem ter permissão **644** (rw-r--r--)
4. Todas as pastas devem ter permissão **755** (rwxr-xr-x)

**Se tiver acesso SSH (Terminal):**
```bash
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod 666 brindes.db
chmod 666 data_log.csv
chmod 644 *.php
chmod 755 inc templates imgs
chmod 644 inc/*.php templates/*.php
```

### 3️⃣ Verificação de Funcionamento

Acesse: **http://brindes.alianca.ind.br**

Você deve ver a página inicial do sistema de brindes.

#### Testar Área de Funcionários:
1. Digite um CPF válido ou matrícula
2. O sistema deve gerar o QR Code

#### Testar Área RH:
1. Clique em "Acesso RH"
2. Faça login com:
   - **Usuário:** `rhadmin`
   - **Senha:** `rhadmin1927`
3. Teste o scanner de QR Code
4. Teste "Sair da Conta" - deve pedir login novamente

### 4️⃣ Verificação de Segurança

✅ **Teste de segurança do banco:**
Tente acessar: `http://brindes.alianca.ind.br/brindes.db`
- **Resultado esperado:** Erro 403 Forbidden (bloqueado pelo .htaccess)

✅ **Teste de segurança do config:**
Tente acessar: `http://brindes.alianca.ind.br/config.php`
- **Resultado esperado:** Erro 403 Forbidden (bloqueado pelo .htaccess)

✅ **Teste de segurança dos logs:**
Tente acessar: `http://brindes.alianca.ind.br/data_log.csv`
- **Resultado esperado:** Erro 403 Forbidden (bloqueado pelo .htaccess)

### 5️⃣ Resolução de Problemas Comuns

#### ❌ Problema: Erro 500 Internal Server Error
**Solução:**
1. Verifique se o arquivo `.htaccess` foi enviado corretamente
2. Verifique permissões dos arquivos
3. Verifique logs de erro do PHP no cPanel: `php_errors.log`

#### ❌ Problema: "Database locked" ou erro de escrita no banco
**Solução:**
1. Verifique se `brindes.db` tem permissão 666
2. Verifique se a pasta tem permissão 755

#### ❌ Problema: QR Code não aparece
**Solução:**
1. Verifique se `allow_url_fopen` está habilitado no PHP
2. Verifique se o servidor permite requisições externas (Google Charts API)
3. Entre em contato com suporte do servidor se necessário

#### ❌ Problema: CSS não carrega / página sem estilo
**Solução:**
1. Verifique se `templates/base.php` foi enviado corretamente
2. Limpe cache do navegador (Ctrl + F5)

#### ❌ Problema: Logout não funciona
**Solução:**
1. Limpe cookies do navegador
2. Feche e reabra o navegador
3. Tente em aba anônima/privada

### 6️⃣ Credenciais de Acesso RH

Os seguintes usuários estão configurados para acessar a área do RH:

| Usuário | Senha |
|---------|-------|
| rhadmin | rhadmin1927 |
| jose.neto | alianca1927 |
| sara.guimaraes | alianca1927 |
| patricia.simoes | alianca1927 |
| liberato.silva | alianca1927 |

**Para alterar senhas:** Edite o arquivo `config.php` na seção `$RH_USERS`.

### 7️⃣ Backup Automático (Recomendado)

Configure backup automático no cPanel:
1. Acesse "Backup" no cPanel
2. Configure backup diário para:
   - `brindes.db` (banco de dados)
   - `data_log.csv` (logs)

### 8️⃣ Configuração HTTPS (Opcional mas Recomendado)

Se o domínio tiver certificado SSL instalado:

1. Edite `config.php` e mude:
   ```php
   define('BASE_URL', 'https://brindes.alianca.ind.br');
   ```

2. Edite `config.php` e mude:
   ```php
   ini_set('session.cookie_secure', 1);
   ```

3. No `.htaccess`, adicione no início:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

Isso forçará HTTPS e permitirá usar a câmera do celular no scanner.

---

## ✅ Checklist Final

Antes de considerar a instalação completa, verifique:

- [ ] Todos os arquivos foram enviados
- [ ] Permissões estão corretas (666 para DB e CSV, 644 para PHP)
- [ ] Página inicial carrega corretamente
- [ ] Funcionário consegue gerar QR Code
- [ ] Login RH funciona
- [ ] Scanner QR funciona
- [ ] Dar baixa funciona e registra no banco
- [ ] Logout funciona e pede credenciais novamente
- [ ] Arquivos sensíveis estão bloqueados (403)
- [ ] Lista de funcionários carrega
- [ ] Logs aparecem corretamente

---

## 📞 Suporte

Se encontrar problemas:
1. Verifique o arquivo `php_errors.log` na pasta do projeto
2. Verifique os logs de erro do Apache no cPanel
3. Teste localmente com `php -S localhost:8000` para isolar problemas do servidor

**O sistema está 100% funcional e pronto para produção!**
