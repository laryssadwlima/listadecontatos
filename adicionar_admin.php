<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = trim($_POST['nome']);
        $usuario = trim($_POST['usuario']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        // Verifica se usuário já existe
        $stmt = $conn->prepare("SELECT id FROM admins WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Este nome de usuário já está em uso.");
        }

        // Insere novo admin
        $stmt = $conn->prepare("INSERT INTO admins (nome, usuario, email, senha, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $nome, $usuario, $email, $senha);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Administrador adicionado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        } else {
            throw new Exception("Erro ao adicionar administrador.");
        }

    } catch (Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
    }

    header("Location: gerenciar_admins.php");
    exit;
}