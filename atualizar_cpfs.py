#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para atualizar CPFs de funcionários no banco de dados
Útil para adicionar CPFs aos funcionários importados sem essa informação
"""

import sqlite3
import pandas as pd
import sys

DB_PATH = 'brindes.db'

def normalizar_cpf(cpf):
    """Remove caracteres não numéricos do CPF"""
    if pd.isna(cpf):
        return None
    cpf_str = str(cpf).strip()
    cpf_limpo = ''.join(filter(str.isdigit, cpf_str))
    if cpf_limpo:
        cpf_limpo = cpf_limpo.zfill(11)
    return cpf_limpo if cpf_limpo else None

def atualizar_cpfs_do_excel(arquivo_excel):
    """Atualiza CPFs dos funcionários usando dados de um arquivo Excel"""
    
    print("=" * 60)
    print("ATUALIZAÇÃO DE CPFs")
    print("=" * 60)
    print()
    
    # Lê o arquivo Excel
    print(f"Lendo arquivo: {arquivo_excel}")
    try:
        df = pd.read_excel(arquivo_excel)
    except Exception as e:
        print(f"ERRO ao ler Excel: {e}")
        return False
    
    print(f"Linhas encontradas: {len(df)}")
    print(f"Colunas: {list(df.columns)}")
    print()
    
    # Conecta ao banco
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    atualizados = 0
    nao_encontrados = 0
    sem_cpf = 0
    
    for idx, row in df.iterrows():
        try:
            nome = str(row.get('NOME', row.get('Nome', ''))).strip()
            cpf = normalizar_cpf(row.get('CPF', row.get('Cpf', '')))
            matricula = str(row.get('MATRICULA', row.get('Matricula', row.get('MATRÍCULA', '')))).strip()
            
            # Pula linhas sem dados essenciais
            if not nome or nome == 'nan':
                continue
            
            # Se não tem CPF, pula
            if not cpf:
                sem_cpf += 1
                continue
            
            # Busca funcionário pela matrícula
            cursor.execute("""
                SELECT cpf FROM funcionarios WHERE matricula = ?
            """, (matricula,))
            
            resultado = cursor.fetchone()
            
            if resultado:
                cpf_atual = resultado[0]
                
                # Atualiza se não tem CPF ou se o CPF é diferente
                if not cpf_atual or cpf_atual != cpf:
                    cursor.execute("""
                        UPDATE funcionarios 
                        SET cpf = ?
                        WHERE matricula = ?
                    """, (cpf, matricula))
                    
                    atualizados += 1
                    print(f"✓ {matricula} | {nome:40s} | CPF atualizado: {cpf}")
            else:
                nao_encontrados += 1
                print(f"✗ {matricula} | {nome:40s} | Não encontrado no banco")
        
        except Exception as e:
            print(f"ERRO na linha {idx}: {e}")
    
    # Commit
    conn.commit()
    
    print()
    print("=" * 60)
    print("RESUMO DA ATUALIZAÇÃO")
    print("=" * 60)
    print(f"CPFs atualizados: {atualizados}")
    print(f"Funcionários não encontrados: {nao_encontrados}")
    print(f"Linhas sem CPF no Excel: {sem_cpf}")
    
    # Estatísticas finais
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != ''")
    total_com_cpf = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total = cursor.fetchone()[0]
    
    print(f"\nTotal de funcionários com CPF: {total_com_cpf}/{total}")
    
    conn.close()
    return True

def atualizar_cpf_individual(matricula, cpf):
    """Atualiza o CPF de um funcionário específico"""
    
    cpf_normalizado = normalizar_cpf(cpf)
    
    if not cpf_normalizado:
        print("CPF inválido!")
        return False
    
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Verifica se o funcionário existe
    cursor.execute("SELECT nome_completo, cpf FROM funcionarios WHERE matricula = ?", (matricula,))
    resultado = cursor.fetchone()
    
    if not resultado:
        print(f"Funcionário com matrícula {matricula} não encontrado!")
        conn.close()
        return False
    
    nome, cpf_atual = resultado
    
    # Atualiza
    cursor.execute("UPDATE funcionarios SET cpf = ? WHERE matricula = ?", (cpf_normalizado, matricula))
    conn.commit()
    
    print(f"✓ CPF atualizado!")
    print(f"  Matrícula: {matricula}")
    print(f"  Nome: {nome}")
    print(f"  CPF anterior: {cpf_atual or 'Sem CPF'}")
    print(f"  CPF novo: {cpf_normalizado}")
    
    conn.close()
    return True

def main():
    if len(sys.argv) < 2:
        print("USO:")
        print("  python atualizar_cpfs.py <arquivo.xls>")
        print("  python atualizar_cpfs.py <matricula> <cpf>")
        print()
        print("EXEMPLOS:")
        print("  python atualizar_cpfs.py funcionarios_com_cpf.xls")
        print("  python atualizar_cpfs.py 18943 12345678901")
        sys.exit(1)
    
    if len(sys.argv) == 2:
        # Modo: arquivo Excel
        arquivo = sys.argv[1]
        atualizar_cpfs_do_excel(arquivo)
    else:
        # Modo: atualização individual
        matricula = sys.argv[1]
        cpf = sys.argv[2]
        atualizar_cpf_individual(matricula, cpf)

if __name__ == '__main__':
    main()
