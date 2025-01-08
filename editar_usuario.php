<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';
$usuario = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, setor = ?, planta = ?, 
                               email = ?, ramal = ?, telefone_comercial = ? WHERE id = ?");
        
        $stmt->bind_param("ssssssi", 
            $_POST['nome'],
            $_POST['setor'],
            $_POST['planta'],
            $_POST['email'],
            $_POST['ramal'],
            $_POST['telefone_comercial'],
            $_POST['id']
        );
        
        $stmt->execute();
        $mensagem = "Usuário atualizado com sucesso!";
        
    } catch (Exception $e) {
        $mensagem = "Erro ao atualizar usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Editar Usuário</h5>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-info"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <?php if ($usuario): ?>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Setor</label>
                                <input type="text" name="setor" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['setor']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Planta</label>
                                <input type="text" name="planta" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['planta']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ramal</label>
                                <input type="text" name="ramal" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['ramal']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telefone Comercial</label>
                                <input type="text" name="telefone_comercial" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['telefone_comercial']); ?>">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">Usuário não encontrado.</div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>