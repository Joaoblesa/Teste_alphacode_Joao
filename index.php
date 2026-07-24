<?php
// index.php
require_once "config/Database.php";
require_once "models/Contato.php";

$database = new Database();
$db = $database->getConnection();
$contato = new Contato($db);
$mensagem = "";

// Trata mensagens de feedback (Sucesso / Erro)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deletado') $mensagem = "<div class='alert alert-success'>Contato excluído com sucesso!</div>";
    elseif ($_GET['msg'] == 'atualizado') $mensagem = "<div class='alert alert-success'>Contato atualizado com sucesso!</div>";
    elseif ($_GET['msg'] == 'erro') $mensagem = "<div class='alert alert-danger'>Ocorreu um erro na operação.</div>";
}

// Processa o envio do formulário de cadastro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $contato->nome = $_POST['nome'];
    $contato->data_nascimento = $_POST['data_nascimento'];
    $contato->email = $_POST['email'];
    $contato->profissao = $_POST['profissao'];
    $contato->telefone = $_POST['telefone'];
    $contato->celular = $_POST['celular'];
    
    // Tratamento dos Checkboxes
    $contato->whatsapp = isset($_POST['whatsapp']) ? 1 : 0;
    $contato->notificacao_email = isset($_POST['notificacao_email']) ? 1 : 0;
    $contato->notificacao_sms = isset($_POST['notificacao_sms']) ? 1 : 0;

    if ($contato->criar()) {
        $mensagem = "<div class='alert alert-success'>Contato cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar contato.</div>";
    }
}

