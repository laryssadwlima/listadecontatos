<?php
session_start();
require_once 'db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';
$tipo_mensagem = '';

// Verifica se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = "ID da lista inválido.";
    $_SESSION['tipo_mensagem'] = "danger";
    header("Location: index.php");
    exit;
}

$lista_id = (int)$_GET['id'];

// Busca todos os usuários para selecionar
$usuarios = $conn->query("SELECT id, nome, email, setor FROM usuarios ORDER BY nome");

// Busca os dados da lista atual
try {
    $stmt = $conn->prepare("SELECT * FROM listas_distribuicao WHERE id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();
    $lista = $stmt->get_result()->fetch_assoc();

    if (!$lista) {
        throw new Exception("Lista não encontrada.");
    }

    // Busca os membros atuais da lista
    $stmt = $conn->prepare("SELECT usuario_id FROM membros_lista WHERE lista_id = ?");
    $stmt->bind_param("i", $lista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $membros_atuais = [];
    while ($row = $result->fetch_assoc()) {
        $membros_atuais[] = $row['usuario_id'];
    }

} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao carregar lista: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "danger";
    header("Location: index.php");
    exit;
}

// Processa o formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->begin_transaction();

        // Dados da lista
        $nome_lista = trim($_POST['nome_lista']);
        $email_lista = trim($_POST['email_lista']);
        $membros = isset($_POST['membros']) ? $_POST['membros'] : [];

        // Atualiza a lista
        $stmt = $conn->prepare("UPDATE listas_distribuicao SET nome = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome_lista, $email_lista, $lista_id);
        $stmt->execute();

        // Remove todos os membros atuais
        $stmt = $conn->prepare("DELETE FROM membros_lista WHERE lista_id = ?");
        $stmt->bind_param("i", $lista_id);
        $stmt->execute();

        // Insere os novos membros
        if (!empty($membros)) {
            $stmt = $conn->prepare("INSERT INTO membros_lista (lista_id, usuario_id) VALUES (?, ?)");
            foreach ($membros as $usuario_id) {
                $stmt->bind_param("ii", $lista_id, $usuario_id);
                $stmt->execute();
            }
        }

        $conn->commit();
        $mensagem = "Lista de distribuição atualizada com sucesso!";
        $tipo_mensagem = "success";
        
        // Atualiza os dados da lista e membros após a edição
        $lista['nome'] = $nome_lista;
        $lista['email'] = $email_lista;
        $membros_atuais = $membros;

    } catch (Exception $e) {
        $conn->rollback();
        $mensagem = "Erro ao atualizar lista: " . $e->getMessage();
        $tipo_mensagem = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Lista de Distribuição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f5f5f5;
        }
        .select2-container .select2-selection--multiple {
            min-height: 100px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #154f72;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar Lista de Distribuição
                </h5>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show">
                        <?php echo $mensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome da Lista</label>
                            <input type="text" name="nome_lista" class="form-control" required
                                   value="<?php echo htmlspecialchars($lista['nome']); ?>"
                                   placeholder="Ex: Equipe Comercial">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email da Lista</label>
                            <input type="email" name="email_lista" class="form-control" required
                                   value="<?php echo htmlspecialchars($lista['email']); ?>"
                                   placeholder="Ex: comercial@empresa.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Membros da Lista</label>
                        <select name="membros[]" class="form-control select2" multiple required>
                            <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                <option value="<?php echo $usuario['id']; ?>"
                                        <?php echo in_array($usuario['id'], $membros_atuais) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($usuario['nome']); ?> 
                                    (<?php echo htmlspecialchars($usuario['setor']); ?>) - 
                                    <?php echo htmlspecialchars($usuario['email']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text">
                            Selecione os usuários que farão parte desta lista de distribuição
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar Alterações
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Selecione os membros',
            width: '100%',
            language: {
                noResults: function() {
                    return "Nenhum usuário encontrado";
                }
            }
        });
    });
    </script>
</body>
</html>