<?php
session_start();
require_once 'db.php';

// Verifica se está logado como admin
$isAdmin = isset($_SESSION['usuario']);
$nomeAdmin = isset($_SESSION['admin_nome']) ? $_SESSION['admin_nome'] : 'Administrador'; // Valor padrão

$erro = '';

if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $usuario = trim($_POST['usuario']);
        $senha = trim($_POST['senha']);

        if (empty($usuario) || empty($senha)) {
            $erro = 'Por favor, preencha todos os campos.';
        } else {
            // Busca o usuário no banco usando prepared statement
            $stmt = $conn->prepare("SELECT id, usuario, nome, senha FROM admins WHERE usuario = ?");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                // Verifica se a senha está correta
                if ($senha === $admin['senha']) { // Idealmente, você deveria usar password_hash() e password_verify()
                    $_SESSION['usuario'] = $admin['usuario'];
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nome'] = $admin['nome'];
                    
                    header("Location: index.php");
                    exit;
                } else {
                    $erro = 'Usuário ou senha incorretos';
                }
            } else {
                $erro = 'Usuário ou senha incorretos';
            }
        }
    } catch (Exception $e) {
        $erro = 'Erro ao realizar login: ' . $e->getMessage();
        error_log('Erro no login: ' . $e->getMessage());
    }
}

?>

<!-- Resto do HTML permanece igual -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            text-align: center;
            padding: 20px;
        }
        .logo {
            max-width: 230px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
        }
        .btn-primary {
            padding: 10px;
            border-radius: 5px;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header">
                <img src="estilo/logo.png" alt="Logo" class="logo">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($erro): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                                   required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Entrar
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Voltar para o Painel
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>