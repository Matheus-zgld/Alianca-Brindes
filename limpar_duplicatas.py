#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para remover duplicatas no banco de dados
Mantém o registro com mais informações (CPF preenchido)
"""

import sqlite3

DB_PATH = 'brindes.db'

def limpar_duplicatas():
    """Remove funcionários duplicados, mantendo o mais completo"""
    
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    print("=" * 60)
    print("LIMPEZA DE DUPLICATAS")
    print("=" * 60)
    print()
    
    # Busca todas as matrículas
    cursor.execute("SELECT matricula, nome_completo, cpf, brinde_status FROM funcionarios ORDER BY matricula")
    funcionarios = cursor.fetchall()
    
    # Agrupa por nome (já que algumas matrículas podem variar apenas no zero à esquerda)
    by_nome = {}
    for mat, nome, cpf, status in funcionarios:
        nome_norm = nome.upper().strip()
        if nome_norm not in by_nome:
            by_nome[nome_norm] = []
        by_nome[nome_norm].append((mat, nome, cpf, status))
    
    # Identifica duplicatas
    duplicatas_removidas = 0
    for nome_norm, registros in by_nome.items():
        if len(registros) > 1:
            # Ordena: prioriza quem tem CPF, depois status do brinde
            registros_ordenados = sorted(registros, key=lambda x: (
                1 if x[2] else 0,  # tem CPF
                x[3],  # status brinde
                len(x[0])  # tamanho da matrícula (prefere com zeros)
            ), reverse=True)
            
            # Mantém o primeiro (melhor), remove os outros
            manter = registros_ordenados[0]
            remover = registros_ordenados[1:]
            
            if len(remover) > 0:
                print(f"Duplicata encontrada: {nome_norm}")
                print(f"  MANTENDO: Matrícula {manter[0]} | CPF: {manter[2] or 'Sem CPF'} | Status: {manter[3]}")
                
                for r in remover:
                    print(f"  Removendo: Matrícula {r[0]} | CPF: {r[2] or 'Sem CPF'} | Status: {r[3]}")
                    cursor.execute("DELETE FROM funcionarios WHERE matricula = ?", (r[0],))
                    duplicatas_removidas += 1
                
                print()
    
    # Commit
    conn.commit()
    
    # Verifica total após limpeza
    cursor.execute("SELECT COUNT(*) FROM funcionarios")
    total_final = cursor.fetchone()[0]
    
    print("=" * 60)
    print("RESUMO DA LIMPEZA")
    print("=" * 60)
    print(f"✓ Registros duplicados removidos: {duplicatas_removidas}")
    print(f"✓ Total de funcionários após limpeza: {total_final}")
    print()
    
    conn.close()

if __name__ == '__main__':
    limpar_duplicatas()
