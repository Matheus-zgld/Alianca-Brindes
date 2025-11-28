# 📋 LISTA DE ARQUIVOS E SUAS FUNÇÕES

## 🆕 ARQUIVOS NOVOS CRIADOS (para facilitar instalação):

### Documentação:
- **`LEIA_ME_PRIMEIRO.md`** ⭐ - Resumo executivo em 3 passos (COMECE POR AQUI!)
- **`COMO_ABRIR.md`** - Guia detalhado de como abrir o site
- **`INICIO_RAPIDO.md`** - Guia rápido de instalação
- **`INSTALACAO.md`** - Guia completo de instalação passo a passo
- **`RESUMO_PREPARACAO.md`** - Resumo de tudo que foi preparado/modificado

### Configuração:
- **`config.php`** - Arquivo central de configuração (URLs, senhas, cores)
- **`.htaccess`** - Configuração do Apache (segurança, cache, redirecionamento)

### Scripts:
- **`deploy.sh`** - Script de deploy automático (Linux/SSH)
- **`verify.php`** - Script de verificação do sistema (EXCLUIR após uso!)

### Redirecionamento:
- **`index.html`** - Redireciona para index.php (fallback)

---

## 📁 ARQUIVOS ORIGINAIS (já existiam no projeto):

### Páginas principais:
- **`index.php`** - Página inicial para funcionários
- **`rh.php`** - Área do RH (scanner QR)
- **`dar_baixa.php`** - Processa entrega de brinde
- **`rh_funcionarios.php`** - Lista todos funcionários
- **`rh_logs.php`** - Visualiza logs do sistema
- **`rh_logout.php`** - Faz logout do RH

### Banco de dados e logs:
- **`brindes.db`** - Banco de dados SQLite (funcionários e entregas)
- **`data_log.csv`** - Arquivo de logs de eventos
- **`brindes.db.bak*`** - Backups do banco de dados

### Estrutura:
- **`inc/functions.php`** - Funções PHP centralizadas
- **`imgs/`** - Pasta de imagens (logo, etc)
- **`scripts/`** - Scripts auxiliares
- **`templates/`** - Templates HTML/PHP

---

## ✏️ ARQUIVOS MODIFICADOS (otimizados):

1. **`inc/functions.php`**
   - Agora usa `config.php`
   - Usuários do RH centralizados

2. **`index.php`**
   - Usa constantes do config (cores, URLs)

3. **`rh.php`**
   - Usa constantes do config

4. **`rh_funcionarios.php`**
   - Usa constantes do config

5. **`templates/base.php`**
   - Usa constantes do config como fallback

6. **`README.md`**
   - Atualizado com novas informações

---

## ⚠️ ARQUIVOS IMPORTANTES:

### DEVEM ter permissão 666 (leitura + escrita):
- `brindes.db`
- `data_log.csv`

### DEVEM ter permissão 644 (apenas leitura):
- Todos os arquivos `.php`
- `.htaccess`
- `config.php`

### DEVEM ser EXCLUÍDOS após uso:
- `verify.php` (após verificar que está tudo OK)

---

## 🔒 ARQUIVOS PROTEGIDOS (não acessíveis via web):

O `.htaccess` protege estes arquivos automaticamente:
- `config.php` - Configurações sensíveis
- `brindes.db` - Banco de dados
- `data_log.csv` - Logs
- `.htaccess` - Próprio arquivo de configuração
- `php_errors.log` - Logs de erros
- Arquivos `.git` e `.env`

Se alguém tentar acessar diretamente (ex: `http://site.com/config.php`), receberá erro 403 (Forbidden).

---

## 📦 O QUE COPIAR PARA O SERVIDOR:

### ✅ COPIE TUDO, exceto:
- `old_python_backup/` (se existir) - Backup da versão Python antiga
- `.git/` (se existir) - Controle de versão

### ✅ ESTRUTURA FINAL NO SERVIDOR:

