## 🎁 Sistema de Resgate de Brindes de Final de Ano 🎄

Este é um sistema web simples e robusto, desenvolvido em **Python/Flask**, para gerenciar a entrega de brindes aos funcionários, garantindo que cada um receba **apenas um brinde** de forma eficiente e rastreável.

-----

## ✨ Funcionalidades Principais

  * **Cadastro e Geração de QR Code:** O funcionário insere seu **Nome**, **Matrícula** e **CPF** para se cadastrar. O sistema gera um QR Code único (`CPF:MATRICULA`), que pode ser salvo ou impresso.
  * **Controle de Duplicidade:** O funcionário pode gerar o QR Code quantas vezes quiser enquanto o brinde estiver pendente. Após a baixa, a geração é **automaticamente bloqueada**.
  * **Área Segura do RH:** Acessada via autenticação por senha (**rhadmin** / **rhadmin1927**), a área permite dar baixa nas entregas.
  * **Leitura Versátil:** O RH pode escanear o QR Code usando a **câmera do celular** ou inserir o código manualmente.
  * **Rastreio em Tempo Real:** A tela do RH exibe uma lista atualizada de todos os funcionários que já tiveram o brinde entregue, mostrando Nome, Matrícula e horário da baixa.
  * **Validação Reforçada:** O cadastro inclui validação do **dígito verificador do CPF** e verifica se a combinação CPF/Matrícula é única, prevenindo tentativas de fraude.

-----

## 🛠️ Tecnologias Utilizadas

| Componente | Tecnologia | Descrição |
| :--- | :--- | :--- |
| **Backend** | Python 🐍 | Linguagem principal do servidor. |
| **Framework Web** | Flask | Microweb framework leve. |
| **Banco de Dados** | SQLite 💾 | Banco de dados local para rastreamento de status. |
| **QR Code** | `qrcode` + `Pillow` | Geração dinâmica da imagem do código. |
| **Frontend** | HTML/CSS/JS 🎨 | Interface com design em **Azul Escuro** e **Amarelo**, incluindo o scanner de câmera via JS. |

-----

## 💡 Estrutura de Pastas

```
/projeto-brindes
├── app.py                  # 🚀 Lógica principal, rotas, autenticação e DB
├── requirements.txt        # 📦 Lista de dependências Python
└── templates/              # 🖼️ Arquivos HTML (Frontend)
    ├── base.html
    ├── funcionario_home.html
    ├── qr_code_display.html
    ├── rh_home.html        # Scanner de câmera e lista de entregues
    ├── rh_status.html
    └── rh_confirmacao.html
```
