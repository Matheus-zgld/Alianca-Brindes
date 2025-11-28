# 🚀 Guia de Instalação - Sistema de Brindes Aliança

Este guia detalha os passos necessários para instalar o sistema no servidor de produção.

## 📋 Pré-requisitos

- **Servidor Web**: Apache 2.4+
- **PHP**: Versão 7.4 ou superior (recomendado: PHP 8.0+)
- **Extensões PHP necessárias**:
  - `pdo_sqlite` (para banco de dados SQLite)
  - `mbstring` (para manipulação de strings)
  - `curl` ou `allow_url_fopen` habilitado (para geração de QR codes)
  - `gd` (opcional, para manipulação de imagens)
- **Permissões de escrita** no diretório da aplicação

---

## 📦 Passo 1: Transferir arquivos para o servidor

1. **Conecte-se ao servidor** via FTP, SFTP ou SSH
2. **Navegue até o diretório**: `/ssd/aliancaind/public_html/brindes.alianca.br/`
3. **Transfira todos os arquivos** deste projeto para o diretório

### Estrutura de arquivos no servidor:
```
/ssd/aliancaind/public_html/brindes.alianca.br/
├── .htaccess                    # Configuração do Apache
├── config.php                   # Configurações do sistema
├── index.php                    # Página inicial (funcionários)
├── rh.php                       # Área do RH
├── dar_baixa.php               # Processamento de baixa
├── rh_funcionarios.php         # Lista de funcionários
├── rh_logs.php                 # Visualização de logs
├── rh_logout.php               # Logout do RH
├── brindes.db                  # Banco de dados SQLite
├── data_log.csv                # Arquivo de logs
├── imgs/                       # Imagens e logo
├── inc/                        # Funções PHP
│   └── functions.php
├── scripts/                    # Scripts auxiliares
└── templates/                  # Templates HTML/PHP
```

---

## 🔧 Passo 2: Configurar permissões de arquivos

Execute os seguintes comandos no servidor (via SSH):

```bash
# Entre no diretório do projeto
cd /ssd/aliancaind/public_html/brindes.alianca.br/

# Define permissões para diretórios
find . -type d -exec chmod 755 {} \;

# Define permissões para arquivos
find . -type f -exec chmod 644 {} \;

# Permissões especiais para arquivos que precisam ser escritos
chmod 666 brindes.db
chmod 666 data_log.csv
chmod 666 brindes.db.bak*

# Se necessário, ajuste o proprietário (substitua www-data pelo usuário do Apache)
chown -R www-data:www-data .
```

**Alternativa via FTP**: Configure as permissões manualmente:
- Diretórios: `755`
- Arquivos PHP/HTML: `644`
- `brindes.db`: `666` (leitura e escrita)
- `data_log.csv`: `666` (leitura e escrita)

---

## ⚙️ Passo 3: Configurar o arquivo config.php

Edite o arquivo `config.php` e ajuste as configurações conforme necessário:

```php
// Altere para 'production' em ambiente de produção
define('ENVIRONMENT', 'production');

// Ajuste a URL base (remova http:// se usar HTTPS)
define('BASE_URL', 'http://brindes.alianca.ind.br');

// Se usar HTTPS, descomente esta linha:
// ini_set('session.cookie_secure', 1);
```

---

## 🌐 Passo 4: Configurar o domínio

### Opção A: Domínio já está configurado

Se o domínio `brindes.alianca.ind.br` já aponta para `/ssd/aliancaind/public_html/brindes.alianca.br/`, pule para o Passo 5.

### Opção B: Configurar Virtual Host no Apache

1. Crie ou edite o arquivo de configuração do Apache:

```bash
sudo nano /etc/apache2/sites-available/brindes.alianca.conf
```

2. Adicione a seguinte configuração:

```apache
<VirtualHost *:80>
    ServerName brindes.alianca.ind.br
    ServerAlias www.brindes.alianca.ind.br
    
    DocumentRoot /ssd/aliancaind/public_html/brindes.alianca.br
    
    <Directory /ssd/aliancaind/public_html/brindes.alianca.br>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/brindes_error.log
    CustomLog ${APACHE_LOG_DIR}/brindes_access.log combined
</VirtualHost>
```

3. Ative o site e reinicie o Apache:

```bash
sudo a2ensite brindes.alianca.conf
sudo systemctl restart apache2
```

### Opção C: Configurar DNS

Certifique-se de que o DNS do domínio `brindes.alianca.ind.br` aponta para o IP do servidor.

---

## ✅ Passo 5: Verificar a instalação

1. **Acesse o site**: Abra o navegador e vá para `http://brindes.alianca.ind.br`
2. **Você deverá ver**: A página inicial do sistema (tela azul com amarelo)
3. **Teste a área do RH**: 
   - Acesse `http://brindes.alianca.ind.br/rh.php`
   - Use as credenciais: 
     - Usuário: `rhadmin`
     - Senha: `rhadmin1927`

---

## 🔍 Passo 6: Executar verificação de sistema

Crie um arquivo temporário `verify.php` na raiz do projeto:

