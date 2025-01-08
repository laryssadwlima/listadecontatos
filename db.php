<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "listacontatos";

// Criar conexão
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4"); // Define o charset para UTF-8

    // Verifica conexão
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

} catch (Exception $e) {
    // Log do erro
    error_log("Erro de conexão com banco de dados: " . $e->getMessage());
    
    // Mensagem genérica para o usuário
    die("Desculpe, ocorreu um erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Função para escapar strings (proteção contra SQL Injection)
function escape_string($conn, $string) {
    return $conn->real_escape_string($string);
}

// Função para fechar a conexão
function close_connection($conn) {
    $conn->close();
}

// Função para executar queries com prepared statements
function execute_query($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = $conn->prepare($sql);
        
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt;
        
    } catch (Exception $e) {
        error_log("Erro na execução da query: " . $e->getMessage());
        throw new Exception("Erro ao executar operação no banco de dados.");
    }
}

// Função para buscar uma única linha
function fetch_single($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = execute_query($conn, $sql, $params, $types);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Erro ao buscar dados: " . $e->getMessage());
        throw new Exception("Erro ao buscar dados.");
    }
}

// Função para buscar múltiplas linhas
function fetch_all($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = execute_query($conn, $sql, $params, $types);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Erro ao buscar dados: " . $e->getMessage());
        throw new Exception("Erro ao buscar dados.");
    }
}

// Função para inserir dados
function insert_data($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = execute_query($conn, $sql, $params, $types);
        return $conn->insert_id;
        
    } catch (Exception $e) {
        error_log("Erro ao inserir dados: " . $e->getMessage());
        throw new Exception("Erro ao inserir dados.");
    }
}

// Função para atualizar dados
function update_data($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = execute_query($conn, $sql, $params, $types);
        return $stmt->affected_rows;
        
    } catch (Exception $e) {
        error_log("Erro ao atualizar dados: " . $e->getMessage());
        throw new Exception("Erro ao atualizar dados.");
    }
}

// Função para deletar dados
function delete_data($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = execute_query($conn, $sql, $params, $types);
        return $stmt->affected_rows;
        
    } catch (Exception $e) {
        error_log("Erro ao deletar dados: " . $e->getMessage());
        throw new Exception("Erro ao deletar dados.");
    }
}
?>