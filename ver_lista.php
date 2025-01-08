<?php
session_start();
require_once 'db.php';

$lista_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Busca informações da lista
$stmt = $conn->prepare("SELECT * FROM listas_distribuicao WHERE id = ?");
$stmt->bind_param("i", $lista_id);
$stmt->execute();
$lista = $stmt->get_result()->fetch_assoc();

// Busca membros da lista
$stmt = $conn->prepare("
    SELECT u.* 
    FROM usuarios u 
    INNER JOIN membros_lista m ON u.id = m.usuario_id 
    WHERE m.lista_id = ?
    ORDER BY u.nome
");
$stmt->bind_param("i", $lista_id);
$stmt->execute();
$membros = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Membros da Lista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> 
                    Membros: <?php echo htmlspecialchars($lista['nome']); ?>
                    <small class="d-block text-light">
                        <?php echo htmlspecialchars($lista['email']); ?>
                    </small>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Setor</th>
                                <th>Email</th>
                                <th>Ramal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($membro = $membros->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($membro['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($membro['setor']); ?></td>
                                    <td><?php echo htmlspecialchars($membro['email']); ?></td>
                                    <td><?php echo htmlspecialchars($membro['ramal']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>