```php
<?php
require_once __DIR__ . '/config.php';

echo "<h1>Verificação do Sistema</h1>";

// Verifica extensões PHP
$extensions = ['pdo_sqlite', 'mbstring', 'curl', 'json'];
echo "<h2>Extensões PHP:</h2><ul>";
foreach($extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<li>$ext: " . ($loaded ? '✅ OK' : '❌ NÃO INSTALADA') . "</li>";
}
echo "</ul>";

// Verifica permissões de arquivos
echo "<h2>Permissões de Arquivos:</h2><ul>";
$files = ['brindes.db', 'data_log.csv', 'config.php'];
foreach($files as $file) {
    $exists = file_exists($file);
    $writable = is_writable($file);
    echo "<li>$file: ";
    echo ($exists ? '✅ Existe' : '❌ Não encontrado');
    echo " | ";
    echo ($writable ? '✅ Gravável' : '⚠️ Apenas leitura');
    echo "</li>";
}
echo "</ul>";

// Verifica banco de dados
echo "<h2>Banco de Dados:</h2>";
try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM funcionarios');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Conexão OK - Total de funcionários: " . $result['total'] . "</p>";
} catch(Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Informações do servidor
echo "<h2>Informações do Servidor:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "</ul>";

echo "<hr><p><strong>⚠️ IMPORTANTE:</strong> Exclua este arquivo após a verificação!</p>";
?>
```

Acesse `http://brindes.alianca.ind.br/verify.php` e verifique se tudo está OK.

**IMPORTANTE**: Exclua o arquivo `verify.php` após a verificação por segurança.

---

## 🔐 Passo 7: Segurança adicional (Recomendado)

### 1. Configurar HTTPS (SSL)

Se você tem um certificado SSL:

```bash
# Instale o Certbot (Let's Encrypt)
sudo apt install certbot python3-certbot-apache

# Obtenha o certificado
sudo certbot --apache -d brindes.alianca.ind.br
```

Depois, atualize o `config.php`:
```php
define('BASE_URL', 'https://brindes.alianca.ind.br');
ini_set('session.cookie_secure', 1);
```

### 2. Proteger arquivos sensíveis

O `.htaccess` já inclui proteção, mas verifique se está funcionando:
- Tente acessar `http://brindes.alianca.ind.br/brindes.db` - deve dar erro 403
- Tente acessar `http://brindes.alianca.ind.br/config.php` - deve dar erro 403

### 3. Backup regular

Configure um cron job para backup automático:

```bash
# Edite o crontab
crontab -e

# Adicione esta linha (backup diário às 2h da manhã)
0 2 * * * cp /ssd/aliancaind/public_html/brindes.alianca.br/brindes.db /ssd/aliancaind/backups/brindes_$(date +\%Y\%m\%d).db
```

---

## 📱 Passo 8: Teste completo do sistema

### Teste 1: Área do Funcionário
1. Acesse `http://brindes.alianca.ind.br`
2. Insira um CPF ou Matrícula válida
3. Verifique se o QR Code é gerado corretamente

### Teste 2: Área do RH
1. Acesse `http://brindes.alianca.ind.br/rh.php`
2. Faça login com as credenciais do RH
3. Teste o scanner de QR Code (usando câmera ou entrada manual)
4. Confirme uma entrega de brinde
5. Verifique se o status foi atualizado corretamente

### Teste 3: Logs
1. Acesse `http://brindes.alianca.ind.br/rh_logs.php`
2. Verifique se os eventos estão sendo registrados

---

## ❓ Solução de Problemas

### Problema: "Página não encontrada" ou "403 Forbidden"

**Solução**: Verifique as permissões e o `.htaccess`
```bash
chmod 644 .htaccess
sudo systemctl restart apache2
```

### Problema: "Erro ao conectar com o banco de dados"

**Solução**: Verifique as permissões do arquivo `brindes.db`
```bash
chmod 666 brindes.db
chown www-data:www-data brindes.db
```

### Problema: QR Code não é gerado

**Solução**: Verifique se o `curl` ou `allow_url_fopen` está habilitado
```bash
php -m | grep curl
```

Se não estiver instalado:
```bash
sudo apt install php-curl
sudo systemctl restart apache2
```

### Problema: Sessão não mantém login do RH

**Solução**: Verifique as permissões do diretório de sessões do PHP
```bash
sudo chmod 1733 /var/lib/php/sessions
```

---

## 📞 Suporte

Para problemas ou dúvidas:
- Verifique os logs de erro do Apache: `/var/log/apache2/brindes_error.log`
- Verifique os logs do PHP: `php_errors.log` (na raiz do projeto)
- Consulte o arquivo `data_log.csv` para rastrear eventos do sistema

---

## ✅ Checklist Final

- [ ] Arquivos transferidos para `/ssd/aliancaind/public_html/brindes.alianca.br/`
- [ ] Permissões configuradas corretamente
- [ ] `config.php` ajustado para produção
- [ ] Domínio `brindes.alianca.ind.br` acessível
- [ ] Página inicial carrega corretamente
- [ ] Login do RH funciona
- [ ] QR Code é gerado corretamente
- [ ] Baixa de brindes funciona
- [ ] Logs estão sendo registrados
- [ ] Arquivo `verify.php` foi excluído (se criado)
- [ ] Backup configurado

---

## 🎉 Pronto!

O sistema está instalado e pronto para uso. Acesse `http://brindes.alianca.ind.br` para começar a usar.

**URL de acesso:**
- **Funcionários**: http://brindes.alianca.ind.br/
- **Área do RH**: http://brindes.alianca.ind.br/rh.php

**Credenciais de teste do RH:**
- Usuário: `rhadmin`
- Senha: `rhadmin1927`
