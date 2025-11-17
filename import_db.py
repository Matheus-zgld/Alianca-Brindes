# import_db.py
import pandas as pd
import sqlite3
import os

# --- Configurações ---
DB_NAME = 'brindes.db'
# ATENÇÃO: Confirme que seu arquivo Excel antigo (.xls) tem exatamente este nome
EXCEL_FILE = 'funcionarios_brindes.xls' 
TABLE_NAME = 'funcionarios'

# Mapeamento de Colunas:
# CHAVE: O nome exato da coluna no seu arquivo Excel (.xls)
# VALOR: O nome da coluna correspondente na tabela 'funcionarios' do SQLite
COLUMNS_MAPPING = {
    'Matricula': 'matricula',
    'Centro Custo': 'centro_custo',
    'Nome': 'nome_completo',
    'C.P.F.': 'cpf',
}


def import_excel_to_sqlite():
    """
    Lê o arquivo Excel (.xls) e insere os dados na tabela 'funcionarios' do SQLite.
    """
    if not os.path.exists(EXCEL_FILE):
        print(f"❌ ERRO: Arquivo Excel '{EXCEL_FILE}' não encontrado no diretório.")
        print("Certifique-se de que o arquivo está na mesma pasta e tem a extensão .xls")
        return

    try:
        # 1. Leitura do arquivo Excel (.xls) usando pandas (que utilizará xlrd)
        df = pd.read_excel(EXCEL_FILE)
        print(f"✅ Arquivo '{EXCEL_FILE}' lido com sucesso. Total de registros: {len(df)}")
        
        # 2. Renomear colunas do DataFrame para corresponder ao DB
        # O 'errors="raise"' garante que se um cabeçalho esperado não for encontrado, ele avisa.
        df.rename(columns=COLUMNS_MAPPING, inplace=True, errors="raise")
        
        # 3. Preparar colunas do DB que não estão no Excel
        # Definindo o status inicial como PENDENTE (0)
        df['brinde_status'] = 0
        df['data_resgate'] = None
        
        # 4. Conectar ao SQLite
        conn = sqlite3.connect(DB_NAME)
        
        # 5. Inserir dados no DB
        # if_exists='append' adiciona novas linhas. Se quiser apagar o que existe e recarregar, use 'replace'.
        df.to_sql(TABLE_NAME, conn, if_exists='append', index=False)
        
        # Verifica se a tabela foi populada
        count = conn.execute(f"SELECT COUNT(*) FROM {TABLE_NAME}").fetchone()[0]
        
        conn.close()
        print(f"🎉 SUCESSO! {count} registros estão agora no banco de dados '{DB_NAME}'.")

    except KeyError as e:
        print(f"❌ ERRO: O cabeçalho da coluna {e} não foi encontrado no seu arquivo Excel.")
        print("Ajuste o dicionário 'COLUMNS_MAPPING' no script para refletir os nomes exatos do seu Excel.")
    except Exception as e:
        print(f"❌ Ocorreu um erro inesperado durante a importação: {e}")

if __name__ == '__main__':
    import_excel_to_sqlite()