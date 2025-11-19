import sqlite3
import qrcode
from io import BytesIO
import base64
import re
from functools import wraps
from flask import Response
from flask import Flask, render_template, request, redirect, url_for, send_file, send_from_directory, session
import csv
import os
from datetime import datetime

app = Flask(__name__)
app.secret_key = 'alianca_brindes_secret_key_2024'
DB_NAME = 'brindes.db'

# --- Configuração e Funções do Banco de Dados ---

def get_db_connection():
    """Cria e retorna a conexão com o banco de dados."""
    conn = sqlite3.connect(DB_NAME)
    conn.row_factory = sqlite3.Row
    return conn

def setup_database():
    """Cria a tabela de funcionários se ela não existir, com restrições mais brandas."""
    conn = get_db_connection()
    conn.execute('''
        CREATE TABLE IF NOT EXISTS funcionarios (
            id INTEGER PRIMARY KEY,
            nome_completo TEXT,
            matricula TEXT,
            cpf TEXT,
            brinde_status INTEGER DEFAULT 0, -- 0: PENDENTE, 1: RESGATADO
            data_resgate TEXT
        );
    ''')
    conn.commit()
    conn.close()

# Inicializa o banco de dados na primeira execução
setup_database()

# --- Logging de ações (QR gerado / baixa) ---
LOG_FILE = 'data_log.csv'

