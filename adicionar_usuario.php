<?php
session_start();
require_once 'db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recebe os dados do formulário
        $nome = $_POST['nome'];
        $setor = $_POST['setor'];
        $planta = $_POST['planta'];
        $emails = $_POST['email'];
        $ramais = $_POST['ramal'];
        $telefones = $_POST['telefone_comercial'];

        // Prepara os campos múltiplos
        $emails_concat = implode(';', array_filter($emails));
        $ramais_concat = implode(';', array_filter($ramais));
        $telefones_concat = implode(';', array_filter($telefones));

        // Prepara a query
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, setor, planta, email, ramal, telefone_comercial) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", 
            $nome,
            $setor,
            $planta,
            $emails_concat,
            $ramais_concat,
            $telefones_concat
        );
        $stmt->execute();

        $mensagem = "Usuário adicionado com sucesso!";
        
    } catch (Exception $e) {
        $mensagem = "Erro ao adicionar usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Adicionar Usuário</h5>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-info"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <form method="POST" id="formUsuario">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Setor</label>
                            <input type="text" name="setor" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Planta</label>
                            <select name="planta" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="P1">P1</option>
                                <option value="P2">P2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Emails -->
                    <div class="mb-4">
                        <label class="form-label">Emails</label>
                        <div id="emailsContainer">
                            <div class="input-group mb-2">
                                <input type="email" name="email[]" class="form-control" required>
                                <button type="button" class="btn btn-success" onclick="adicionarCampo('emailsContainer', 'email')">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Ramais -->
                    <div class="mb-4">
                        <label class="form-label">Ramais</label>
                        <div id="ramaisContainer">
                            <div class="input-group mb-2">
                                <input type="text" name="ramal[]" class="form-control">
                                <button type="button" class="btn btn-success" onclick="adicionarCampo('ramaisContainer', 'ramal')">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Telefones Comerciais -->
                    <div class="mb-4">
                        <label class="form-label">Telefones Comerciais</label>
                        <div id="telefonesContainer">
                            <div class="input-group mb-2">
                                <input type="text" name="telefone_comercial[]" class="form-control">
                                <button type="button" class="btn btn-success" onclick="adicionarCampo('telefonesContainer', 'telefone_comercial')">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar Usuário
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function adicionarCampo(containerId, fieldName) {
        const container = document.getElementById(containerId);
        const template = container.children[0].cloneNode(true);
        
        // Limpa o valor do campo
        template.querySelector('input').value = '';
        
        // Adiciona botão de remover
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger';
        removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
        removeBtn.onclick = function() {
            container.removeChild(template);
        };
        
        // Substitui o botão de adicionar pelo de remover
        template.replaceChild(removeBtn, template.lastElementChild);
        
        container.appendChild(template);
    }
    </script>

    <script src="https://cdn.jsdelivr