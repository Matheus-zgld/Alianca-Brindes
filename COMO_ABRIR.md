# 🎯 COMO ABRIR O SITE - GUIA ESPECÍFICO

## 📍 Situação Atual:
- Você tem os arquivos em: `C:\Users\tetor\Downloads\Alianca-Brindes`
- Precisa colocar em: `/ssd/aliancaind/public_html/brindes.alianca.br/`
- Domínio de acesso: `brindes.alianca.ind.br`

---

## 🚀 PASSO A PASSO PARA ABRIR O SITE:

### PASSO 1: Conectar ao Servidor (Escolha uma opção)

#### Opção A: Via FileZilla (MAIS FÁCIL)

1. **Baixe o FileZilla** (se não tiver): https://filezilla-project.org/
2. **Abra o FileZilla**
3. **Conecte ao servidor:**
   - Host: `IP_DO_SERVIDOR` (exemplo: 192.168.1.100)
   - Usuário: `seu_usuario`
   - Senha: `sua_senha`
   - Porta: `22` (SFTP) ou `21` (FTP)
   - Clique em "Conexão Rápida"

4. **No lado esquerdo** (seu computador):
   - Navegue até: `C:\Users\tetor\Downloads\Alianca-Brindes`

5. **No lado direito** (servidor):
   - Navegue até: `/ssd/aliancaind/public_html/brindes.alianca.br/`
   - Se a pasta não existir, crie ela: botão direito → "Criar diretório"

6. **Transfira tudo:**
   - Selecione TODOS os arquivos da pasta local
   - Arraste para o lado direito (servidor)
   - Aguarde a transferência completar

#### Opção B: Via WinSCP

1. **Baixe o WinSCP**: https://winscp.net/
2. **Abra o WinSCP**
3. **Nova Sessão:**
   - Protocolo: SFTP
   - Nome do host: `IP_DO_SERVIDOR`
   - Usuário: `seu_usuario`
   - Senha: `sua_senha`
   - Clique em "Login"

4. **Navegue e copie** (similar ao FileZilla)

#### Opção C: Via SSH (Terminal)

Se você tem acesso SSH:

```powershell
# No PowerShell do Windows
scp -r C:\Users\tetor\Downloads\Alianca-Brindes\* usuario@IP_SERVIDOR:/ssd/aliancaind/public_html/brindes.alianca.br/
```

---

### PASSO 2: Ajustar Permissões no Servidor

**Conecte via SSH** (PuTTY ou terminal):

```bash
# Entre no servidor
ssh usuario@IP_SERVIDOR

# Vá para o diretório
cd /ssd/aliancaind/public_html/brindes.alianca.br/

# Execute o script de deploy (RECOMENDADO)
chmod +x deploy.sh
./deploy.sh

# OU configure manualmente:
chmod 666 brindes.db
chmod 666 data_log.csv
chmod 644 *.php
chmod 644 .htaccess
chmod 755 imgs scripts templates inc
```

---

### PASSO 3: Configurar o Domínio

#### Se o domínio JÁ estiver configurado:

Pule para o PASSO 4!

#### Se o domínio NÃO estiver configurado:

**Via SSH, edite o Apache:**

```bash
# Crie ou edite o arquivo de configuração
sudo nano /etc/apache2/sites-available/brindes.alianca.conf
```

**Cole isto:**

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

**Salve** (Ctrl+X, depois Y, depois Enter)

**Ative o site:**

```bash
sudo a2ensite brindes.alianca.conf
sudo systemctl restart apache2
```

---

### PASSO 4: ABRIR O SITE! 🎉

1. **Abra seu navegador** (Chrome, Firefox, Edge, etc.)

2. **Digite na barra de endereço:**
   ```
   http://brindes.alianca.ind.br
   ```

