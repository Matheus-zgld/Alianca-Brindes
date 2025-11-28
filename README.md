## 🎁 Sistema de Resgate de Brindes de Final de Ano 🎄

Este é um sistema web simples e robusto, desenvolvido em **PHP**, para gerenciar a entrega de brindes aos funcionários da **Aliança Industrial**, garantindo que cada um receba **apenas um brinde** de forma eficiente e rastreável.

---

## 🚀 INÍCIO RÁPIDO

### Para colocar o sistema online AGORA:

1. **Transfira os arquivos** para: `/ssd/aliancaind/public_html/brindes.alianca.br/`
2. **Configure permissões**: `chmod 666 brindes.db data_log.csv`
3. **Acesse**: http://brindes.alianca.ind.br

📖 **Veja o guia completo**: [INICIO_RAPIDO.md](INICIO_RAPIDO.md)

---

## ✨ Funcionalidades Principais

  * **Cadastro e Geração de QR Code:** O funcionário insere seu **CPF** ou **Matrícula** para gerar um QR Code único, que pode ser salvo ou impresso.
  * **Controle de Duplicidade:** O funcionário pode gerar o QR Code quantas vezes quiser enquanto o brinde estiver pendente. Após a baixa, a geração é **automaticamente bloqueada**.
  * **Área Segura do RH:** Acessada via autenticação HTTP Basic, permite dar baixa nas entregas.
  * **Leitura Versátil:** O RH pode escanear o QR Code usando a **câmera do celular** ou inserir o código manualmente.
  * **Rastreio em Tempo Real:** A tela do RH exibe uma lista atualizada de todos os funcionários que já tiveram o brinde entregue.
  * **Validação Reforçada:** O cadastro inclui validação do **dígito verificador do CPF** e verifica se a combinação CPF/Matrícula é única.
  * **Sistema de Logs:** Registra todas as ações importantes em arquivo CSV para auditoria.

---

## 🛠️ Tecnologias Utilizadas

| Componente | Tecnologia | Descrição |
| :--- | :--- | :--- |
| **Backend** | PHP 7.4+ | Linguagem principal do servidor |
| **Banco de Dados** | SQLite 💾 | Banco de dados leve e eficiente |
| **QR Code** | Google Charts API | Geração dinâmica de códigos QR |
| **Frontend** | HTML/CSS/JS 🎨 | Interface com design em **Azul Escuro** e **Amarelo Ouro** |
| **Servidor Web** | Apache | Com mod_rewrite para URLs amigáveis |

---

## 📂 Estrutura do Projeto

```
/brindes-alianca/
├── config.php              # ⚙️ Configurações centralizadas
├── .htaccess              # 🔒 Segurança e otimizações Apache
├── index.php              # 🏠 Página inicial (funcionários)
├── rh.php                 # 👔 Área do RH (scanner QR)
├── dar_baixa.php          # ✅ Processamento de baixa
├── rh_funcionarios.php    # 📋 Lista de funcionários
├── rh_logs.php            # 📊 Visualização de logs
├── rh_logout.php          # 🚪 Logout do RH
├── brindes.db             # 💾 Banco de dados SQLite
├── data_log.csv           # 📝 Arquivo de logs
├── deploy.sh              # 🚀 Script de deploy automático
├── verify.php             # 🔍 Verificação do sistema (excluir após uso)
├── imgs/                  # 🖼️ Imagens e logo
├── inc/                   # 📦 Funções PHP
│   └── functions.php
├── scripts/               # 🔧 Scripts auxiliares
└── templates/             # 🎨 Templates HTML/PHP
    ├── base.php
    ├── funcionario_home.php
    ├── qr_code_display.php
    ├── rh_home.php
    ├── rh_status.php
    ├── rh_confirmacao.php
    ├── rh_funcionarios.php
    ├── rh_logs.php
    └── status.php
```

---

## 📚 Documentação

- **[INICIO_RAPIDO.md](INICIO_RAPIDO.md)** - Guia rápido para colocar online em 3 passos
- **[INSTALACAO.md](INSTALACAO.md)** - Guia completo de instalação passo a passo
- **[RESUMO_PREPARACAO.md](RESUMO_PREPARACAO.md)** - Resumo de tudo que foi preparado

---

## 🌐 URLs de Acesso

Após a instalação, o sistema estará disponível em:

| Página | URL | Descrição |
|--------|-----|-----------|
| **Página Inicial** | http://brindes.alianca.ind.br/ | Funcionários geram QR Code |
| **Área do RH** | http://brindes.alianca.ind.br/rh.php | Scanner e controle de entregas |
| **Funcionários** | http://brindes.alianca.ind.br/rh_funcionarios.php | Lista completa de funcionários |
| **Logs** | http://brindes.alianca.ind.br/rh_logs.php | Histórico de eventos |

---

## 👥 Credenciais de Acesso (RH)

| Usuário | Senha | Perfil |
|---------|-------|--------|
| rhadmin | rhadmin1927 | Administrador |
| jose.neto | alianca1927 | RH |
| sara.guimaraes | alianca1927 | RH |
| patricia.simoes | alianca1927 | RH |
| liberato.silva | alianca1927 | RH |

**Para adicionar/modificar usuários**, edite o arquivo `config.php`.

---

## ⚡ Instalação Rápida

### Opção 1: Deploy Automático (Linux/SSH)

