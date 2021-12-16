<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once '../models/ResetPassword.php';

require_once '../helpers/session_helper.php';

require_once '../models/User.php';

// Requer o PHP Mailer
require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';

require_once '../vendor/phpmailer/phpmailer/src/Exception.php';

require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';

class ResetPasswords
{
    private $resetModel;
    private $userModel;
    private $mail;

    public function __construct()
    {
        $this->resetModel = new ResetPassword();
        $this->userModel = new User();
        // Setup do PHPMailer
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Port = $_ENV['MAIL_PORT'];
        $this->mail->Username = $_ENV['MAIL_USERNAME'];
        $this->mail->Password = $_ENV['MAIL_PASSWORD'];
    }

    public function sendEmail()
    {
        // Sanitiza o post
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $usersEmail = trim($_POST['usersEmail']);

        if (empty($usersEmail)) {
            flash('reset', 'Por favor insira email');
            redirect('../reset-password.php');
        }

        if (!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)) {
            flash('reset', 'Email inválido');
            redirect('../reset-password.php');
        }

        // Vai ser usado para fazer a query no bd
        $selector = bin2hex(random_bytes(8));
        // Vai ser usado como confirmação uma vez que foi encontrado um resultado no bd
        $token = random_bytes(32);
        $url = 'http://localhost/create-new-password.php?selector='.$selector.'&validator='
        .bin2hex($token);
        // A data de expiracao ira ser de meia hora
        $expires = date('U') + 1800;
        if (!$this->resetModel->deleteEmail($usersEmail)) {
            exit('Ocorreu um erro');
        }
        $hashedToken = password_hash($token, PASSWORD_BCRYPT);
        if (!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)) {
            exit('Ocorreu um erro');
        }
        // Ja pode enviar o email
        $subject = 'Alteração de senha';
        $message = '<p>Nós recebemos uma requisição de alteração de senha.</p>';
        $message .= '<p>Aqui está o link para alterar sua senha: </p>';
        $message .= "<a href='".$url."'>".$url.'</a>';

        $this->mail->setFrom($_ENV['MAIL_MAIL']);
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->addAddress($usersEmail);

        $this->mail->send();

        flash('reset', 'Cheque o seu email', 'form-message-green');
        redirect('../reset-password.php');
    }

    public function resetPassword()
    {
        // Sanitiza o post
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd-repeat' => trim($_POST['pwd-repeat']),
        ];
        $url = '../create-new-password.php?selector='.$data['selector'].'&validator='
        .$data['validator'];

        if (empty($_POST['pwd'] || $_POST['pwd-repeat'])) {
            flash('newReset', 'Por favor preencha todos os campos');
            redirect($url);
        } elseif ($data['pwd'] != $data['pwd-repeat']) {
            flash('newReset', 'As senhas são diferentes');
            redirect($url);
        } elseif (strlen($data['pwd']) < 6) {
            flash('newReset', 'Senha muito curta');
            redirect($url);
        }

        $currentDate = date('U');
        if (!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)) {
            flash('newReset', 'Desculpe, o link não é mais válido.');
            redirect($url);
        }

        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if (!$tokenCheck) {
            flash('newReset', 'Você precisa requisitar uma nova alteração de senha');
            redirect($url);
        }

        $tokenEmail = $row->pwdResetEmail;
        if (!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)) {
            flash('newReset', 'Ocorreu um erro');
            redirect($url);
        }

        $newPwdHash = password_hash($data['pwd'], PASSWORD_BCRYPT);
        if (!$this->userModel->resetPassword($newPwdHash, $tokenEmail)) {
            flash('newReset', 'Ocorreu um erro');
            redirect($url);
        }

        if (!$this->resetModel->deleteEmail($tokenEmail)) {
            flash('newReset', 'Ocorreu um erro');
            redirect($url);
        }

        flash('newReset', 'Senha alterada', 'form-message-green');
        redirect($url);
    }
}

$init = new ResetPasswords();

// Assegura que o usuario esta fazendo uma requisicao do tipo post
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    switch ($_POST['type']) {
        case 'send':
            $init->sendEmail();

            break;

        case 'reset':
            $init->resetPassword();

            break;

        default:
            header('location: ../index.php');
    }
} else {
    header('location: ../index.php');
}
