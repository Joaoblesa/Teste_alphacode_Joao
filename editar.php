<?php
// editar.php
require_once "config/Database.php";
require_once "models/Contato.php";

$database = new Database();
$db = $database->getConnection();
$contato = new Contato($db);

$mensagem = "";

// 1. Verifica se foi passado o ID via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?msg=erro");
    exit();
}

$contato->id = $_GET['id'];

// 2. Processa o envio do formulário de atualização (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar'])) {
    $contato->nome = $_POST['nome'];
    $contato->data_nascimento = $_POST['data_nascimento'];
    $contato->email = $_POST['email'];
    $contato->profissao = $_POST['profissao'];
    $contato->telefone = $_POST['telefone'];
    $contato->celular = $_POST['celular'];
    
    // Checkboxes
    $contato->whatsapp = isset($_POST['whatsapp']) ? 1 : 0;
    $contato->notificacao_email = isset($_POST['notificacao_email']) ? 1 : 0;
    $contato->notificacao_sms = isset($_POST['notificacao_sms']) ? 1 : 0;

    if ($contato->atualizar()) {
        header("Location: index.php?msg=atualizado");
        exit();
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar o contato.</div>";
    }
}

// 3. Carrega os dados atuais do registro
if (!$contato->lerUm()) {
    header("Location: index.php?msg=erro");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphacode - Editar Contato</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; outline: none; }
        body { font-family: 'Open Sans', sans-serif; background-color: #ffffff; color: #333333; }
        a { text-decoration: none; color: inherit; }

        header { background-color: #0087d1; color: #ffffff; padding: 15px 40px; display: flex; align-items: center; }
        .logo-topo { height: 50px; margin-right: 20px; }
        header h1 { font-size: 24px; font-weight: 600; }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: 600; text-align: center; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px 30px; margin-bottom: 30px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 14px; color: #0087d1; font-weight: 600; margin-bottom: 5px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"] {
            padding: 10px 0; border: none; border-bottom: 2px solid #cccccc; font-size: 16px; background: transparent; transition: border-color 0.3s;
        }
        .form-group input:focus { border-bottom-color: #0087d1; }

        .checkbox-group { grid-column: span 2; display: flex; flex-wrap: wrap; gap: 15px 40px; margin-top: 10px; }
        .checkbox-item { display: flex; align-items: center; }
        .checkbox-item input[type="checkbox"] { margin-right: 10px; width: 18px; height: 18px; cursor: pointer; accent-color: #0087d1; }
        .checkbox-item label { font-size: 14px; color: #555555; cursor: pointer; }

        .btn-container { grid-column: span 2; display: flex; justify-content: flex-end; gap: 15px; margin-top: 10px; }
        .btn-submit { background-color: #0087d1; color: #ffffff; border: none; padding: 12px 30px; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.3s; }
        .btn-submit:hover { background-color: #006da8; }
        .btn-cancel { background-color: #6c757d; color: #ffffff; border: none; padding: 12px 25px; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-cancel:hover { background-color: #5a6268; }

        footer { background-color: #333333; color: #ffffff; padding: 20px 40px; font-size: 12px; margin-top: 100px; }
        .footer-content { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .footer-left a { margin-right: 15px; opacity: 0.8; }
        .logo-footer { height: 25px; margin: 0 10px; vertical-align: middle; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .checkbox-group { grid-column: span 1; flex-direction: column; gap: 10px; }
            .btn-container { grid-column: span 1; justify-content: center; }
            .footer-content { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>
</head>
<body>

    <header>
        <img src="assets/logo.png" alt="Alphacode Logo" class="logo-topo">
        <h1>Editar Contato</h1>
    </header>

    <div class="container">
        
        <?php if(!empty($mensagem)) echo $mensagem; ?>

        <form action="editar.php?id=<?php echo $contato->id; ?>" method="POST">
            <div class="form-grid">
                
                <div class="form-group">
                    <label>Nome completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($contato->nome); ?>" required>
                </div>

                <div class="form-group">
                    <label>Data de nascimento</label>
                    <input type="date" name="data_nascimento" value="<?php echo $contato->data_nascimento; ?>" required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($contato->email); ?>" required>
                </div>

                <div class="form-group">
                    <label>Profissão</label>
                    <input type="text" name="profissao" value="<?php echo htmlspecialchars($contato->profissao); ?>" required>
                </div>

                <div class="form-group">
                    <label>Telefone para contato</label>
                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($contato->telefone); ?>">
                </div>

                <div class="form-group">
                    <label>Celular para contato</label>
                    <input type="text" name="celular" value="<?php echo htmlspecialchars($contato->celular); ?>" required>
                </div>

                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="whatsapp" name="whatsapp" <?php echo $contato->whatsapp ? 'checked' : ''; ?>>
                        <label for="whatsapp">Número de celular possui Whatsapp</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="notificacao_email" name="notificacao_email" <?php echo $contato->notificacao_email ? 'checked' : ''; ?>>
                        <label for="notificacao_email">Enviar notificações por E-mail</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="notificacao_sms" name="notificacao_sms" <?php echo $contato->notificacao_sms ? 'checked' : ''; ?>>
                        <label for="notificacao_sms">Enviar notificações por SMS</label>
                    </div>
                </div>

                <div class="btn-container">
                    <a href="index.php" class="btn-cancel">Cancelar</a>
                    <button type="submit" name="atualizar" class="btn-submit">Salvar alterações</button>
                </div>

            </div>
        </form>

    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <a href="#">Termos</a> | <a href="#">Políticas</a>
            </div>
            <div class="footer-center">
                &copy; Copyright 2022 | Desenvolvido por <img src="assets/logo_footer.png" alt="Alphacode IT Solutions" class="logo-footer">
            </div>
            <div class="footer-right">
                @Alphacode IT Solutions 2022
            </div>
        </div>
    </footer>

</body>
</html>