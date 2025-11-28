#!/bin/bash
# Script de Verificação Pós-Instalação
# Execute este script após fazer upload dos arquivos para verificar se tudo está correto

echo "======================================"
echo "🔍 Verificação do Sistema de Brindes"
echo "======================================"
echo ""

# Verifica arquivos principais
echo "📁 Verificando arquivos principais..."
files_ok=true

required_files=(
    "config.php"
    "index.php"
    "rh.php"
    "rh_login.php"
    "rh_logout.php"
    "rh_funcionarios.php"
    "rh_logs.php"
    "dar_baixa.php"
    "brindes.db"
    "data_log.csv"
    ".htaccess"
    "inc/functions.php"
    "templates/base.php"
    "templates/funcionario_home.php"
    "templates/rh_home.php"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✅ $file"
    else
        echo "   ❌ $file - FALTANDO!"
        files_ok=false
    fi
done

echo ""

# Verifica permissões
echo "🔐 Verificando permissões..."
perms_ok=true

if [ -f "brindes.db" ]; then
    perm=$(stat -c "%a" brindes.db 2>/dev/null || stat -f "%Lp" brindes.db 2>/dev/null)
    if [ "$perm" = "666" ]; then
        echo "   ✅ brindes.db ($perm)"
    else
        echo "   ⚠️  brindes.db ($perm) - Recomendado: 666"
        perms_ok=false
    fi
fi

if [ -f "data_log.csv" ]; then
    perm=$(stat -c "%a" data_log.csv 2>/dev/null || stat -f "%Lp" data_log.csv 2>/dev/null)
    if [ "$perm" = "666" ]; then
        echo "   ✅ data_log.csv ($perm)"
    else
        echo "   ⚠️  data_log.csv ($perm) - Recomendado: 666"
        perms_ok=false
    fi
fi

echo ""

# Verifica se SQLite está funcionando
echo "🗄️  Verificando banco de dados..."
if command -v sqlite3 &> /dev/null; then
    if [ -f "brindes.db" ]; then
        count=$(sqlite3 brindes.db "SELECT COUNT(*) FROM funcionarios;" 2>/dev/null)
        if [ $? -eq 0 ]; then
            echo "   ✅ Banco de dados acessível ($count funcionários cadastrados)"
        else
            echo "   ❌ Erro ao acessar banco de dados"
        fi
    fi
else
    echo "   ⚠️  sqlite3 não disponível para teste"
fi

echo ""

# Verifica PHP
echo "🐘 Verificando PHP..."
if command -v php &> /dev/null; then
    php_version=$(php -v | head -n 1)
    echo "   ✅ $php_version"
    
    # Testa config.php
    php -l config.php > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "   ✅ config.php válido"
    else
        echo "   ❌ config.php tem erros de sintaxe"
    fi
else
    echo "   ❌ PHP não encontrado"
fi

echo ""

# Resumo final
echo "======================================"
echo "📊 RESUMO"
echo "======================================"

if $files_ok && $perms_ok; then
    echo "✅ Sistema verificado com sucesso!"
    echo ""
    echo "Próximos passos:"
    echo "1. Acesse http://brindes.alianca.ind.br"
    echo "2. Teste a geração de QR Code"
    echo "3. Teste o login RH"
    echo "4. Verifique segurança dos arquivos sensíveis"
else
    echo "⚠️  Alguns problemas foram encontrados"
    echo ""
    echo "Corrija os problemas acima antes de usar o sistema"
fi

echo ""
