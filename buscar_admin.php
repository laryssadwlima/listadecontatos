<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['usuario'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT id, nome, usuario, email FROM admins WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($admin = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode($admin);
    } else {
        header('HTTP/1.0 404 Not Found');
    }
}