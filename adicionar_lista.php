<?php
session_start();
require_once 'db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';

// Busca todos os usuários para selecionar
$usuarios = $conn->query("SELECT id, nome, email, setor FROM usuarios ORDER BY nome");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->begin_transaction();

        // Dados da lista
        $nome_lista = trim($_POST['nome_lista']);
        $email_lista = trim($_POST['email_lista']);
        $membros = isset($_POST['membros']) ? $_POST['membros'] : [];

        // Insere a lista
        $stmt = $conn->prepare("INSERT INTO listas_distribuicao (nome, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome_lista, $email_lista);
        $stmt->execute();
        $lista_id = $conn->insert_id;

        // Insere os membros
        if (!empty($membros)) {
            $stmt = $conn->prepare("INSERT INTO membros_lista (lista_id, usuario_id) VALUES (?, ?)");
            foreach ($membros as $usuario_id) {
                $stmt->bind_param("ii", $lista_id, $usuario_id);
                $stmt->execute();
            }
        }

        $conn->commit();
        $mensagem = "Lista de distribuição criada com sucesso!";
        
    } catch (Exception $e) {
        $conn->rollback();
        $mensagem = "Erro ao criar lista: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Lista de Distribuição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--multiple {
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-envelope-plus"></i> Nova Lista de Distribuição
                </h5>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-info"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome da Lista</label>
                            <input type="text" name="nome_lista" class="form-control" required
                                   placeholder="Ex: Equipe Comercial">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email da Lista</label>
                            <input type="email" name="email_lista" class="form-control" required
                                   placeholder="Ex: comercial@empresa.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Membros da Lista</label>
                        <select name="membros[]" class="form-control select2" multiple required>
                            <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                <option value="<?php echo $usuario['id']; ?>">
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
                            <i class="bi bi-save"></i> Criar Lista
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