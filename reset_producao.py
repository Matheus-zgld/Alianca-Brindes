import sqlite3
import csv

DB_PATH = 'brindes.db'
LOG_PATH = 'data_log.csv'

def reset_database():
    conn = sqlite3.connect(DB_PATH)
    cur = conn.cursor()

    # Reset brinde_status and data_resgate
    cur.execute("UPDATE funcionarios SET brinde_status = 0, data_resgate = NULL")
    conn.commit()

    # Quick stats after reset
    cur.execute("SELECT COUNT(*) FROM funcionarios")
    total = cur.fetchone()[0]
    cur.execute("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 1")
    ainda_com_brinde = cur.fetchone()[0]
    cur.execute("SELECT COUNT(*) FROM funcionarios WHERE data_resgate IS NOT NULL AND data_resgate != ''")
    ainda_com_data = cur.fetchone()[0]

    conn.close()

    print("Reset concluido:")
    print(f"  Total funcionarios: {total}")
    print(f"  Com brinde_status=1: {ainda_com_brinde}")
    print(f"  Com data_resgate preenchida: {ainda_com_data}")


def clear_log():
    # Truncate CSV log file
    with open(LOG_PATH, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        # Optionally keep a header line to indicate reset
        writer.writerow(["timestamp", "matricula", "acao", "detalhes"])
        writer.writerow(["RESET", "-", "limpeza", "ambiente de producao iniciado"])
    print(f"Log zerado: {LOG_PATH}")


if __name__ == '__main__':
    reset_database()
    clear_log()
    print("Ambiente preparado para producao.")
