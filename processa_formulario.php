<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar se os campos obrigatórios foram preenchidos
    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['mensagem'])) {
        echo "Por favor, preencha todos os campos obrigatórios.";
        exit;
    }

    // Validar o formato do e-mail
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "E-mail inválido!";
        exit;
    }

    // Inicializar o PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.hostgator.com.br';
        $mail->SMTPAuth = true;
        $mail->Username = 'seu_email@dominio.com';  // Seu e-mail
        $mail->Password = 'SuaSenha';  // Sua senha de e-mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Definir remetente e destinatário
        $mail->setFrom($_POST['email'], $_POST['nome']);
        $mail->addAddress('contato@inspiretechcompany.com');  // E-mail de destino
        $mail->addReplyTo($_POST['email'], $_POST['nome']);  // E-mail de resposta

        // Verificar se há um arquivo de anexo
        if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['curriculo']['tmp_name'], $_FILES['curriculo']['name']);
        }

        // Definir o conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Novo formulário de Trabalhe Conosco';
        $mail->Body = "
            <p><strong>Nome:</strong> {$_POST['nome']}</p>
            <p><strong>Email:</strong> {$_POST['email']}</p>
            <p><strong>Telefone:</strong> {$_POST['telefone']}</p>
            <p><strong>Cargo:</strong> {$_POST['cargo']}</p>
            <p><strong>Mensagem:</strong><br>{$_POST['mensagem']}</p>
        ";

        // Enviar o e-mail
        $mail->send();

        // Confirmar o envio do e-mail
        echo "E-mail enviado com sucesso!<br>";

        // Redirecionar para a página de agradecimento
        header('Location: trabalhe-conosco-obrigado.html');
        exit;  // Garantir que a execução pare aqui

    } catch (Exception $e) {
        // Caso ocorra erro no envio
        echo "Erro no envio: {$mail->ErrorInfo}";
        exit;  // Interrompe a execução
    }
}
?>
