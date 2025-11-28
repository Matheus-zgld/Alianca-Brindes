# ✅ RESUMO DA PREPARAÇÃO DO SISTEMA

## 📦 O que foi criado/modificado:

### ✨ Novos arquivos criados:

1. **`config.php`** - Arquivo central de configuração
   - Define URLs, caminhos e configurações
   - Usuários do RH centralizados
   - Fácil ajuste entre desenvolvimento/produção

2. **`.htaccess`** - Configuração do Apache
   - Proteção de arquivos sensíveis (DB, logs, config)
   - Otimizações de cache e compressão
   - Páginas de erro customizadas

3. **`INSTALACAO.md`** - Guia completo de instalação
   - Instruções detalhadas passo a passo
   - Configuração de permissões
   - Solução de problemas comuns

4. **`INICIO_RAPIDO.md`** - Guia rápido para colocar online
   - 3 passos simples
   - URLs importantes
   - Comandos prontos para usar

5. **`verify.php`** - Script de verificação do sistema
   - Verifica extensões PHP necessárias
   - Testa permissões de arquivos
   - Valida conexão com banco de dados
   - Testa geração de QR codes
   - ⚠️ Deve ser EXCLUÍDO após uso!

### 🔧 Arquivos modificados:

1. **`inc/functions.php`**
   - Integrado com `config.php`
   - Usuários do RH agora vêm do config
   - Mantém todas as funções originais

2. **`index.php`**
   - Usa constantes do config (BG_COLOR, FG_COLOR, LOGO_URL)

3. **`rh.php`**
   - Usa constantes do config

4. **`rh_funcionarios.php`**
   - Usa constantes do config

5. **`templates/base.php`**
   - Usa constantes do config como fallback

---

## 🚀 Como usar (PASSO A PASSO):

### 1. Transferir arquivos para o servidor

Copie **TODOS** os arquivos desta pasta para:
```
/ssd/aliancaind/public_html/brindes.alianca.br/
```

Você pode usar:
- **FileZilla** (FTP/SFTP)
- **WinSCP** (SFTP)
- **Comando SCP**: 
  ```bash
  scp -r * usuario@servidor:/ssd/aliancaind/public_html/brindes.alianca.br/
  ```

### 2. Ajustar permissões (via SSH)

```bash
cd /ssd/aliancaind/public_html/brindes.alianca.br/

# Permissões para arquivos que precisam ser escritos
chmod 666 brindes.db
chmod 666 data_log.csv

# Se houver backups do banco
chmod 666 brindes.db.bak*

# Opcional: ajustar proprietário (se necessário)
chown -R www-data:www-data .
```

### 3. Verificar se está funcionando

1. **Acesse**: http://brindes.alianca.ind.br
   - ✅ Deve mostrar a tela azul com formulário

2. **Acesse**: http://brindes.alianca.ind.br/verify.php
   - ✅ Verifica todos os componentes
   - ⚠️ **DEPOIS EXCLUA** este arquivo!

3. **Teste o RH**: http://brindes.alianca.ind.br/rh.php
   - Usuário: `rhadmin`
   - Senha: `rhadmin1927`

---

## 🌐 URLs do sistema:

| Página | URL |
|--------|-----|
| **Página Inicial (Funcionários)** | http://brindes.alianca.ind.br/ |
| **Área do RH** | http://brindes.alianca.ind.br/rh.php |
| **Lista de Funcionários** | http://brindes.alianca.ind.br/rh_funcionarios.php |
| **Logs do Sistema** | http://brindes.alianca.ind.br/rh_logs.php |
| **Verificação (temporário)** | http://brindes.alianca.ind.br/verify.php |

---

## 👥 Credenciais de acesso RH:

| Usuário | Senha |
|---------|-------|
| rhadmin | rhadmin1927 |
| jose.neto | alianca1927 |
| sara.guimaraes | alianca1927 |
| patricia.simoes | alianca1927 |
| liberato.silva | alianca1927 |

**Para adicionar novos usuários**: Edite o arquivo `config.php` (linha 50-56)

---

## 🔒 Segurança implementada:

- ✅ Banco de dados protegido (não acessível via URL)
- ✅ Arquivo de configuração protegido
- ✅ Logs protegidos
- ✅ Autenticação HTTP Basic para área do RH
- ✅ Validação de CPF no backend
- ✅ Logs de todas as ações importantes
- ✅ Sessões configuradas com segurança

---

## 📂 Estrutura de arquivos no servidor:

```
/ssd/aliancaind/public_html/brindes.alianca.br/
├── .htaccess                    # ← Configuração Apache
├── config.php                   # ← Configurações centralizadas
├── index.php                    # ← Página inicial
├── rh.php                       # ← Área do RH
├── dar_baixa.php               # ← Processamento de baixa
├── rh_funcionarios.php         # ← Lista de funcionários
├── rh_logs.php                 # ← Visualização de logs
├── rh_logout.php               # ← Logout
├── brindes.db                  # ← Banco de dados (666)
├── data_log.csv                # ← Arquivo de logs (666)
├── verify.php                  # ← Verificação (EXCLUIR após uso!)
├── INSTALACAO.md               # ← Guia completo
├── INICIO_RAPIDO.md            # ← Guia rápido
├── README.md                   # ← Documentação original
├── imgs/                       # ← Imagens
│   └── logo.png
├── inc/                        # ← Funções PHP
│   └── functions.php
├── scripts/                    # ← Scripts auxiliares
└── templates/                  # ← Templates HTML/PHP
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

## ⚙️ Configurações importantes:

### Para usar HTTPS (SSL):

Edite `config.php` e altere:

```php
// Linha 26
define('BASE_URL', 'https://brindes.alianca.ind.br');

// Linha 35 (descomente)
ini_set('session.cookie_secure', 1);
```

### Para adicionar novos usuários do RH:

Edite `config.php`, seção de usuários:

```php
$RH_USERS = [
    'rhadmin' => 'rhadmin1927',
    'novo.usuario' => 'senha123',  // ← Adicione aqui
    // ...
];
```

---

## 🐛 Solução de problemas:

### "Página não encontrada" (404)
- Verifique se os arquivos foram copiados corretamente
- Verifique configuração do Apache/VirtualHost

### "Erro ao conectar com o banco de dados"
```bash
chmod 666 brindes.db
chown www-data:www-data brindes.db
```

### "QR Code não aparece"
```bash
sudo apt install php-curl
sudo systemctl restart apache2
```

### "Sessão expira rapidamente"
Verifique permissões do diretório de sessões:
```bash
ls -la /var/lib/php/sessions
```

---

## 📝 Checklist final:

- [ ] Arquivos copiados para `/ssd/aliancaind/public_html/brindes.alianca.br/`
- [ ] Permissões ajustadas (666 para DB e logs)
- [ ] Site acessível em http://brindes.alianca.ind.br
- [ ] Página inicial carrega corretamente
- [ ] Login do RH funciona
- [ ] QR Code é gerado
- [ ] Baixa de brinde funciona
- [ ] Logs são registrados
- [ ] `verify.php` foi executado
- [ ] `verify.php` foi EXCLUÍDO
- [ ] Backup configurado (opcional)

---

## 🎉 PRONTO PARA PRODUÇÃO!

Seu sistema está otimizado e pronto para ser usado no servidor.

**Próximos passos:**
1. Transfira os arquivos
2. Execute `verify.php` para testar
3. Exclua `verify.php`
4. Comece a usar!

**Dúvidas?** Consulte `INSTALACAO.md` para informações detalhadas.

---

**Sistema de Brindes - Aliança Industrial**  
*Preparado em: 27 de novembro de 2025*
