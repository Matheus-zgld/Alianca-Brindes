#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para importar funcionários do Excel para o banco de dados SQLite
"""

import sqlite3
import pandas as pd
import os
import sys

# Configurações
DB_PATH = 'brindes.db'
EXCEL_FILE = 'funcionarios.xls'

def normalizar_cpf(cpf):
    """Remove caracteres não numéricos do CPF"""
    if pd.isna(cpf):
        return None
    cpf_str = str(cpf).strip()
    # Remove pontos, traços e espaços
    cpf_limpo = ''.join(filter(str.isdigit, cpf_str))
    # Preenche com zeros à esquerda se necessário
    if cpf_limpo:
        cpf_limpo = cpf_limpo.zfill(11)
    return cpf_limpo if cpf_limpo else None

def verificar_estrutura_db():
    """Verifica e exibe a estrutura do banco de dados"""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    print("=== Estrutura do Banco de Dados ===\n")
    
    # Lista todas as tabelas
    cursor.execute("SELECT name FROM sqlite_master WHERE type='table'")
    tabelas = cursor.fetchall()
    
    for (tabela,) in tabelas:
        print(f"Tabela: {tabela}")
        cursor.execute(f"PRAGMA table_info({tabela})")
        colunas = cursor.fetchall()
        for coluna in colunas:
            print(f"  - {coluna[1]} ({coluna[2]})")
        
        # Conta registros
        cursor.execute(f"SELECT COUNT(*) FROM {tabela}")
        count = cursor.fetchone()[0]
        print(f"  Total de registros: {count}\n")
    
    conn.close()

def importar_funcionarios():
    """Importa funcionários do Excel para o banco de dados"""
    
    # Verifica se os arquivos existem
    if not os.path.exists(EXCEL_FILE):
        print(f"ERRO: Arquivo {EXCEL_FILE} não encontrado!")
        return False
    
    if not os.path.exists(DB_PATH):
        print(f"ERRO: Banco de dados {DB_PATH} não encontrado!")
        return False
    
    print(f"Lendo arquivo Excel: {EXCEL_FILE}")
    
    # Lê o arquivo Excel
    try:
        df = pd.read_excel(EXCEL_FILE)
    except Exception as e:
        print(f"ERRO ao ler Excel: {e}")
        return False
    
    print(f"Total de linhas no Excel: {len(df)}")
    print(f"Colunas encontradas: {list(df.columns)}\n")
    
    # Exibe as primeiras linhas
    print("=== Primeiras linhas do Excel ===")
    print(df.head())
    print()
    
    # Conecta ao banco de dados
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Verifica estrutura da tabela
    cursor.execute("PRAGMA table_info(funcionarios)")
    colunas_db = [col[1] for col in cursor.fetchall()]
    print(f"Colunas no banco de dados: {colunas_db}\n")
    
    # Contador de ações
    novos = 0
    atualizados = 0
    erros = 0
    
    # Processa cada funcionário
    for idx, row in df.iterrows():
        try:
            # Extrai dados (ajuste os nomes das colunas conforme necessário)
            nome = str(row.get('NOME', row.get('Nome', ''))).strip()
            cpf = normalizar_cpf(row.get('C.P.F.', row.get('CPF', row.get('Cpf', ''))))
            matricula = str(row.get('MATRICULA', row.get('Matricula', row.get('MATRÍCULA', '')))).strip()
            
            # Pula linhas vazias
            if not nome or nome == 'nan':
                continue
            
            print(f"Processando: {nome} | CPF: {cpf} | Matrícula: {matricula}")
            
            # Verifica se já existe (por CPF ou matrícula)
            cursor.execute("""
                SELECT matricula, nome_completo, cpf, brinde_status FROM funcionarios 
                WHERE cpf = ? OR matricula = ?
            """, (cpf, matricula))
            
            existente = cursor.fetchone()
            
            if existente:
                # Atualiza registro existente
                mat_existente, nome_existente, cpf_existente, brinde_status = existente
                
                print(f"  -> Funcionário já existe (Matrícula: {mat_existente})")
                
                # Atualiza com os dados mais completos
                novo_cpf = cpf if cpf else cpf_existente
                nova_matricula = matricula if matricula else mat_existente
                
                cursor.execute("""
                    UPDATE funcionarios 
                    SET nome_completo = ?, cpf = ?
                    WHERE matricula = ?
                """, (nome, novo_cpf, mat_existente))
                
                atualizados += 1
                print(f"  -> Atualizado")
            else:
                # Insere novo funcionário
                cursor.execute("""
                    INSERT INTO funcionarios (matricula, nome_completo, cpf, brinde_status, centro_custo)
                    VALUES (?, ?, ?, 0, 0)
                """, (matricula, nome, cpf))
                
                novos += 1
                print(f"  -> Novo funcionário adicionado (Matrícula: {matricula})")
            
        except Exception as e:
            erros += 1
            print(f"  -> ERRO ao processar linha {idx}: {e}")
    
    # Commit das alterações
    conn.commit()
    
    # Exibe resumo
    print("\n=== RESUMO DA IMPORTAÇÃO ===")
    print(f"Funcionários novos adicionados: {novos}")
    print(f"Funcionários atualizados: {atualizados}")
    print(f"Erros: {erros}")
    
    # Exibe total final
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total = cursor.fetchone()[0]
    print(f"Total de funcionários no banco: {total}")
    
    conn.close()
    
    return True

def main():
    print("=== IMPORTAÇÃO DE FUNCIONÁRIOS ===\n")
    
    # Verifica estrutura do banco
    verificar_estrutura_db()
    
    # Importa funcionários
    sucesso = importar_funcionarios()
    
    if sucesso:
        print("\n✓ Importação concluída com sucesso!")
    else:
        print("\n✗ Importação falhou!")
        sys.exit(1)

if __name__ == '__main__':
    main()
