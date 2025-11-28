#!/bin/bash

# ============================================
# Script de Deploy - Sistema de Brindes Aliança
# ============================================
# 
# Este script automatiza a instalação do sistema no servidor
# 
# USO:
#   chmod +x deploy.sh
#   ./deploy.sh
# 
# ============================================

echo "================================================"
echo "  🎁 Deploy - Sistema de Brindes Aliança"
echo "================================================"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Diretório de instalação
INSTALL_DIR="/ssd/aliancaind/public_html/brindes.alianca.br"

# Função para imprimir mensagens
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERRO]${NC} $1"
}

# Verificar se está rodando com privilégios necessários
if [ ! -w "$INSTALL_DIR" ] 2>/dev/null; then
    print_warning "Você pode precisar de privilégios sudo para algumas operações"
fi

echo ""
print_status "Verificando estrutura de diretórios..."

# Criar diretório se não existir
if [ ! -d "$INSTALL_DIR" ]; then
    print_warning "Diretório $INSTALL_DIR não existe. Criando..."
    mkdir -p "$INSTALL_DIR" || {
        print_error "Falha ao criar diretório. Execute: sudo mkdir -p $INSTALL_DIR"
        exit 1
    }
    print_success "Diretório criado"
else
    print_success "Diretório já existe"
fi

echo ""
print_status "Copiando arquivos para $INSTALL_DIR..."

# Copiar arquivos (assumindo que o script está na raiz do projeto)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ "$SCRIPT_DIR" = "$INSTALL_DIR" ]; then
    print_success "Já estamos no diretório de destino"
else
    # Copiar todos os arquivos exceto o próprio script e arquivos desnecessários
    rsync -av --exclude='deploy.sh' --exclude='.git' --exclude='*.bak' \
          --exclude='old_python_backup' \
          "$SCRIPT_DIR/" "$INSTALL_DIR/" || {
        print_error "Falha ao copiar arquivos"
        exit 1
    }
    print_success "Arquivos copiados"
fi

echo ""
print_status "Configurando permissões..."

cd "$INSTALL_DIR" || exit 1

# Permissões para diretórios
find . -type d -exec chmod 755 {} \; 2>/dev/null
print_success "Permissões de diretórios configuradas (755)"

# Permissões para arquivos PHP/HTML
find . -type f -name "*.php" -exec chmod 644 {} \; 2>/dev/null
find . -type f -name "*.html" -exec chmod 644 {} \; 2>/dev/null
find . -type f -name ".htaccess" -exec chmod 644 {} \; 2>/dev/null
print_success "Permissões de arquivos PHP/HTML configuradas (644)"

# Permissões especiais para arquivos que precisam ser escritos
if [ -f "brindes.db" ]; then
    chmod 666 brindes.db
    print_success "Permissão do banco de dados configurada (666)"
else
    print_warning "Arquivo brindes.db não encontrado"
fi

if [ -f "data_log.csv" ]; then
    chmod 666 data_log.csv
    print_success "Permissão do arquivo de log configurada (666)"
else
    print_warning "Arquivo data_log.csv não encontrado"
fi

# Backups do banco
chmod 666 brindes.db.bak* 2>/dev/null
if [ $? -eq 0 ]; then
    print_success "Permissões dos backups do banco configuradas"
fi

# Tentar ajustar proprietário (pode precisar de sudo)
if [ -n "$(command -v apache2)" ]; then
    WEB_USER="www-data"
elif [ -n "$(command -v httpd)" ]; then
    WEB_USER="apache"
else
    WEB_USER="www-data"
fi

print_status "Tentando ajustar proprietário para $WEB_USER..."
chown -R $WEB_USER:$WEB_USER . 2>/dev/null && print_success "Proprietário ajustado" || print_warning "Não foi possível ajustar proprietário (pode precisar de sudo)"

echo ""
print_status "Verificando extensões PHP..."

# Verificar extensões necessárias
PHP_BIN=$(command -v php)
if [ -z "$PHP_BIN" ]; then
    print_error "PHP não encontrado no PATH"
    exit 1
fi

print_success "PHP encontrado: $($PHP_BIN --version | head -n 1)"

# Verificar extensões
check_extension() {
    if $PHP_BIN -m | grep -q "^$1\$"; then
        print_success "Extensão $1 instalada"
        return 0
    else
        print_warning "Extensão $1 NÃO instalada"
        return 1
    fi
}

check_extension "pdo_sqlite"
check_extension "mbstring"
check_extension "json"
check_extension "curl"

echo ""
print_status "Verificando Apache..."

# Verificar se Apache está rodando
if systemctl is-active --quiet apache2 2>/dev/null; then
    print_success "Apache está rodando"
elif systemctl is-active --quiet httpd 2>/dev/null; then
    print_success "Apache (httpd) está rodando"
else
    print_warning "Apache pode não estar rodando"
fi

# Verificar mod_rewrite
if apache2ctl -M 2>/dev/null | grep -q "rewrite_module"; then
    print_success "mod_rewrite está habilitado"
elif httpd -M 2>/dev/null | grep -q "rewrite_module"; then
    print_success "mod_rewrite está habilitado"
else
    print_warning "mod_rewrite pode não estar habilitado"
fi

echo ""
print_status "Testando conexão com banco de dados..."

# Teste simples de conexão
$PHP_BIN -r "
try {
    \$pdo = new PDO('sqlite:$INSTALL_DIR/brindes.db');
    \$stmt = \$pdo->query('SELECT COUNT(*) FROM funcionarios');
    \$count = \$stmt->fetchColumn();
    echo \"✓ Conexão OK - Funcionários cadastrados: \$count\n\";
    exit(0);
} catch(Exception \$e) {
    echo \"✗ Erro: \" . \$e->getMessage() . \"\\n\";
    exit(1);
}
" && print_success "Banco de dados acessível" || print_error "Problema ao acessar banco de dados"

echo ""
echo "================================================"
echo "  ✅ Deploy concluído!"
echo "================================================"
echo ""
echo "📋 Próximos passos:"
echo ""
echo "1. Acesse: http://brindes.alianca.ind.br/verify.php"
echo "   Para verificar se tudo está funcionando"
echo ""
echo "2. Teste a página principal:"
echo "   http://brindes.alianca.ind.br/"
echo ""
echo "3. Teste a área do RH:"
echo "   http://brindes.alianca.ind.br/rh.php"
echo "   Usuário: rhadmin | Senha: rhadmin1927"
echo ""
echo "4. IMPORTANTE: Exclua o arquivo verify.php após testar!"
echo "   rm $INSTALL_DIR/verify.php"
echo ""
echo "================================================"
echo ""

# Oferecer para abrir o verify.php no navegador (se estiver em ambiente gráfico)
if [ -n "$DISPLAY" ]; then
    read -p "Deseja abrir verify.php no navegador agora? (s/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[SsYy]$ ]]; then
        xdg-open "http://brindes.alianca.ind.br/verify.php" 2>/dev/null || \
        open "http://brindes.alianca.ind.br/verify.php" 2>/dev/null || \
        print_warning "Não foi possível abrir o navegador automaticamente"
    fi
fi

echo ""
print_success "Script de deploy finalizado!"
echo ""

exit 0
