<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: index.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        header("Location: index.php?error=delete");
        exit;
    }
}
?>