def log_event(action, cpf=None, matricula=None, nome=None, extra=None):
    """Registra um evento no arquivo CSV com dados relevantes."""
    header = ['timestamp', 'action', 'cpf', 'matricula', 'nome', 'remote_addr', 'user_agent', 'extra']
    ts = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    
    remote = ''
    if request:
        # Tenta obter o IP real do cliente, considerando proxies (X-Forwarded-For).
        # Se o cabeçalho existir, pega o primeiro IP da lista (o do cliente original).
        remote = request.headers.get('X-Forwarded-For', request.remote_addr).split(',')[0].strip()

    ua = request.headers.get('User-Agent') if request and request.headers else ''

    write_header = not os.path.exists(LOG_FILE)
    try:
        with open(LOG_FILE, 'a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            if write_header:
                writer.writerow(header)
            writer.writerow([ts, action, cpf or '', matricula or '', nome or '', remote, ua, extra or ''])
    except Exception as e:
        # Não interrompe a aplicação por falha de logging
        print(f"Falha ao gravar log: {e}")


# Rota para servir imagens da pasta imgs (se preferir manter imagens fora de static)
@app.route('/imgs/<path:filename>')
def imgs(filename):
    base = os.path.join(os.path.dirname(__file__), 'imgs')
    return send_from_directory(base, filename)

# --- Funções de Validação ---

def check_auth(username, password):
    """Esta função deve ser usada para verificar se um nome de usuário/senha
    dado é válido."""
    # As senhas são hardcoded aqui, mas em produção devem estar em variáveis de ambiente ou em um banco de dados seguro.
    rh_users = {
        'rhadmin': 'rhadmin1927',
        'jose.neto': 'alianca1927',
        'sara.guimaraes': 'alianca1927',
        'patricia.simoes': 'alianca1927',
        'liberato.silva': 'alianca1927'
    }
    # Verifica se o usuário existe no dicionário e se a senha fornecida corresponde.
    return username in rh_users and rh_users[username] == password

def authenticate():
    """Envia uma resposta para o cliente pedindo autenticação."""
    return Response(
    'Não foi possível verificar seu acesso, você precisa fazer login com as credenciais apropriadas', 401,
    {'WWW-Authenticate': 'Basic realm="Login Obrigatório"'})

def requires_auth(f):
    """O decorador que verifica o cabeçalho de autenticação e a sessão."""
    @wraps(f)
    def decorated(*args, **kwargs):
        # Se o usuário acabou de fazer logout, força a reautenticação.
        if session.get('logout_flag'):
            session.pop('logout_flag', None) # Limpa a flag
            return authenticate()

        # Se o usuário já tem uma sessão válida, permite o acesso.
        if session.get('rh_user'):
            return f(*args, **kwargs)

        auth = request.authorization
        # Se não há credenciais ou se são inválidas, pede autenticação.
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
        
        # Se a autenticação foi bem-sucedida, armazena o usuário na sessão.
        session['rh_user'] = auth.username
        return f(*args, **kwargs)
    return decorated

# --- Funções de QR Code ---

def generate_qr_code(data):
    """Gera um QR Code para os dados e retorna em base64."""
    qr = qrcode.QRCode(version=1, box_size=10, border=5)
    qr.add_data(data)
    qr.make(fit=True)
    img = qr.make_image(fill_color="#000080", back_color="#FFD700") # Azul Escuro e Amarelo
    
    # Converte a imagem para bytes e depois para base64 para uso no HTML
    buffered = BytesIO()
    img.save(buffered, format="PNG")
    img_str = base64.b64encode(buffered.getvalue()).decode("utf-8")
    return f"data:image/png;base64,{img_str}"

# --- Funções de CPF ---

def valida_cpf(cpf):
    """
    Valida um CPF seguindo a regra de cálculo dos dígitos verificadores.
    """
    cpf = re.sub(r'[^0-9]', '', str(cpf)) # Remove formatação

    # 1. Verificação de formato: 11 dígitos e não sequência igual
    if len(cpf) != 11:
        return False
    if cpf == cpf[0] * 11:
        return False

    # Função auxiliar para calcular o dígito verificador
    def calcular_dv(sub_cpf, pesos):
        soma = 0
        for i in range(len(sub_cpf)):
            soma += int(sub_cpf[i]) * pesos[i]
        
        resto = soma % 11
        return 0 if resto < 2 else 11 - resto

    # 2. Cálculo do primeiro dígito verificador (DV1)
    # Pesos decrescentes de 10 a 2
    pesos_dv1 = range(10, 1, -1)
    dv1_calculado = calcular_dv(cpf[:9], pesos_dv1)
    
    # 3. Cálculo do segundo dígito verificador (DV2)
    # Pesos decrescentes de 11 a 2
    pesos_dv2 = range(11, 1, -1)
    dv2_calculado = calcular_dv(cpf[:10], pesos_dv2)

    # 4. Comparação
    # Converte os dígitos calculados para string para comparação
    dv1_real = int(cpf[9])
    dv2_real = int(cpf[10])

    return dv1_calculado == dv1_real and dv2_calculado == dv2_real

# --- Rotas da Aplicação ---

@app.route('/', methods=['GET', 'POST'])
def funcionario_home():
    if request.method == 'POST':
        # Usuário pode escolher usar cpf ou matricula
        identifier = request.form.get('identifier', 'cpf')
        cpf = request.form.get('cpf', '').strip()
        matricula = request.form.get('matricula', '').strip()

        conn = get_db_connection()
        funcionario = None
        try:
            if identifier == 'cpf':
                # Verifica formato básico do CPF antes de buscar
                if not valida_cpf(cpf):
                    conn.close()
                    return render_template('funcionario_home.html', error="CPF inválido. Verifique o número e tente novamente.", bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

                funcionario = conn.execute('SELECT * FROM funcionarios WHERE cpf = ?', (cpf,)).fetchone()
            else:
                # Busca pela matrícula
                if not matricula:
                    conn.close()
                    return render_template('funcionario_home.html', error="Informe a matrícula.", bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')
                funcionario = conn.execute('SELECT * FROM funcionarios WHERE matricula = ?', (matricula,)).fetchone()

        except Exception as e:
            conn.close()
            return render_template('funcionario_home.html', error=f"Erro interno de DB: {e}", bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

        if funcionario is None:
            conn.close()
            return render_template('funcionario_home.html', error="Funcionário não encontrado no cadastro. Consulte o RH.", bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

        # Se já resgatou
        if funcionario['brinde_status'] == 1:
            conn.close()
            return render_template('status.html', status="RESGATADO", data=funcionario['data_resgate'], bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

        # Gera QR e registra log
        qr_data = f"{funcionario['cpf']}:{funcionario['matricula']}"
        qr_code = generate_qr_code(qr_data)
        qr_content = qr_data
        # Log do evento de geração de QR
        try:
            log_event('QR_GENERATED', cpf=funcionario['cpf'], matricula=funcionario['matricula'], nome=funcionario['nome_completo'], extra='QR gerado pelo funcionário')
        except Exception:
            pass

        conn.close()
        return render_template('qr_code_display.html', nome=funcionario['nome_completo'], qr_code=qr_code, qr_content=qr_content, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

    # GET
    return render_template('funcionario_home.html', bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

@app.route('/rh', methods=['GET', 'POST'])
@requires_auth
def rh_home():
    """Página do RH para dar baixa no brinde E ver a lista de resgatados."""
    conn = get_db_connection()
    
    # 1. Busca a lista de funcionários que já resgataram o brinde (Para exibição)
    resgatados = conn.execute(
        'SELECT nome_completo, matricula, cpf, data_resgate FROM funcionarios WHERE brinde_status = 1 ORDER BY data_resgate DESC'
    ).fetchall()
    
    # 2. Lógica para processar o QR Code (POST)
    if request.method == 'POST':
        qr_data = request.form['qr_data'] 
        
        # Verifica se o dado lido é válido (deve conter um divisor ':')
        if not qr_data or ':' not in qr_data:
            # Retorna o erro, passando a lista de resgatados de volta para a tela
            conn.close()
            return render_template('rh_home.html', error="QR Code inválido. Formato esperado: CPF:MATRICULA.", resgatados=resgatados, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

        # Extrai CPF e Matrícula do dado do QR Code
        cpf, matricula = qr_data.split(':')
        
        # Busca o funcionário para verificar o status
        funcionario = conn.execute(
            'SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?', 
            (cpf, matricula)
        ).fetchone()

        conn.close()

        # Verifica se o funcionário existe no DB
        if funcionario is None:
            return render_template('rh_home.html', error="Funcionário não encontrado. Verifique a Matrícula e o CPF.", resgatados=resgatados, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

        # Se o brinde já foi resgatado (status = 1)
        if funcionario['brinde_status'] == 1:
            return render_template('rh_status.html', funcionario=funcionario, resgatado=True, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')
        # Se o brinde está pendente (status = 0)
        else:
            return render_template('rh_status.html', funcionario=funcionario, resgatado=False, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

    # 3. Retorno padrão (GET)
    # Exibe a tela inicial do RH com a lista de resgatados
    conn.close()
    # Passa username para exibir / usar no log se necessário
    username = request.authorization.username if request.authorization else ''
    # Mantemos as mesmas cores da área do funcionário (azul/amarillo)
    return render_template('rh_home.html', resgatados=resgatados, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png', rh_user=session.get('rh_user', ''))


@app.route('/rh_logout')
def rh_logout():
    """Realiza o logout do usuário da área do RH."""
    # Limpa o usuário da sessão e define uma flag para forçar a reautenticação.
    session.pop('rh_user', None)
    session['logout_flag'] = True
    return redirect(url_for('funcionario_home'))


@app.route('/rh_logs')
@requires_auth
def rh_logs():
    # Lê o arquivo de log CSV e separa por tipo de ação
    qr_logs = []
    baixa_logs = []
    # lê e filtra (server-side) por query params simples
    q = request.args.get('q','').strip().lower()
    action_filter = request.args.get('action','')
    date_from = request.args.get('date_from','')
    date_to = request.args.get('date_to','')

    if os.path.exists(LOG_FILE):
        try:
            with open(LOG_FILE, newline='', encoding='utf-8') as f:
                reader = csv.DictReader(f)
                for row in reader:
                    action = row.get('action','')
                    ts = row.get('timestamp','')
                    # split date/time if possible
                    date_part = ''
                    time_part = ''
                    if ts:
                        parts = ts.split(' ')
                        if len(parts) >= 2:
                            date_part = parts[0]
                            time_part = parts[1]
                        else:
                            date_part = ts

                    entry = {
                        'timestamp': ts,
                        'date': date_part,
                        'time': time_part,
                        'action': action,
                        'cpf': row.get('cpf',''),
                        'matricula': row.get('matricula',''),
                        'nome': row.get('nome',''),
                        'remote_addr': row.get('remote_addr',''),
                        'user_agent': row.get('user_agent',''),
                        'extra': row.get('extra',''),
                    }

                    # basic server-side filtering
                    if action_filter and action_filter != action:
                        continue

                    if date_from and entry['date'] and entry['date'] < date_from:
                        continue
                    if date_to and entry['date'] and entry['date'] > date_to:
                        continue

                    if q:
                        hay = ' '.join([entry.get('cpf',''), entry.get('matricula',''), entry.get('nome',''), entry.get('extra',''), entry.get('user_agent','')]).lower()
                        if q not in hay:
                            continue

                    if action == 'QR_GENERATED':
                        qr_logs.append(entry)
                    elif action == 'DAR_BAIXA':
                        baixa_logs.append(entry)
        except Exception as e:
            print(f"Erro lendo logs: {e}")

    # Mostrar com as mesmas cores do RH
    return render_template('rh_logs.html', qr_logs=qr_logs[::-1], baixa_logs=baixa_logs[::-1], bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png', q=q, action_filter=action_filter, date_from=date_from, date_to=date_to)


@app.route('/rh_funcionarios')
@requires_auth
def rh_funcionarios():
    q = request.args.get('q','').strip().lower()
    status = request.args.get('status','')
    conn = get_db_connection()
    rows = conn.execute('SELECT nome_completo, cpf, matricula, brinde_status FROM funcionarios ORDER BY nome_completo').fetchall()
    conn.close()

    funcionarios = []
    for r in rows:
        item = {
            'nome_completo': str(r['nome_completo'] or ''),
            'cpf': str(r['cpf'] or ''),
            'matricula': str(r['matricula'] or ''),
            'brinde_status': int(r['brinde_status'] or 0)
        }
        funcionarios.append(item)

    # server-side filtering
    if q:
        q_lower = q.lower()
        funcionarios = [f for f in funcionarios if q_lower in (f['nome_completo'] + ' ' + f['cpf'] + ' ' + f['matricula']).lower()]
    if status in ('0','1'):
        funcionarios = [f for f in funcionarios if str(f['brinde_status']) == status]

    return render_template('rh_funcionarios.html', funcionarios=funcionarios, bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png', q=q, status=status)

@app.route('/dar_baixa', methods=['POST'])
def dar_baixa():
    """Confirmação de baixa no brinde."""
    cpf = request.form['cpf']
    matricula = request.form['matricula']
    
    # Pega dados atuais antes da atualização
    conn = get_db_connection()
    funcionario = conn.execute('SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?', (cpf, matricula)).fetchone()
    if funcionario is None:
        conn.close()
        return render_template('rh_home.html', error='Funcionário não encontrado para dar baixa.', resgatados=[], bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')

    data_hora = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    conn.execute(
        'UPDATE funcionarios SET brinde_status = 1, data_resgate = ? WHERE cpf = ? AND matricula = ? AND brinde_status = 0',
        (data_hora, cpf, matricula)
    )
    conn.commit()

    # Log do evento de baixa
    try:
        log_event('DAR_BAIXA', cpf=cpf, matricula=matricula, nome=funcionario['nome_completo'], extra=f'Baixa confirmada por RH: {request.authorization.username if request.authorization else "-"}')
    except Exception:
        pass

    conn.close()
    return render_template('rh_confirmacao.html', nome=request.form.get('nome', funcionario['nome_completo']), bg_color='#000080', fg_color='#FFD700', logo_url='/imgs/logo.png')


if __name__ == '__main__':
    # Para rodar localmente, use: python app.py
    # O comando "pip install flask qrcode" deve ser executado antes.
    app.run(host='0.0.0.0', port=5000, debug=True)