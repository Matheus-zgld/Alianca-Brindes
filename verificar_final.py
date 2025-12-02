import sqlite3

conn = sqlite3.connect('brindes.db')
cursor = conn.cursor()

print("=" * 70)
print("VERIFICAÇÃO FINAL DO BANCO")
print("=" * 70)
print()

# Total
cursor.execute("SELECT COUNT(*) FROM funcionarios")
total = cursor.fetchone()[0]
print(f"Total de funcionarios: {total}")

# Com CPF
cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != ''")
com_cpf = cursor.fetchone()[0]
print(f"Com CPF: {com_cpf}")

# Que receberam brinde
cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 1")
com_brinde = cursor.fetchone()[0]
print(f"Que ja receberam brinde: {com_brinde}")

# Verifica Daisy
print("\nVerificando DAISY:")
cursor.execute("SELECT matricula, nome_completo, brinde_status FROM funcionarios WHERE nome_completo LIKE '%DAISY%'")
rows = cursor.fetchall()
for r in rows:
    status = "Recebeu" if r[2] == 1 else "Pendente"
    print(f"  {r[0]} | {r[1]} | {status}")

# Verifica matrículas sem zero
print("\nVerificando matriculas:")
cursor.execute("SELECT matricula FROM funcionarios WHERE matricula NOT LIKE '0%' LIMIT 5")
sem_zero = cursor.fetchall()
if sem_zero:
    print(f"  PROBLEMA: {len(sem_zero)} matriculas sem zero!")
    for r in sem_zero:
        print(f"    {r[0]}")
else:
    print("  OK: Todas as matriculas começam com 0")

# Centro de custo
cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE centro_custo = 0")
sem_cc = cursor.fetchone()[0]
cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE centro_custo > 0")
com_cc = cursor.fetchone()[0]
print(f"\nCentro de custo:")
print(f"  Com CC: {com_cc}")
print(f"  Sem CC (0): {sem_cc}")

print("\n" + "=" * 70)

conn.close()