3. **Você deverá ver:**
   - Tela azul escura (#000080)
   - Logo da empresa no topo
   - Formulário para inserir CPF ou Matrícula
   - Botões amarelo ouro (#FFD700)

---

### PASSO 5: Verificar se Está Tudo OK

1. **Acesse a página de verificação:**
   ```
   http://brindes.alianca.ind.br/verify.php
   ```

2. **Verifique se todos os itens estão com ✅**

3. **Se tudo estiver OK, EXCLUA o verify.php:**
   ```bash
   # Via SSH:
   rm /ssd/aliancaind/public_html/brindes.alianca.br/verify.php
   ```

---

### PASSO 6: Testar a Área do RH

1. **Acesse:**
   ```
   http://brindes.alianca.ind.br/rh.php
   ```

2. **Aparecerá uma janela de login:**
   - Usuário: `rhadmin`
   - Senha: `rhadmin1927`

3. **Você deverá ver:**
   - Scanner de QR Code
   - Lista de funcionários que já resgataram
   - Interface azul e amarela

---

## ❌ PROBLEMAS COMUNS E SOLUÇÕES:

### 1. "Site não carrega" ou "Erro 404"

**Possível causa:** Domínio não está apontando corretamente

**Solução:**
```bash
# Verifique se os arquivos estão no lugar certo:
ls -la /ssd/aliancaind/public_html/brindes.alianca.br/

# Deve mostrar: index.php, rh.php, config.php, etc.
```

**Outra solução:** Verifique o DNS do domínio

---

### 2. "Página em branco" ou "Erro 500"

**Possível causa:** Permissões incorretas ou erro no PHP

**Solução:**
```bash
# Veja os erros do Apache:
tail -f /var/log/apache2/brindes_error.log

# Ou:
tail -f /ssd/aliancaind/public_html/brindes.alianca.br/php_errors.log
```

---

### 3. "Erro ao conectar com banco de dados"

**Possível causa:** Permissões do arquivo brindes.db

**Solução:**
```bash
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod 666 brindes.db
chown www-data:www-data brindes.db
```

---

### 4. "QR Code não aparece"

**Possível causa:** Extensão curl não instalada

**Solução:**
```bash
sudo apt install php-curl
sudo systemctl restart apache2
```

---

### 5. "Não consigo fazer login no RH"

**Possível causa:** Credenciais incorretas

**Solução:** Tente estas credenciais:
- Usuário: `rhadmin`
- Senha: `rhadmin1927`

Se ainda não funcionar, verifique o arquivo `config.php` (linhas 50-56)

---

## 🆘 AINDA NÃO CONSEGUIU?

### Teste direto pelo IP:

Se o domínio não funcionar, tente acessar pelo IP:

```
http://IP_DO_SERVIDOR/
```

Se funcionar pelo IP mas não pelo domínio, o problema é de DNS/configuração do Apache.

---

## 📞 CHECKLIST FINAL:

Antes de considerar pronto, verifique:

- [ ] Arquivos copiados para `/ssd/aliancaind/public_html/brindes.alianca.br/`
- [ ] Permissões ajustadas (666 para .db e .csv)
- [ ] Site abre em `http://brindes.alianca.ind.br`
- [ ] Página inicial mostra formulário azul e amarelo
- [ ] Login do RH funciona (`rhadmin` / `rhadmin1927`)
- [ ] Scanner de QR Code abre
- [ ] `verify.php` foi executado e depois excluído
- [ ] Sistema registra logs corretamente

---

## 🎉 PRONTO!

Seu sistema está no ar e funcionando!

**URLs importantes:**
- **Funcionários**: http://brindes.alianca.ind.br/
- **RH**: http://brindes.alianca.ind.br/rh.php
- **Logs**: http://brindes.alianca.ind.br/rh_logs.php

---

## 📚 DOCUMENTAÇÃO ADICIONAL:

- **[INICIO_RAPIDO.md](INICIO_RAPIDO.md)** - Resumo em 3 passos
- **[INSTALACAO.md](INSTALACAO.md)** - Guia completo detalhado
- **[RESUMO_PREPARACAO.md](RESUMO_PREPARACAO.md)** - O que foi modificado

---

**Boa sorte! 🍀**

Se precisar de ajuda, consulte os arquivos de log ou a documentação completa.