```bash
# 1. Transfira os arquivos para o servidor
scp -r * usuario@servidor:/ssd/aliancaind/public_html/brindes.alianca.br/

# 2. Conecte via SSH
ssh usuario@servidor

# 3. Execute o script de deploy
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod +x deploy.sh
./deploy.sh
```

### Opção 2: Manual (FTP/SFTP)

```bash
# 1. Copie todos os arquivos via FTP para:
/ssd/aliancaind/public_html/brindes.alianca.br/

# 2. Via SSH, ajuste as permissões:
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod 666 brindes.db data_log.csv
chmod 644 *.php .htaccess
```

### 3. Verificar Instalação

Acesse: http://brindes.alianca.ind.br/verify.php

✅ Verifica extensões, permissões e conectividade

⚠️ **IMPORTANTE**: Exclua `verify.php` após a verificação!

---

## 🔧 Configuração

O arquivo `config.php` centraliza todas as configurações:

```php
// Ambiente (development ou production)
define('ENVIRONMENT', 'production');

// URL base do site
define('BASE_URL', 'http://brindes.alianca.ind.br');

// Cores do tema
define('BG_COLOR', '#000080');  // Azul escuro
define('FG_COLOR', '#FFD700');  // Amarelo ouro

// Usuários do RH
$RH_USERS = [
    'rhadmin' => 'rhadmin1927',
    'jose.neto' => 'alianca1927',
    // Adicione mais usuários aqui
];
```

---

## 🔒 Segurança

O sistema implementa várias camadas de segurança:

- ✅ **Autenticação HTTP Basic** para área do RH
- ✅ **Proteção de arquivos sensíveis** via `.htaccess`
- ✅ **Validação de CPF** com dígito verificador
- ✅ **Logs de auditoria** de todas as ações
- ✅ **Sessões seguras** com configurações otimizadas
- ✅ **Banco de dados protegido** (não acessível via web)

---

## 🐛 Solução de Problemas

### Problema: Site não carrega

**Solução:**
```bash
# Verifique permissões
ls -la /ssd/aliancaind/public_html/brindes.alianca.br/

# Reinicie o Apache
sudo systemctl restart apache2
```

### Problema: Erro de banco de dados

**Solução:**
```bash
# Ajuste permissões do banco
chmod 666 brindes.db
chown www-data:www-data brindes.db
```

### Problema: QR Code não aparece

**Solução:**
```bash
# Instale a extensão curl
sudo apt install php-curl
sudo systemctl restart apache2
```

**Mais soluções:** Consulte [INSTALACAO.md](INSTALACAO.md) seção "Solução de Problemas"

---

## 📊 Fluxo de Uso

### Para Funcionários:

1. Acessa http://brindes.alianca.ind.br/
2. Informa **CPF** ou **Matrícula**
3. Sistema gera **QR Code único**
4. Funcionário salva/imprime o QR Code
5. Apresenta ao RH para resgatar o brinde

### Para o RH:

1. Acessa http://brindes.alianca.ind.br/rh.php
2. Faz login com credenciais
3. Escaneia QR Code do funcionário (câmera ou manual)
4. Sistema valida e mostra dados do funcionário
5. Confirma entrega do brinde
6. Funcionário não pode mais gerar novo QR Code

---

## 📝 Logs e Auditoria

Todos os eventos são registrados em `data_log.csv`:

- Geração de QR Codes
- Tentativas de acesso
- Entregas confirmadas
- Logins do RH
- Erros e exceções

Acesse os logs em: http://brindes.alianca.ind.br/rh_logs.php

---

## 🔄 Backup

### Backup Manual:

```bash
# Backup do banco de dados
cp brindes.db brindes_$(date +%Y%m%d).db

# Backup dos logs
cp data_log.csv data_log_$(date +%Y%m%d).csv
```

### Backup Automático (cron):

```bash
# Edite o crontab
crontab -e

# Adicione (backup diário às 2h)
0 2 * * * cp /ssd/aliancaind/public_html/brindes.alianca.br/brindes.db /backup/brindes_$(date +\%Y\%m\%d).db
```

---

## 🎨 Personalização

### Alterar cores do sistema:

Edite `config.php`:
```php
define('BG_COLOR', '#000080');  // Azul escuro
define('FG_COLOR', '#FFD700');  // Amarelo ouro
```

### Alterar logo:

Substitua o arquivo `imgs/logo.png` pela sua logo.

---

## 📈 Estatísticas

Veja estatísticas em tempo real na área do RH:

- Total de funcionários cadastrados
- Brindes já resgatados
- Brindes pendentes
- Últimas entregas
- Logs de atividades

---

## 🤝 Suporte

Para dúvidas ou problemas:

1. Consulte a [documentação completa](INSTALACAO.md)
2. Verifique os [logs do sistema](data_log.csv)
3. Execute [verify.php](verify.php) para diagnóstico

---

## 📜 Licença

Sistema desenvolvido para uso interno da **Aliança Industrial**.

---

## 🎉 Pronto para Usar!

O sistema está **otimizado e pronto** para ser colocado em produção.

**Próximos passos:**
1. Siga o [INICIO_RAPIDO.md](INICIO_RAPIDO.md)
2. Transfira os arquivos
3. Acesse o site
4. Comece a usar!

---

**Sistema de Brindes - Aliança Industrial**  
*Versão PHP - Preparado para produção*

  -----