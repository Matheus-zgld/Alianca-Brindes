# 🚀 INÍCIO RÁPIDO - Sistema de Brindes Aliança

## Como colocar online AGORA:

### 1️⃣ Transferir arquivos (5 minutos)
```bash
# Via SFTP/FTP, envie TODOS os arquivos para:
/ssd/aliancaind/public_html/brindes.alianca.br/
```

### 2️⃣ Ajustar permissões (2 minutos)
```bash
# Conecte via SSH e execute:
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod 666 brindes.db data_log.csv
chmod 644 *.php .htaccess
chmod 755 imgs scripts templates inc
```

### 3️⃣ Acessar o site (PRONTO!)
```
http://brindes.alianca.ind.br
```

## 🔍 Verificar se está funcionando:

1. **Abra**: http://brindes.alianca.ind.br
   - ✅ Deve mostrar tela azul com formulário

2. **Abra**: http://brindes.alianca.ind.br/verify.php
   - ✅ Verifica se tudo está OK
   - ⚠️ **DEPOIS EXCLUA este arquivo!**

3. **Teste o RH**: http://brindes.alianca.ind.br/rh.php
   - Usuário: `rhadmin`
   - Senha: `rhadmin1927`

## ❌ NÃO está funcionando?

### Erro: "Página não encontrada"
```bash
# Verifique se o Apache está apontando corretamente
ls -la /ssd/aliancaind/public_html/brindes.alianca.br/
```

### Erro: "Banco de dados"
```bash
# Corrija as permissões
chmod 666 brindes.db
chown www-data:www-data brindes.db
```

### Erro: "QR Code não aparece"
```bash
# Instale curl
sudo apt install php-curl
sudo systemctl restart apache2
```

## 📱 URLs importantes:

- **Funcionários**: http://brindes.alianca.ind.br/
- **RH**: http://brindes.alianca.ind.br/rh.php
- **Funcionários cadastrados**: http://brindes.alianca.ind.br/rh_funcionarios.php
- **Logs do sistema**: http://brindes.alianca.ind.br/rh_logs.php

## 👥 Usuários do RH:

| Usuário | Senha |
|---------|-------|
| rhadmin | rhadmin1927 |
| jose.neto | alianca1927 |
| sara.guimaraes | alianca1927 |
| patricia.simoes | alianca1927 |
| liberato.silva | alianca1927 |

## 📝 Arquivos importantes:

- `config.php` - Configurações do sistema
- `.htaccess` - Configuração do Apache
- `brindes.db` - Banco de dados (SQLite)
- `data_log.csv` - Logs de eventos
- `INSTALACAO.md` - Guia completo de instalação

## 🆘 Precisa de ajuda?

Veja o arquivo `INSTALACAO.md` para instruções detalhadas.

---

**LEMBRE-SE**: Após verificar que está tudo OK, EXCLUA o arquivo `verify.php`!
