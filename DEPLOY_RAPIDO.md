# ✅ CHECKLIST FINAL - Pronto para Produção

## 🎯 Destino dos Arquivos
```
/ssd/aliancaind/public_html/brindes.alianca.br/
```

## 📤 Upload via cPanel (alianca.ind.br:2083)

### 1. Acesse o Gerenciador de Arquivos
- Login: alianca.ind.br:2083
- Navegue até: `/ssd/aliancaind/public_html/brindes.alianca.br/`

### 2. Faça Upload de TODOS os Arquivos

#### ✅ Arquivos na Raiz (18 arquivos):
```
config.php              ← CONFIGURAÇÃO PRINCIPAL
index.php               ← PÁGINA INICIAL
rh.php                  ← ÁREA RH
rh_login.php            ← LOGIN RH  
rh_logout.php           ← LOGOUT RH
rh_funcionarios.php     ← LISTA FUNCIONÁRIOS
rh_logs.php             ← LOGS DO SISTEMA
dar_baixa.php           ← DAR BAIXA BRINDE
brindes.db              ← BANCO DE DADOS
data_log.csv            ← LOG DE EVENTOS
.htaccess               ← CONFIGURAÇÃO APACHE (IMPORTANTE!)
```

#### ✅ Pasta inc/ (1 arquivo):
```
inc/functions.php       ← FUNÇÕES DO SISTEMA
```

#### ✅ Pasta templates/ (9 arquivos):
```
templates/base.php
templates/funcionario_home.php
templates/qr_code_display.php
templates/status.php
templates/rh_home.php
templates/rh_status.php
templates/rh_confirmacao.php
templates/rh_funcionarios.php
templates/rh_logs.php
```

#### ✅ Pasta imgs/ (se tiver logo):
```
imgs/logo.png           ← LOGO DA EMPRESA
```

### 3. Configure Permissões (MUITO IMPORTANTE!)

No Gerenciador de Arquivos do cPanel:

1. **Banco de dados** `brindes.db`:
   - Botão direito → Permissões → **666**
   
2. **Log** `data_log.csv`:
   - Botão direito → Permissões → **666**

3. **Todos os arquivos .php**:
   - Devem estar em **644** (padrão)

4. **Todas as pastas** (inc, templates, imgs):
   - Devem estar em **755** (padrão)

### 4. Teste Imediatamente

#### ✅ Teste 1: Página Inicial
- Acesse: `http://brindes.alianca.ind.br`
- Deve carregar a página azul com formulário

#### ✅ Teste 2: Gerar QR Code
- Digite um CPF ou matrícula
- Deve aparecer o QR Code

#### ✅ Teste 3: Login RH
- Clique "Acesso RH"
- Login: `rhadmin` / Senha: `rhadmin1927`
- Deve entrar na área do RH

#### ✅ Teste 4: Logout
- Clique "Sair da Conta"
- Deve voltar para página inicial
- Clique "Acesso RH" novamente
- Deve pedir login de novo ✅

#### ✅ Teste 5: Segurança
Tente acessar (devem dar erro 403):
- `http://brindes.alianca.ind.br/brindes.db` → ❌ Bloqueado
- `http://brindes.alianca.ind.br/config.php` → ❌ Bloqueado
- `http://brindes.alianca.ind.br/data_log.csv` → ❌ Bloqueado

## 🚨 Se Algo Não Funcionar

### Erro 500?
- Verifique se `.htaccess` foi enviado
- Verifique permissões (666 no banco e log)

### Banco não abre?
- Permissão do `brindes.db` DEVE ser **666**

### CSS não carrega?
- Limpe cache do navegador (Ctrl + F5)
- Verifique se `templates/` foi enviado

### QR Code não aparece?
- Normal! O servidor precisa permitir requisições externas
- Entre em contato com suporte do servidor

## 📊 Configurações Atuais

✅ **URL de Produção:** `http://brindes.alianca.ind.br`
✅ **Ambiente:** Production
✅ **Erros PHP:** Desabilitados (logs em php_errors.log)
✅ **Banco de dados:** SQLite (brindes.db)
✅ **Logs:** data_log.csv
✅ **Segurança:** Arquivos sensíveis bloqueados via .htaccess

## 👥 Usuários RH (5 usuários)

| Usuário | Senha |
|---------|-------|
| rhadmin | rhadmin1927 |
| jose.neto | alianca1927 |
| sara.guimaraes | alianca1927 |
| patricia.simoes | alianca1927 |
| liberato.silva | alianca1927 |

## 🎉 Funcionalidades 100% Operacionais

✅ Geração de QR Code por CPF ou Matrícula
✅ Scanner de QR Code (câmera + upload de imagem)
✅ Login RH com formulário (não usa HTTP Basic Auth)
✅ Logout funcional (pede credenciais ao retornar)
✅ Dar baixa em brindes
✅ Lista de funcionários com filtros
✅ Logs completos com data DD-MM-YY
✅ Status visual (verde/vermelho)
✅ Design responsivo (mobile + desktop)
✅ Segurança de arquivos sensíveis
✅ Tradução PT-BR completa

---

## 📞 Em caso de dúvidas

Leia o arquivo: `INSTRUCOES_INSTALACAO_SERVIDOR.md`

**Sistema 100% pronto para uso em produção! 🚀**