// Consulta todos os contatos no banco
$resultado = $contato->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphacode - Cadastro de Contatos</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* RESET & BASE */
        * { margin: 0; padding: 0; box-sizing: border-box; outline: none; }
        body { font-family: 'Open Sans', sans-serif; background-color: #ffffff; color: #333333; }

        /* HEADER AZUL DO WIREFRAME */
        header { 
            background-color: #0087d1; 
            color: #ffffff; 
            padding: 20px 60px; 
            display: flex; 
            align-items: center; 
        }
        .logo-topo { height: 50px; margin-right: 25px; }
        header h1 { font-size: 22px; font-weight: 600; }

        /* CONTAINER PRINCIPAL */
        .container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }
        
        /* ALERTAS */
        .alert { padding: 12px 20px; margin-bottom: 25px; border-radius: 4px; font-weight: 600; text-align: center; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* FORMULÁRIO EM DUPAS COLUNAS */
        .form-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px 40px; 
        }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group label { 
            font-size: 13px; 
            color: #333333; 
            font-weight: 700; 
            margin-bottom: 8px; 
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"] {
            padding: 8px 0; 
            border: none; 
            border-bottom: 1px solid #cccccc; 
            font-size: 15px; 
            color: #555555;
            background: transparent; 
        }
        .form-group input:focus { border-bottom: 2px solid #0087d1; }
        .form-group input::placeholder { color: #aaaaaa; }

        /* CHECKBOXES */
        .checkbox-area {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .checkbox-group { display: flex; align-items: center; gap: 8px; }
        .checkbox-group input[type="checkbox"] { 
            width: 18px; 
            height: 18px; 
            accent-color: #0087d1; 
            cursor: pointer;
        }
        .checkbox-group label { font-size: 13px; color: #555555; font-weight: 600; cursor: pointer; }

        /* BOTÃO CADASTRAR */
        .btn-container { grid-column: span 2; display: flex; justify-content: flex-end; }
        .btn-submit { 
            background-color: #56b3e6; 
            color: #ffffff; 
            border: none; 
            padding: 12px 35px; 
            border-radius: 4px; 
            font-size: 15px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: background 0.2s;
        }
        .btn-submit:hover { background-color: #0087d1; }

        /* DIVISOR */
        hr { border: 0; border-top: 1px solid #e0e0e0; margin: 40px 0; }

        /* TABELA ESTILIZADA */
        .table-container { 
            background-color: #ffffff; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            border-radius: 4px;
            overflow: hidden; 
        }
        table { width: 100%; border-collapse: collapse; }
        table thead tr { background-color: #0087d1; color: #ffffff; text-align: left; }
        table th { padding: 12px 20px; font-size: 13px; font-weight: 700; }
        table td { padding: 12px 20px; font-size: 13px; color: #444444; border-bottom: 1px solid #f0f0f0; }
        
        .actions-cell { display: flex; gap: 12px; align-items: center; }
        .icon-action { width: 18px; height: 18px; cursor: pointer; }

        /* RODAPÉ AZUL */
        footer { 
            background-color: #0087d1; 
            color: #ffffff; 
            padding: 18px 40px; 
            font-size: 12px; 
            margin-top: 80px; 
        }
        .footer-content { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            max-width: 1100px; 
            margin: 0 auto; 
        }
        .footer-left a { color: #ffffff; text-decoration: none; font-weight: 600; }
        .logo-footer { height: 20px; vertical-align: middle; margin: 0 5px; }

        /* RESPONSIVO */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .checkbox-area { flex-direction: column; align-items: flex-start; gap: 12px; }
            .btn-container { justify-content: center; }
            .footer-content { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Header Alphacode -->
    <header>
        <img src="assets/logo.png" alt="Alphacode Logo" class="logo-topo">
        <h1>Cadastro de contatos</h1>
    </header>

    <div class="container">
        
        <?php if(!empty($mensagem)) echo $mensagem; ?>

        <!-- Formulário do Wireframe -->
        <form action="index.php" method="POST">
            <div class="form-grid">
                
                <div class="form-group">
                    <label>Nome completo</label>
                    <input type="text" name="nome" placeholder="Ex.: Letícia Pacheco dos Santos" required>
                </div>

                <div class="form-group">
                    <label>Data de nascimento</label>
                    <input type="date" name="data_nascimento" required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="Ex.: leticia@gmail.com" required>
                </div>

                <div class="form-group">
                    <label>Profissão</label>
                    <input type="text" name="profissao" placeholder="Ex.: Desenvolvedora Web" required>
                </div>

                <div class="form-group">
                    <label>Telefone para contato</label>
                    <input type="text" name="telefone" placeholder="Ex.: (11) 4033-2019">
                </div>

                <div class="form-group">
                    <label>Celular para contato</label>
                    <input type="text" name="celular" placeholder="Ex.: (11) 98493-2039" required>
                </div>

                <!-- Checkboxes de Notificação -->
                <div class="checkbox-area">
                    <div class="checkbox-group">
                        <input type="checkbox" id="whatsapp" name="whatsapp" value="1">
                        <label for="whatsapp">Número de celular possui Whatsapp</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="notificacao_email" name="notificacao_email" value="1">
                        <label for="notificacao_email">Enviar notificações por E-mail</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="notificacao_sms" name="notificacao_sms" value="1">
                        <label for="notificacao_sms">Enviar notificações por SMS</label>
                    </div>
                </div>

                <!-- Botão de Cadastro -->
                <div class="btn-container">
                    <button type="submit" name="cadastrar" class="btn-submit">Cadastrar contato</button>
                </div>

            </div>
        </form>

        <hr>

        <!-- Tabela de Listagem -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Data de nascimento</th>
                        <th>E-mail</th>
                        <th>Celular para contato</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado->rowCount() > 0): ?>
                        <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)): 
                            $data_formatada = ($row['data_nascimento']) ? date('d/m/Y', strtotime($row['data_nascimento'])) : '';
                        ?>
                            <tr>
                                <td><?php echo $row['nome']; ?></td>
                                <td><?php echo $data_formatada; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['celular']; ?></td>
                                <td class="actions-cell">
                                    <a href="editar.php?id=<?php echo $row['id']; ?>" title="Editar">
                                        <img src="assets/icon_editar.png" alt="Editar" class="icon-action">
                                    </a>
                                    <a href="deletar.php?id=<?php echo $row['id']; ?>" title="Excluir" onclick="return confirm('Deseja realmente excluir este contato?');">
                                        <img src="assets/icon_excluir.png" alt="Excluir" class="icon-action">
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888;">Nenhum contato cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Rodapé -->
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <a href="#">Termos</a> | <a href="#">Políticas</a>
            </div>
            <div>
                &copy; Copyright 2022 | Desenvolvido por <img src="assets/logo_footer.png" alt="Alphacode Logo" class="logo-footer">
            </div>
            <div>
                &copy;Alphacode IT Solutions 2022
            </div>
        </div>
    </footer>

</body>
</html>