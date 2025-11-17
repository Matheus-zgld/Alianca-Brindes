# app.py

import sqlite3
import qrcode
from io import BytesIO
import base64
import re
from functools import wraps
from flask import Response
from flask import Flask, render_template, request, redirect, url_for, send_file

app = Flask(__name__)
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

# --- Funções de Validação ---

def check_auth(username, password):
    """Esta função deve ser usada para verificar se um nome de usuário/senha
    dado é válido."""
    # A senha é hardcoded aqui, mas em produção deve estar em variáveis de ambiente.
    return username == 'rhadmin' and password == 'rhadmin1927'

def authenticate():
    """Envia uma resposta para o cliente pedindo autenticação."""
    return Response(
    'Não foi possível verificar seu acesso, você precisa fazer login com as credenciais apropriadas', 401,
    {'WWW-Authenticate': 'Basic realm="Login Obrigatório"'})

def requires_auth(f):
    """O decorador que verifica o cabeçalho de autenticação."""
    @wraps(f)
    def decorated(*args, **kwargs):
        auth = request.authorization
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
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
        cpf = request.form['cpf'].strip()
        matricula = request.form['matricula'].strip()
        nome = request.form['nome'].strip()

        # 1. Validação do CPF por Algoritmo (mantido)
        if not valida_cpf(cpf):
            return render_template('funcionario_home.html', error="CPF inválido. Verifique o número e tente novamente.", bg_color='#000080', fg_color='#FFD700')

        conn = get_db_connection()
        funcionario = None
        
        try:
            # 2. Tenta buscar o funcionário pela combinação CPF E Matrícula
            funcionario = conn.execute(
                'SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?', 
                (cpf, matricula)
            ).fetchone()

            # 3. VERIFICAÇÃO DE DUPLICIDADE/FRAUDE (NOVO BLOCO DE SEGURANÇA)
            if funcionario is None:
                # Se a combinação CPF/Matricula não foi encontrada, checa se o CPF ou a Matrícula já existem
                cpf_existente = conn.execute('SELECT * FROM funcionarios WHERE cpf = ? OR matricula = ?', (cpf, matricula)).fetchone()
                
                if cpf_existente:
                    conn.close()
                    # Bloqueia se o CPF ou Matrícula já estiverem cadastrados em outra combinação
                    return render_template('funcionario_home.html', 
                                            error="Erro de Segurança: Este CPF/Matrícula já está registrado com outra combinação de dados.", 
                                            bg_color='#000080', fg_color='#FFD700')

                # Se for realmente um novo registro (e não uma fraude de combinação), insere
                conn.execute(
                    'INSERT INTO funcionarios (nome_completo, matricula, cpf) VALUES (?, ?, ?)',
                    (nome, matricula, cpf)
                )
                conn.commit()
                
                # Busca o funcionário recém-inserido
                funcionario = conn.execute(
                    'SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?', 
                    (cpf, matricula)
                ).fetchone()

        except Exception as e:
            conn.close()
            return render_template('funcionario_home.html', error=f"Erro interno de DB: {e}", bg_color='#000080', fg_color='#FFD700')
        
        # 4. Checagem de sucesso na busca (Mantida)
        if funcionario is None:
            conn.close()
            return render_template('funcionario_home.html', error="Erro desconhecido. Tente novamente.", bg_color='#000080', fg_color='#FFD700')

        # 5. Lógica de Bloqueio (Regra de um brinde por funcionário - Mantida)
        if funcionario['brinde_status'] == 1:
            conn.close()
            return render_template('status.html', status="RESGATADO", data=funcionario['data_resgate'], bg_color='#000080', fg_color='#FFD700')
        else:
            qr_data = f"{funcionario['cpf']}:{funcionario['matricula']}"
            qr_code = generate_qr_code(qr_data)
            qr_content = qr_data # para exibir
            conn.close()
            return render_template('qr_code_display.html', 
                                    nome=funcionario['nome_completo'], 
                                    qr_code=qr_code, 
                                    qr_content=qr_content,
                                    bg_color='#000080', 
                                    fg_color='#FFD700')

    return render_template('funcionario_home.html', bg_color='#000080', fg_color='#FFD700')

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
            return render_template('rh_home.html', error="QR Code inválido. Formato esperado: CPF:MATRICULA.", resgatados=resgatados, bg_color='#FFD700', fg_color='#000080')

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
            return render_template('rh_home.html', error="Funcionário não encontrado. Verifique a Matrícula e o CPF.", resgatados=resgatados, bg_color='#FFD700', fg_color='#000080')

        # Se o brinde já foi resgatado (status = 1)
        if funcionario['brinde_status'] == 1:
            return render_template('rh_status.html', funcionario=funcionario, resgatado=True, bg_color='#FFD700', fg_color='#000080')
        # Se o brinde está pendente (status = 0)
        else:
            return render_template('rh_status.html', funcionario=funcionario, resgatado=False, bg_color='#FFD700', fg_color='#000080')

    # 3. Retorno padrão (GET)
    # Exibe a tela inicial do RH com a lista de resgatados
    conn.close()
    return render_template('rh_home.html', resgatados=resgatados, bg_color='#FFD700', fg_color='#000080')

@app.route('/dar_baixa', methods=['POST'])
def dar_baixa():
    """Confirmação de baixa no brinde."""
    cpf = request.form['cpf']
    matricula = request.form['matricula']
    
    import datetime
    data_hora = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    conn = get_db_connection()
    conn.execute(
        'UPDATE funcionarios SET brinde_status = 1, data_resgate = ? WHERE cpf = ? AND matricula = ? AND brinde_status = 0',
        (data_hora, cpf, matricula)
    )
    conn.commit()
    conn.close()
    
    # Redireciona para o RH para mostrar o status atualizado
    return render_template('rh_confirmacao.html', nome=request.form['nome'], bg_color='#FFD700', fg_color='#000080')


if __name__ == '__main__':
    # Para rodar localmente, use: python app.py
    # O comando "pip install flask qrcode" deve ser executado antes.
    app.run(debug=True)