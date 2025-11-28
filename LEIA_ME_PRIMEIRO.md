# ⚡ RESUMO EXECUTIVO - 3 PASSOS

## 1️⃣ COPIAR ARQUIVOS

### Via FileZilla/WinSCP:
- **De:** `C:\Users\tetor\Downloads\Alianca-Brindes\`
- **Para:** `/ssd/aliancaind/public_html/brindes.alianca.br/`
- **Ação:** Copie TODOS os arquivos

## 2️⃣ AJUSTAR PERMISSÕES (via SSH)

```bash
cd /ssd/aliancaind/public_html/brindes.alianca.br/
chmod +x deploy.sh
./deploy.sh
```

**OU manualmente:**

```bash
chmod 666 brindes.db data_log.csv
chmod 644 *.php .htaccess
```

## 3️⃣ ABRIR O SITE

```
http://brindes.alianca.ind.br
```

**Verificar:**
```
http://brindes.alianca.ind.br/verify.php
```

**RH (login):**
- Usuário: `rhadmin`
- Senha: `rhadmin1927`

---

## ✅ PRONTO!

**Dúvidas?** Veja: [COMO_ABRIR.md](COMO_ABRIR.md)

**Problemas?** Veja: [INSTALACAO.md](INSTALACAO.md)

---

**Sistema de Brindes - Aliança Industrial**
