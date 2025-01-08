<?php
// Modifique a verificação de admin no início do arquivo
session_start();

// Inicializa as variáveis de admin
$isAdmin = false;
$nomeAdmin = '';

// Verifica se o usuário está logado como admin
if (isset($_SESSION['usuario']) && isset($_SESSION['admin_id'])) {
    $isAdmin = true;
    $nomeAdmin = isset($_SESSION['admin_nome']) ? $_SESSION['admin_nome'] : 'Administrador';
}

// Redireciona para login se não estiver logado como admin
if (!$isAdmin && (isset($_GET['admin']) || strpos($_SERVER['PHP_SELF'], 'adicionar_usuario.php') !== false || strpos($_SERVER['PHP_SELF'], 'editar_usuario.php') !== false || strpos($_SERVER['PHP_SELF'], 'deletar_usuario.php') !== false)) {
    header("Location: login.php");
    exit;
}
// Conexão com a base de dados
require_once 'db.php';

// Lida com o filtro de setor e planta
$setorFiltro = isset($_GET['setor']) ? $_GET['setor'] : '';
$plantaFiltro = isset($_GET['planta']) ? $_GET['planta'] : '';
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Query para buscar setores únicos para o filtro
$setores = $conn->query("SELECT DISTINCT setor FROM usuarios ORDER BY setor");

// Query principal com prepared statements
$sql = "SELECT * FROM usuarios WHERE 1=1";
$params = array();
$types = "";

if (!empty($pesquisa)) {
    $sql .= " AND (nome LIKE ? OR planta LIKE ? OR email LIKE ? OR ramal LIKE ? OR telefone_comercial LIKE ?)";
    $searchTerm = "%$pesquisa%";
    $params = array_merge($params, array_fill(0, 5, $searchTerm));
    $types .= str_repeat('s', 5);
}

if ($setorFiltro) {
    $sql .= " AND setor = ?";
    $params[] = $setorFiltro;
    $types .= 's';
}

if ($plantaFiltro) {
    $sql .= " AND planta = ?";
    $params[] = $plantaFiltro;
    $types .= 's';
}

$sql .= " ORDER BY nome";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>

    body {
        background-color: #f5f5f5; /* Cinza bem claro */
    }

    .table th { 
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
    }

    /* Estilo para linhas alternadas da tabela */
    .table tbody tr:nth-child(odd) {
        background-color: #ffffff; /* Linhas ímpares brancas */
    }

    .table tbody tr:nth-child(even) {
        background-color: #EBEBEB; /* Linhas pares cinza claro */
    }

    /* Efeito hover nas linhas da tabela */
    .table tbody tr:hover {
        background-color: #e9ecef; /* Cor quando passar o mouse */
        transition: background-color 0.2s ease;
    }
     
    .search-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .navbar {
        background-color: #ffff !important;
    }

    .navbar-brand {
        color: white !important;
    }

    .navbar .btn-light:hover {
        background-color: #f8f9fa;
        border-color: #ddd;
    }

    .navbar .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Estilo para a classe "card-header" do bloco "Usuários" */
    .card-header {
        background-color: #154f72;
        color: #ffffff;
    }

    /* Exemplo adicional para sombra personalizada */
    .card.shadow-sm {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="estilo/logo" alt="Logo" width="245" height="50" class="me-2">
        </a>
        
        <?php if ($isAdmin): ?>
        <div class="d-flex align-items-center">
            <span class="me-3 text-white">Bem-vindo, <?php echo htmlspecialchars($nomeAdmin); ?></span>
            <div class="btn-group me-3">
                <button class="btn btn-light" onclick="location.href='adicionar_usuario.php'">
                    <i class="bi bi-plus-circle"></i> Adicionar Usuário
                </button>
    
                <button class="btn btn-info" onclick="location.href='gerenciar_admins.php'">
                    <i class="bi bi-pencil"></i> Config Admins
                </button>
            </div>
            <a href="logout.php" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
        <?php else: ?>
        <div>
            <a href="login.php" class="btn btn-outline-primary">
                <i class="bi bi-person"></i> Login 
            </a>
        </div>
        <?php endif; ?>
    </div>
</nav>

    <!-- Área de Pesquisa e Filtros -->
    <div class="container mt-4">

        <div class="search-container shadow-sm">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="pesquisa" class="form-control" 
                               placeholder="Pesquisar por nome, planta, email, ramal, telefone" 
                               value="<?php echo htmlspecialchars($pesquisa); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="setor" class="form-select">
                        <option value="">Filtrar por Setor</option>
                        <?php while($setor = $setores->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($setor['setor']); ?>" 
                                    <?php if ($setorFiltro == $setor['setor']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($setor['setor']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="planta" class="form-select">
                        <option value="">Filtrar por Planta</option>
                        <option value="P1" <?php if ($plantaFiltro == 'P1') echo 'selected'; ?>>P1</option>
                        <option value="P2" <?php if ($plantaFiltro == 'P2') echo 'selected'; ?>>P2</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php" class="btn btn-secondary" title="Limpar Filtros">
                            <i class="bi bi-x-circle"></i> Limpar   
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="container mt-4">
    <!-- Seção de Listas de Distribuição -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-envelope"></i> Listas de Distribuição
                <?php if ($isAdmin): ?>
                    <button class="btn btn-light btn-sm float-end" onclick="location.href='adicionar_lista.php'">
                        <i class="bi bi-plus-circle"></i> Nova Lista
                    </button>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome da Lista</th>
                            <th>Email</th>
                            <th>Quantidade de Membros</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT l.*, COUNT(m.id) as total_membros 
                                 FROM listas_distribuicao l 
                                 LEFT JOIN membros_lista m ON l.id = m.lista_id 
                                 GROUP BY l.id";
                        $listas = $conn->query($query);
                        
                        while ($lista = $listas->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lista['nome']); ?></td>
                                <td><?php echo htmlspecialchars($lista['email']); ?></td>
                                <td><?php echo $lista['total_membros']; ?></td>
                                <td>
                                    <a href="ver_lista.php?id=<?php echo $lista['id']; ?>" 
                                       class="btn btn-sm btn-primary text-white">
                                        <i class="bi bi-eye"></i> Ver Membros
                                    </a>
                                    <?php if ($isAdmin): ?>
                                        <a href="editar_lista.php?id=<?php echo $lista['id']; ?>" 
                                           class="btn btn-sm btn-warning text-white">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="deletar_lista.php?id=<?php echo $lista['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja deletar esta lista?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Seção de Usuários (já existente) -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="bi bi-people"></i> Usuários
            </h5>
        </div>
        <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Setor</th>
                                <th>Planta</th>
                                <th>Email</th>
                                <th>Ramal</th>
                                <th>Telefone Comercial</th>
                                <?php if ($isAdmin): ?>
                                    <th>Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($row['setor']); ?></td>
                                        <td><?php echo htmlspecialchars($row['planta']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ramal']); ?></td>
                                        <td><?php echo htmlspecialchars($row['telefone_comercial']); ?></td>
                                        <?php if ($isAdmin): ?>
                                            <td class="text-center">
                                                <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-warning text-white me-1">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="deletar_usuario.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Tem certeza que deseja deletar este usuário?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo $isAdmin ? '7' : '6'; ?>" class="text-center">
                                        Nenhum resultado encontrado
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>