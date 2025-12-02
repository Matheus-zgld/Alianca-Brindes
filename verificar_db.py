#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para verificar a integridade do banco de dados de funcionários
"""

import sqlite3

DB_PATH = 'brindes.db'

def verificar_banco():
    """Verifica a integridade e estatísticas do banco de dados"""
    
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    print("=" * 60)
    print("VERIFICAÇÃO DO BANCO DE DADOS")
    print("=" * 60)
    print()
    
    # Total de funcionários
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total = cursor.fetchone()[0]
    print(f"✓ Total de funcionários: {total}")
    
    # Funcionários com CPF
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != ''")
    com_cpf = cursor.fetchone()[0]
    print(f"✓ Funcionários com CPF: {com_cpf}")
    
    # Funcionários sem CPF
    sem_cpf = total - com_cpf
    print(f"✓ Funcionários sem CPF: {sem_cpf}")
    
    # Funcionários com matrícula
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE matricula IS NOT NULL AND matricula != ''")
    com_matricula = cursor.fetchone()[0]
    print(f"✓ Funcionários com matrícula: {com_matricula}")
    
    # Status de brindes
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 1")
    com_brinde = cursor.fetchone()[0]
    print(f"✓ Funcionários que já receberam brinde: {com_brinde}")
    
    cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 0 OR brinde_status IS NULL")
    sem_brinde = cursor.fetchone()[0]
    print(f"✓ Funcionários que ainda não receberam brinde: {sem_brinde}")
    
    print()
    print("=" * 60)
    print("AMOSTRA DE FUNCIONÁRIOS (Primeiros 10)")
    print("=" * 60)
    print()
    
    cursor.execute("""
        SELECT matricula, nome_completo, cpf, brinde_status 
        FROM funcionarios 
        ORDER BY matricula 
        LIMIT 10
    """)
    
    for mat, nome, cpf, status in cursor.fetchall():
        status_txt = "✓ Recebeu" if status == 1 else "○ Pendente"
        cpf_txt = cpf if cpf else "Sem CPF"
        print(f"{mat:6s} | {nome:40s} | {cpf_txt:15s} | {status_txt}")
    
    print()
    print("=" * 60)
    print("ÚLTIMOS 10 FUNCIONÁRIOS ADICIONADOS")
    print("=" * 60)
    print()
    
    cursor.execute("""
        SELECT matricula, nome_completo, cpf, brinde_status 
        FROM funcionarios 
        ORDER BY CAST(matricula AS INTEGER) DESC 
        LIMIT 10
    """)
    
    for mat, nome, cpf, status in cursor.fetchall():
        status_txt = "✓ Recebeu" if status == 1 else "○ Pendente"
        cpf_txt = cpf if cpf else "Sem CPF"
        print(f"{mat:6s} | {nome:40s} | {cpf_txt:15s} | {status_txt}")
    
    conn.close()
    
    print()
    print("=" * 60)
    print("✓ Verificação concluída!")
    print("=" * 60)

if __name__ == '__main__':
    verificar_banco()
