import sqlite3
import os

DB_PATH = 'brindes.db'
SQL_OUTPUT = 'brindes_export.sql'

def convert_sqlite_to_sql():
    """Converte SQLite para arquivo SQL compatível com MySQL/PHPMyAdmin"""
    
    conn = sqlite3.connect(DB_PATH)
    cur = conn.cursor()
    
    # Obter todas as tabelas
    cur.execute("SELECT name FROM sqlite_master WHERE type='table';")
    tables = cur.fetchall()
    
    sql_content = []
    sql_content.append("-- Arquivo SQL exportado do SQLite (brindes.db)\n")
    sql_content.append("-- Compatível com MySQL/PHPMyAdmin\n\n")
    
    for table in tables:
        table_name = table[0]
        
        # Pular tabelas do sistema
        if table_name.startswith('sqlite_'):
            continue
        
        sql_content.append(f"\n-- Tabela: {table_name}\n")
        
        # Obter CREATE TABLE
        cur.execute(f"SELECT sql FROM sqlite_master WHERE type='table' AND name='{table_name}';")
        create_sql = cur.fetchone()[0]
        
        # Converter sintaxe SQLite para MySQL
        create_sql = convert_create_table_to_mysql(create_sql)
        sql_content.append(f"DROP TABLE IF EXISTS `{table_name}`;\n")
        sql_content.append(f"{create_sql};\n\n")
        
        # Obter dados
        cur.execute(f"SELECT * FROM {table_name};")
        rows = cur.fetchall()
        
        # Obter nomes das colunas
        cur.execute(f"PRAGMA table_info({table_name});")
        columns = [col[1] for col in cur.fetchall()]
        
        if rows:
            sql_content.append(f"-- Dados: {table_name}\n")
            for row in rows:
                col_names = ', '.join([f"`{col}`" for col in columns])
                values = ', '.join([format_value(val) for val in row])
                sql_content.append(f"INSERT INTO `{table_name}` ({col_names}) VALUES ({values});\n")
            sql_content.append("\n")
    
    conn.close()
    
    # Salvar arquivo SQL
    with open(SQL_OUTPUT, 'w', encoding='utf-8') as f:
        f.write(''.join(sql_content))
    
    print(f"✓ Arquivo SQL criado: {SQL_OUTPUT}")
    print(f"✓ Tamanho: {os.path.getsize(SQL_OUTPUT) / 1024:.2f} KB")
    print(f"✓ Pronto para importar no PHPMyAdmin!")

def convert_create_table_to_mysql(create_sql):
    """Converte CREATE TABLE do SQLite para MySQL"""
    
    # Remover AUTOINCREMENT do SQLite e substituir por AUTO_INCREMENT do MySQL
    create_sql = create_sql.replace('AUTOINCREMENT', 'AUTO_INCREMENT')
    
    # Converter tipos de dados
    create_sql = create_sql.replace('REAL', 'FLOAT')
    create_sql = create_sql.replace('BLOB', 'LONGBLOB')
    
    return create_sql

def format_value(val):
    """Formata valor para SQL"""
    if val is None:
        return 'NULL'
    elif isinstance(val, str):
        # Escapar aspas simples
        escaped = val.replace("'", "''")
        return f"'{escaped}'"
    elif isinstance(val, (int, float)):
        return str(val)
    else:
        return f"'{val}'"

if __name__ == '__main__':
    convert_sqlite_to_sql()
