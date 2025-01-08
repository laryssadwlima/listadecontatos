<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Busca todos os admins
$query = "SELECT id, usuario, nome, email, created_at FROM admins ORDER BY nome";
$admins = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill"></i> Administradores do Sistema
                </h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="bi bi-plus-circle"></i> Novo Admin
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($admin = $admins->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($admin['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning text-white" 
                                                onclick="editarAdmin(<?php echo $admin['id']; ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deletarAdmin(<?php echo $admin['id']; ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="adicionar_admin.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Modal para editar admin -->
    <div class="modal fade" id="editAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="editar_admin.php" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" id="edit_nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" id="edit_usuario" name="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                            <input type="password" name="senha" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 

<script>
    function editarAdmin(id) {
        // Busca os dados do admin via AJAX
        fetch(`buscar_admin.php?id=${id}`)
            .then(response => response.json())
            .then(admin => {
                // Preenche o modal com os dados
                document.getElementById('edit_id').value = admin.id;
                document.getElementById('edit_nome').value = admin.nome;
                document.getElementById('edit_usuario').value = admin.usuario;
                document.getElementById('edit_email').value = admin.email;
                
                // Abre o modal
                new bootstrap.Modal(document.getElementById('editAdminModal')).show();
            });
    }

    function deletarAdmin(id) {
        if (confirm('Tem certeza que deseja excluir este administrador?')) {
            window.location.href = `deletar_admin.php?id=${id}`;
        }
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>