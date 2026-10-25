<?php
session_start();

// Verifica se a pessoa não está logada
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header("Location: login.php");
    exit;
}

include "config/conexao.php";

$mensagem = '';
$prod_edit = null;

// Lógica de Alterar Status (Ativo/Inativo)
if (isset($_GET['status_id'])) {
    $id = (int)$_GET['status_id'];
    // Busca o status atual
    $res = $conn->query("SELECT ativo FROM produtos WHERE id = $id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $novo_status = $row['ativo'] ? 0 : 1; // Inverte o valor
        $conn->query("UPDATE produtos SET ativo = $novo_status WHERE id = $id");
        header("Location: admin.php"); // Recarrega a página para limpar a URL
        exit;
    }
}

// Lógica de Exclusão
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success'>Produto excluído com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao excluir: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Lógica para carregar os dados de Edição
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $res = $conn->query("SELECT * FROM produtos WHERE id = $id");
    if ($res->num_rows > 0) {
        $prod_edit = $res->fetch_assoc();
    }
}

// Lógica para Cadastrar ou Atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);
    $ordem = (int)$_POST['ordem']; // Novo campo de ordenação
    $imagem_path = $_POST['imagem_atual'] ?? '';

    // Upload de Imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $pasta_destino = 'assets/images/produtos/';
        if (!is_dir($pasta_destino)) mkdir($pasta_destino, 0777, true);

        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $caminho_final = $pasta_destino . uniqid('prod_') . '.' . $extensao;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_final)) {
            $imagem_path = $caminho_final;
        }
    }

    if (isset($_POST['cadastrar']) && !empty($nome) && !empty($preco)) {
        // Inserir
        $stmt = $conn->prepare("INSERT INTO produtos (nome, preco, descricao, imagem, ordem) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $nome, $preco, $descricao, $imagem_path, $ordem);
        
        if ($stmt->execute()) $mensagem = "<div class='alert alert-success'>Produto cadastrado com sucesso!</div>";
        else $mensagem = "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
        $stmt->close();
        
    } elseif (isset($_POST['atualizar']) && !empty($nome) && !empty($preco)) {
        // Atualizar
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, preco=?, descricao=?, imagem=?, ordem=? WHERE id=?");
        $stmt->bind_param("sdssii", $nome, $preco, $descricao, $imagem_path, $ordem, $id);
        
        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Produto atualizado com sucesso!</div>";
            $prod_edit = null; // Limpa o form após atualizar
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - D'koser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <style>
        .cursor-pointer { cursor: pointer; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel Administrativo - Produtos</h2>
        <?php if ($prod_edit): ?>
            <a href="admin.php" class="btn btn-secondary">Cancelar Edição</a>
        <?php endif; ?>
    </div>
    
    <?= $mensagem ?>

    <!-- Formulário Dinâmico (Cadastro / Edição) -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title mb-3"><?= $prod_edit ? 'Editar Produto #'.$prod_edit['id'] : 'Novo Cadastro' ?></h5>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <?php if ($prod_edit): ?>
                    <input type="hidden" name="id" value="<?= $prod_edit['id'] ?>">
                    <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($prod_edit['imagem']) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Nome do Produto</label>
                        <input type="text" name="nome" class="form-control" value="<?= $prod_edit ? htmlspecialchars($prod_edit['nome']) : '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preço (R$)</label>
                        <input type="text" name="preco" class="form-control" placeholder="Ex: 21,50" value="<?= $prod_edit ? number_format($prod_edit['preco'], 2, ',', '') : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordem de Exibição</label>
                        <input type="number" name="ordem" class="form-control" value="<?= $prod_edit ? (int)$prod_edit['ordem'] : '0' ?>" title="Menores números aparecem primeiro">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição / Mais informações</label>
                        <textarea name="descricao" class="form-control" rows="2"><?= $prod_edit ? htmlspecialchars($prod_edit['descricao']) : '' ?></textarea>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Foto do Produto <?= $prod_edit && $prod_edit['imagem'] ? '(Deixe em branco para manter a atual)' : '' ?></label>
                        <input type="file" name="imagem" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <?php if ($prod_edit): ?>
                            <button type="submit" name="atualizar" class="btn btn-primary w-100">Salvar Alterações</button>
                        <?php else: ?>
                            <button type="submit" name="cadastrar" class="btn btn-success w-100">Cadastrar Produto</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <h4 class="mb-3">Produtos Cadastrados</h4>
    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Ordem</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // A query agora ordena pela coluna 'ordem' (crescente) e depois pelo ID
                $resultado = $conn->query("SELECT * FROM produtos ORDER BY ordem ASC, id DESC");
                while ($prod = $resultado->fetch_assoc()):
                ?>
                <tr>
                    <td class="fw-bold text-muted"><?= isset($prod['ordem']) ? $prod['ordem'] : '0' ?></td>
                    <td>
                        <?php if ($prod['imagem']): ?>
                            <img src="<?= $prod['imagem'] ?>" alt="Foto" width="50" height="50" style="object-fit:cover; border-radius: 5px;">
                        <?php else: ?>
                            <span class="text-muted">Sem foto</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($prod['nome']) ?></td>
                    <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                    <td>
                        <!-- Botão clicável para alterar o status -->
                        <a href="admin.php?status_id=<?= $prod['id'] ?>" class="text-decoration-none">
                            <span class="badge bg-<?= isset($prod['ativo']) && $prod['ativo'] ? 'success' : 'secondary' ?> cursor-pointer p-2">
                                <?= isset($prod['ativo']) && $prod['ativo'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </a>
                    </td>
                    <td class="text-end">
                        <a href="admin.php?editar=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="admin.php?excluir=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>