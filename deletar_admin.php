<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];

        // Impede que o admin exclua a si mesmo
        if ($_SESSION['admin_id'] == $id) {
            throw new Exception("Você não pode excluir seu próprio usuário.");
        }

        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Administrador excluído com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        } else {
            throw new Exception("Erro ao excluir administrador.");
        }

    } catch (Exception $e) {
        $_SESSION['mensagem'] = $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
    }
}

header("Location: gerenciar_admins.php");
exit;