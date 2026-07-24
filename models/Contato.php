<?php
// models/Contato.php

class Contato {
    private $conn;
    private $table_name = "contatos";

    public $id;
    public $nome;
    public $data_nascimento;
    public $email;
    public $profissao;
    public $telefone;
    public $celular;
    public $whatsapp;
    public $notificacao_email;
    public $notificacao_sms;

    public function __construct($db) {
        $this->conn = $db;
    }

    // LISTAR TODOS OS CONTATOS
    public function listar() {
        $query = "SELECT id, nome, data_nascimento, email, profissao, telefone, celular, whatsapp, notificacao_email, notificacao_sms FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // CADASTRAR NOVO CONTATO
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                (nome, data_nascimento, email, profissao, telefone, celular, whatsapp, notificacao_email, notificacao_sms) 
                VALUES 
                (:nome, :data_nascimento, :email, :profissao, :telefone, :celular, :whatsapp, :notificacao_email, :notificacao_sms)";

        $stmt = $this->conn->prepare($query);

        // Limpeza básica
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->profissao = htmlspecialchars(strip_tags($this->profissao));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->celular = htmlspecialchars(strip_tags($this->celular));

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':data_nascimento', $this->data_nascimento);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':profissao', $this->profissao);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':notificacao_email', $this->notificacao_email);
        $stmt->bindParam(':notificacao_sms', $this->notificacao_sms);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // LER UM ÚNICO CONTATO (PARA EDIÇÃO)
    public function lerUm() {
        $query = "SELECT id, nome, data_nascimento, email, profissao, telefone, celular, whatsapp, notificacao_email, notificacao_sms 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nome = $row['nome'];
            $this->data_nascimento = $row['data_nascimento'];
            $this->email = $row['email'];
            $this->profissao = $row['profissao'];
            $this->telefone = $row['telefone'];
            $this->celular = $row['celular'];
            $this->whatsapp = $row['whatsapp'];
            $this->notificacao_email = $row['notificacao_email'];
            $this->notificacao_sms = $row['notificacao_sms'];
            return true;
        }
        return false;
    }

    // ATUALIZAR CONTATO
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                SET 
                    nome = :nome, 
                    data_nascimento = :data_nascimento, 
                    email = :email, 
                    profissao = :profissao, 
                    telefone = :telefone, 
                    celular = :celular, 
                    whatsapp = :whatsapp, 
                    notificacao_email = :notificacao_email, 
                    notificacao_sms = :notificacao_sms 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpeza
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->profissao = htmlspecialchars(strip_tags($this->profissao));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->celular = htmlspecialchars(strip_tags($this->celular));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':data_nascimento', $this->data_nascimento);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':profissao', $this->profissao);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':whatsapp', $this->whatsapp);
        $stmt->bindParam(':notificacao_email', $this->notificacao_email);
        $stmt->bindParam(':notificacao_sms', $this->notificacao_sms);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETAR CONTATO
    public function deletar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}