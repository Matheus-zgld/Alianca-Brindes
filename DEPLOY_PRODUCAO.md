# 🚀 Instruções de Deploy para Produção

## ✅ Status Atual
- **636 funcionários** importados com sucesso
- **Todos com CPF** cadastrado corretamente
- Banco de dados validado e testado

---

## 📦 Arquivo para Upload

**Arquivo a ser enviado para produção:**

```
brindes.db.PRODUCAO_20251202
```

**Tamanho:** ~88 KB  
**Data de criação:** 02/12/2025

---

## 🔧 Procedimento de Deploy

### Passo 1: Fazer Backup do Banco Atual em Produção

**IMPORTANTE:** Antes de fazer qualquer alteração, faça backup do banco atual!

```bash
# No servidor de produção, execute:
cp brindes.db brindes.db.backup_antes_update_$(date +%Y%m%d_%H%M%S)
```

### Passo 2: Fazer Upload do Novo Banco

1. **Renomeie o arquivo local:**
   - De: `brindes.db.PRODUCAO_20251202`
   - Para: `brindes.db`

2. **Faça upload via FTP/SFTP/SCP para o servidor**
   
   Exemplo via SCP:
   ```bash
   scp brindes.db usuario@brindes.alianca.ind.br:/caminho/completo/para/aplicacao/
   ```

3. **Ou, se tiver acesso SSH ao servidor:**
   ```bash
   # Upload do arquivo
   scp brindes.db.PRODUCAO_20251202 usuario@servidor:/tmp/
   
   # No servidor
   ssh usuario@servidor
   cd /caminho/completo/para/aplicacao/
   cp brindes.db brindes.db.backup_$(date +%Y%m%d_%H%M%S)
   mv /tmp/brindes.db.PRODUCAO_20251202 brindes.db
   chmod 666 brindes.db
   chown www-data:www-data brindes.db  # Ajuste conforme seu servidor
   ```

### Passo 3: Verificar Permissões

O arquivo `brindes.db` precisa ter permissões de leitura/escrita para o servidor web:

```bash
chmod 666 brindes.db
# OU
chmod 664 brindes.db
chown www-data:www-data brindes.db
```

### Passo 4: Testar o Sistema

1. Acesse: http://brindes.alianca.ind.br/
2. Teste com uma matrícula conhecida (exemplo: 13317)
3. Verifique se o CPF aparece corretamente
4. Acesse a área do RH e verifique a listagem de funcionários

---

## 🔍 Verificação Rápida no Servidor

Após o deploy, execute estes comandos para verificar:

```bash
# Ver total de funcionários
sqlite3 brindes.db "SELECT COUNT(*) FROM funcionarios;"
# Deve retornar: 636

# Ver funcionários com CPF
sqlite3 brindes.db "SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != '';"
# Deve retornar: 636

# Ver primeiros 5 funcionários
sqlite3 brindes.db "SELECT matricula, nome_completo, cpf FROM funcionarios LIMIT 5;"
```

---

## 📋 Checklist de Deploy

- [ ] Backup do banco atual feito
- [ ] Arquivo `brindes.db.PRODUCAO_20251202` renomeado para `brindes.db`
- [ ] Upload do arquivo realizado
- [ ] Permissões ajustadas (666 ou 664)
- [ ] Sistema testado com matrícula de teste
- [ ] Área do RH acessível e funcionando
- [ ] Lista de funcionários exibindo 636 registros

---

## 🆘 Rollback (Em caso de problema)

Se algo der errado, restaure o backup:

```bash
# No servidor
cd /caminho/completo/para/aplicacao/
cp brindes.db.backup_antes_update_YYYYMMDD_HHMMSS brindes.db
chmod 666 brindes.db
```

---

## 📊 Diferenças da Versão Anterior

| Item | Antes | Agora |
|------|-------|-------|
| Total de Funcionários | 625 | **636** |
| Funcionários com CPF | 625 | **636** |
| Funcionários sem CPF | 0 | **0** |
| Status | Produção antiga | **Nova versão atualizada** |

---

## 📞 Suporte

- Todos os 636 funcionários da planilha foram importados corretamente
- Todos têm CPF cadastrado
- Nenhum registro duplicado
- Sistema testado e validado localmente

---

## ✨ Resumo

**Arquivo para produção:** `brindes.db.PRODUCAO_20251202` → renomear para `brindes.db`

**Caminho no servidor:** Mesmo local onde está o `brindes.db` atual (geralmente no diretório raiz da aplicação)

**Após upload:** Verificar permissões e testar o sistema

---

*Documento criado em: 02/12/2025*
*Versão do banco: PRODUCAO_20251202*
