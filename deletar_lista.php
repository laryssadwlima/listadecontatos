<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Verifica se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = "ID da lista inválido.";
    $_SESSION['tipo_mensagem'] = "danger";
    header("Location: index.php");
    exit;
}

$lista_id = (int)$_GET['id'];

try {
    // Inicia a transação
    $conn->begin_transaction();

    // Primeiro, verifica se a lista existe
    $stmt = $conn->prepare("SELECT nome FROM listas_distribuicao WHERE id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Lista de distribuição não encontrada.");
    }

    $lista = $result->fetch_assoc();

    // Remove primeiro os membros da lista
    $stmt = $conn->prepare("DELETE FROM membros_lista WHERE lista_id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();

    // Depois remove a lista em si
    $stmt = $conn->prepare("DELETE FROM listas_distribuicao WHERE id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();

    // Confirma as alterações
    $conn->commit();

    $_SESSION['mensagem'] = "Lista de distribuição '" . htmlspecialchars($lista['nome']) . "' foi excluída com sucesso.";
    $_SESSION['tipo_mensagem'] = "success";

} catch (Exception $e) {
    // Em caso de erro, desfaz as alterações
    $conn->rollback();

    $_SESSION['mensagem'] = "Erro ao excluir lista: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "danger";

    // Log do erro
    error_log("Erro ao deletar lista ID $lista_id: " . $e->getMessage());
}

// Fecha a conexão e redireciona
$conn->close();
header("Location: index.php");
exit;