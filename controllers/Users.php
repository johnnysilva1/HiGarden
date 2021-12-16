<?php

require_once '../models/User.php';

require_once '../helpers/session_helper.php';

class Users
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        // Processa o form

        // Sanitiza o post
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Dados do init
        $data = [
            'usersName' => trim($_POST['usersName']),
            'usersEmail' => trim($_POST['usersEmail']),
            'usersUid' => trim($_POST['usersUid']),
            'usersPwd' => trim($_POST['usersPwd']),
            'pwdRepeat' => trim($_POST['pwdRepeat']),
        ];

        // Valida os dados
        if (empty($data['usersName']) || empty($data['usersEmail']) || empty($data['usersUid'])
        || empty($data['usersPwd']) || empty($data['pwdRepeat'])) {
            flash('register', 'Por favor preencha todos os campos');
            redirect('../signup.php');
        }

        if (!preg_match('/^[a-zA-Z0-9]*$/', $data['usersUid'])) {
            flash('register', 'Nome de usuário inválido');
            redirect('../signup.php');
        }

        if (!filter_var($data['usersEmail'], FILTER_VALIDATE_EMAIL)) {
            flash('register', 'Email inválido');
            redirect('../signup.php');
        }

        if (strlen($data['usersPwd']) < 6) {
            flash('register', 'Senha inválida');
            redirect('../signup.php');
        } elseif ($data['usersPwd'] !== $data['pwdRepeat']) {
            flash('register', 'As senhas não são iguais');
            redirect('../signup.php');
        }

        // Usuario com o mesmo email ou nome de usuario ja existe
        if ($this->userModel->findUserByEmailOrUsername($data['usersEmail'], $data['usersName'])) {
            flash('register', 'Nome de usuário ou email já estão em uso');
        }

        // Passou por todas as validacoes
        // Faz o hash da senha
        $data['usersPwd'] = password_hash($data['usersPwd'], PASSWORD_BCRYPT);

        // Registra usuario
        if ($this->userModel->register($data)) {
            redirect('../login.php');
        } else {
            exit('Não foi possível registrar o usuário');
        }
    }

    public function login()
    {
        // Sanitiza o post
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Dados do init
        $data = [
            'name/email' => trim($_POST['name/email']),
            'usersPwd' => trim($_POST['usersPwd']),
        ];

        if (empty($data['name/email']) || empty($data['usersPwd'])) {
            flash('login', 'Por favor preencha todos os campos');
            header('location: ../login.php');

            exit();
        }

        // Checa o usuario/email
        if ($this->userModel->findUserByEmailOrUsername($data['name/email'], $data['name/email'])) {
            // Usuario encontrado
            $loggedInUser = $this->userModel->login($data['name/email'], $data['usersPwd']);
            if ($loggedInUser) {
                // Cria a sessao
                $this->createUserSession($loggedInUser);
            } else {
                flash('login', 'Senha incorreta');
                redirect('../login.php');
            }
        } else {
            flash('login', 'Nenhum usuário foi encontrado');
            redirect('../login.php');
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['usersId'] = $user->usersId;
        $_SESSION['usersName'] = $user->usersName;
        $_SESSION['usersEmail'] = $user->usersEmail;

        redirect('../index.php');
    }

    public function logout()
    {
        unset($_SESSION['usersId'], $_SESSION['usersName'], $_SESSION['usersEmail']);

        session_destroy();
        redirect('../index.php');
    }
}

$init = new Users();

// Checa que o metodo e post
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    switch ($_POST['type']) {
        case 'register':
            $init->register();

            break;

        case 'login':
            $init->login();

            break;
    }
} else {
    switch ($_GET['q']) {
        case 'logout':
            $init->logout();

            break;

        default:
            redirect('../index.php');
    }
}
