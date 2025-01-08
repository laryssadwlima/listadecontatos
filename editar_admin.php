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
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $usuario = trim($_POST['usuario']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        // Verifica se usuário existe (exceto para o ID atual)
        $stmt = $conn->prepare("SELECT id FROM admins WHERE usuario = ? AND id != ?");
        $stmt->bind_param("si", $usuario, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Este nome de usuário já está em uso.");
        }

        // Atualiza o admin
        if (empty($senha)) {
            $sql = "UPDATE admins SET nome = ?, usuario = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $usuario, $email, $id);
        } else {
            $sql = "UPDATE admins SET nome = ?, usuario = ?, email = ?, senha = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $nome, $usuario, $email, $senha, $id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Administrador atualizado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        } else {
            throw new Exception("Erro ao atualizar administrador.");
        }

    } catch (Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
    }

    header("Location: gerenciar_admins.php");
    exit;
}