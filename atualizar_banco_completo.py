#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para atualizar completamente o banco de dados
- Mantém funcionários antigos
- Adiciona novos do Excel
- Corrige matrículas (adiciona zero à esquerda)
- Atualiza centro de custo
"""

import sqlite3
import pandas as pd

DB_PATH = 'brindes.db'
EXCEL_FILE = 'funcionarios.xls'

def normalizar_cpf(cpf):
    """Remove caracteres não numéricos do CPF"""
    if pd.isna(cpf):
        return None
    cpf_str = str(cpf).strip()
    cpf_limpo = ''.join(filter(str.isdigit, cpf_str))
    if cpf_limpo:
        cpf_limpo = cpf_limpo.zfill(11)
    return cpf_limpo if cpf_limpo else None

def normalizar_matricula(matricula):
    """Adiciona zero à esquerda na matrícula se necessário"""
    if pd.isna(matricula):
        return None
    mat_str = str(int(float(matricula))).strip()
    # Matriculas devem ter 6 dígitos com zero à esquerda
    return mat_str.zfill(6)

def main():
    print("=" * 70)
    print("ATUALIZAÇÃO COMPLETA DO BANCO DE DADOS")
    print("=" * 70)
    print()
    
    # Conecta ao banco
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Mostra situação inicial
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total_inicial = cursor.fetchone()[0]
    print(f"Funcionarios no banco atual: {total_inicial}")
    
    # Lê o Excel
    print(f"\nLendo arquivo: {EXCEL_FILE}")
    df = pd.read_excel(EXCEL_FILE)
    print(f"Funcionarios no Excel: {len(df)}")
    print(f"Colunas: {list(df.columns)}")
    print()
    
    novos = 0
    atualizados = 0
    erros = 0
    
    print("Processando funcionários...")
    print("-" * 70)
    
    for idx, row in df.iterrows():
        try:
            # Extrai dados
            nome = str(row.get('Nome', '')).strip()
            cpf = normalizar_cpf(row.get('C.P.F.', ''))
            matricula = normalizar_matricula(row.get('Matricula', ''))
            centro_custo = int(row.get('Centro Custo', 0)) if not pd.isna(row.get('Centro Custo', 0)) else 0
            
            if not nome or nome == 'nan':
                continue
            
            if not matricula:
                print(f"AVISO: Linha {idx} sem matrícula - {nome}")
                continue
            
            # Verifica se existe
            cursor.execute("""
                SELECT matricula, cpf, centro_custo, brinde_status 
                FROM funcionarios 
                WHERE matricula = ?
            """, (matricula,))
            
            existente = cursor.fetchone()
            
            if existente:
                mat_exist, cpf_exist, cc_exist, status_exist = existente
                
                # Atualiza dados (mantém brinde_status)
                cursor.execute("""
                    UPDATE funcionarios 
                    SET nome_completo = ?, cpf = ?, centro_custo = ?
                    WHERE matricula = ?
                """, (nome, cpf or cpf_exist, centro_custo, matricula))
                
                atualizados += 1
                if (idx + 1) % 50 == 0:
                    print(f"Processados {idx + 1} registros...")
            else:
                # Insere novo
                cursor.execute("""
                    INSERT INTO funcionarios 
                    (matricula, nome_completo, cpf, centro_custo, brinde_status)
                    VALUES (?, ?, ?, ?, 0)
                """, (matricula, nome, cpf, centro_custo))
                
                novos += 1
                print(f"NOVO: {matricula} | {nome}")
        
        except Exception as e:
            erros += 1
            print(f"ERRO na linha {idx}: {e}")
    
    # Commit
    conn.commit()
    
    # Estatísticas finais
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total_final = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != ''")
    com_cpf = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 1")
    com_brinde = cursor.fetchone()[0]
    
    print()
    print("=" * 70)
    print("RESUMO")
    print("=" * 70)
    print(f"Funcionarios antes: {total_inicial}")
    print(f"Novos adicionados: {novos}")
    print(f"Atualizados: {atualizados}")
    print(f"Erros: {erros}")
    print(f"Total final: {total_final}")
    print(f"Com CPF: {com_cpf}")
    print(f"Que ja receberam brinde: {com_brinde}")
    print("=" * 70)
    
    # Mostra alguns exemplos
    print("\nExemplos de registros:")
    cursor.execute("""
        SELECT matricula, nome_completo, cpf, centro_custo, brinde_status 
        FROM funcionarios 
        ORDER BY matricula 
        LIMIT 5
    """)
    
    for mat, nome, cpf, cc, status in cursor.fetchall():
        cpf_txt = cpf if cpf else "Sem CPF"
        status_txt = "Recebeu" if status == 1 else "Pendente"
        print(f"{mat} | {nome[:35]:35s} | CPF: {cpf_txt:15s} | CC: {cc:4d} | {status_txt}")
    
    conn.close()
    print("\nConcluido!")

if __name__ == '__main__':
    main()
