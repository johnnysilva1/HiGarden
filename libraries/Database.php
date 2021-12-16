<?php

/*
    * Classe PDO do Banco de Dados
    * Se conecta ao banco
    * Cria os statements
    * Binda os valores
    * Retorna as linhas e resultados
*/
require_once 'Config.php';

class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbname;

    // Objeto do PDO
    private $dbh;
    private $stmt;
    private $error;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASS'];
        $this->dbname = $_ENV['DB_NAME'];

        // Set o DSN
        $dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        // Cria a instancia do PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepara o statement com a query
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Binda os valores ao statement usando parametros nomeados
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;

                    break;

                case is_bool($value):
                    $type = PDO::PARAM_BOOL;

                    break;

                case is_null($value):
                    $type = PDO::PARAM_NULL;

                    break;

                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    // Retorna multiplos resultados
    public function resultSet()
    {
        $this->execute();

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Retorna um unico resultado
    public function single()
    {
        $this->execute();

        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Retorna o numero de linhas do resultado
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}