```
/ssd/aliancaind/public_html/brindes.alianca.br/
├── .htaccess
├── config.php
├── index.html
├── index.php
├── rh.php
├── dar_baixa.php
├── rh_funcionarios.php
├── rh_logs.php
├── rh_logout.php
├── brindes.db
├── data_log.csv
├── deploy.sh
├── verify.php
├── LEIA_ME_PRIMEIRO.md
├── COMO_ABRIR.md
├── INICIO_RAPIDO.md
├── INSTALACAO.md
├── RESUMO_PREPARACAO.md
├── README.md
├── imgs/
│   └── logo.png
├── inc/
│   └── functions.php
├── scripts/
│   └── (vários arquivos)
└── templates/
    └── (vários arquivos)
```

---

## 🎯 ORDEM DE LEITURA RECOMENDADA:

1. **`LEIA_ME_PRIMEIRO.md`** - Resumo em 3 passos ⭐
2. **`COMO_ABRIR.md`** - Como abrir o site detalhadamente
3. **`INICIO_RAPIDO.md`** - Guia rápido
4. **`INSTALACAO.md`** - Guia completo (se tiver problemas)
5. **`RESUMO_PREPARACAO.md`** - O que foi modificado

---

## 🔧 ARQUIVOS DE CONFIGURAÇÃO:

### `config.php` - PRINCIPAIS CONFIGURAÇÕES:

```php
// Ambiente
ENVIRONMENT = 'production'  // 'development' ou 'production'

// URLs
BASE_URL = 'http://brindes.alianca.ind.br'

// Caminhos
DB_PATH = '/caminho/completo/brindes.db'
LOG_FILE = '/caminho/completo/data_log.csv'

// Cores do tema
BG_COLOR = '#000080'  // Azul escuro
FG_COLOR = '#FFD700'  // Amarelo ouro

// Logo
LOGO_URL = 'http://brindes.alianca.ind.br/imgs/logo.png'

// Usuários RH (pode adicionar/remover)
$RH_USERS = [
    'rhadmin' => 'rhadmin1927',
    'jose.neto' => 'alianca1927',
    'sara.guimaraes' => 'alianca1927',
    'patricia.simoes' => 'alianca1927',
    'liberato.silva' => 'alianca1927'
]
```

---

## 📊 TAMANHO DOS ARQUIVOS (aproximado):

- Banco de dados (`brindes.db`): ~100-500 KB (depende do número de funcionários)
- Logs (`data_log.csv`): ~10-50 KB (aumenta com o uso)
- Código PHP: ~200 KB (total)
- Templates: ~150 KB (total)
- Documentação: ~100 KB (total)

**Total do projeto: ~500 KB - 1 MB**

---

## 🔄 MANUTENÇÃO:

### Arquivos que crescem com o tempo:
- `brindes.db` - Conforme adiciona funcionários
- `data_log.csv` - Conforme registra eventos
- `php_errors.log` - Se houver erros

### Recomendação:
Faça backup regular destes arquivos!

---

## ✅ CHECKLIST DE ARQUIVOS:

Antes de considerar o deploy completo, verifique:

- [ ] Todos os arquivos PHP estão no servidor
- [ ] `config.php` está presente e configurado
- [ ] `.htaccess` está presente
- [ ] `brindes.db` tem permissão 666
- [ ] `data_log.csv` tem permissão 666
- [ ] Pasta `imgs/` existe com logo.png
- [ ] Pasta `inc/` existe com functions.php
- [ ] Pasta `templates/` existe com todos templates
- [ ] `verify.php` foi executado
- [ ] `verify.php` foi excluído após verificação
- [ ] Site abre corretamente

---

## 🎉 PRONTO!

Todos os arquivos estão prontos e otimizados para produção!

**Próximo passo:** Siga o [LEIA_ME_PRIMEIRO.md](LEIA_ME_PRIMEIRO.md)

---

**Sistema de Brindes - Aliança Industrial**  
*Preparado e otimizado para produção*
