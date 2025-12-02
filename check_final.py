import sqlite3

conn = sqlite3.connect('brindes.db')
cursor = conn.cursor()

cursor.execute('SELECT COUNT(*) FROM funcionarios')
total = cursor.fetchone()[0]

cursor.execute("SELECT COUNT(*) FROM funcionarios WHERE cpf IS NOT NULL AND cpf != ''")
com_cpf = cursor.fetchone()[0]

print('=' * 60)
print('BANCO DE DADOS FINAL')
print('=' * 60)
print(f'Total de funcionarios: {total}')
print(f'Funcionarios com CPF: {com_cpf}')
print(f'Funcionarios sem CPF: {total - com_cpf}')
print('=' * 60)

conn.close()
