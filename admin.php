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
$vars_edit = [];

// Lógica de Alterar Status (Ativo/Inativo)
if (isset($_GET['status_id'])) {
    $id = (int)$_GET['status_id'];
    $res = $conn->query("SELECT ativo FROM produtos WHERE id = $id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $novo_status = $row['ativo'] ? 0 : 1;
        $conn->query("UPDATE produtos SET ativo = $novo_status WHERE id = $id");
        header("Location: admin.php");
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
        
        // Busca variações
        $res_vars = $conn->query("SELECT * FROM produto_variacoes WHERE produto_id = $id");
        while ($v = $res_vars->fetch_assoc()) {
            $vars_edit[] = $v;
        }
    }
}

// Lógica para Cadastrar ou Atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = str_replace(',', '.', $_POST['preco']);
    $descricao = trim($_POST['descricao']);
    $ordem = (int)$_POST['ordem'];
    $estoque = (int)$_POST['estoque']; // Novo campo de estoque geral
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

    $produto_id = null;

    if (isset($_POST['cadastrar']) && !empty($nome) && !empty($preco)) {
        // Inserir
        $stmt = $conn->prepare("INSERT INTO produtos (nome, preco, descricao, imagem, ordem, estoque) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssii", $nome, $preco, $descricao, $imagem_path, $ordem, $estoque);
        
        if ($stmt->execute()) {
            $produto_id = $conn->insert_id;
            $mensagem = "<div class='alert alert-success'>Produto cadastrado com sucesso!</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
        }
        $stmt->close();
        
    } elseif (isset($_POST['atualizar']) && !empty($nome) && !empty($preco)) {
        // Atualizar
        $produto_id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, preco=?, descricao=?, imagem=?, ordem=?, estoque=? WHERE id=?");
        $stmt->bind_param("sdssiii", $nome, $preco, $descricao, $imagem_path, $ordem, $estoque, $produto_id);
        
        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Produto atualizado com sucesso!</div>";
            $prod_edit = null;
            $vars_edit = [];
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $conn->error . "</div>";
        }
        $stmt->close();
    }

    // Salvar Variações (Substitui as antigas pelas novas)
    if ($produto_id) {
        $conn->query("DELETE FROM produto_variacoes WHERE produto_id = $produto_id");

        if (isset($_POST['var_nome']) && is_array($_POST['var_nome'])) {
            $stmt_var = $conn->prepare("INSERT INTO produto_variacoes (produto_id, nome_cor, cor_hex, estoque) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['var_nome']); $i++) {
                $v_nome = trim($_POST['var_nome'][$i]);
                $v_hex = trim($_POST['var_hex'][$i]);
                $v_est = (int)$_POST['var_estoque'][$i];
                
                if (!empty($v_nome)) {
                    $stmt_var->bind_param("issi", $produto_id, $v_nome, $v_hex, $v_est);
                    $stmt_var->execute();
                }
            }
            $stmt_var->close();
        }
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
        .variation-row { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-bottom: 10px; border: 1px solid #dee2e6; }
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
                    <div class="col-md-3">
                        <label class="form-label">Preço (R$)</label>
                        <input type="text" name="preco" class="form-control" placeholder="Ex: 21,50" value="<?= $prod_edit ? number_format($prod_edit['preco'], 2, ',', '') : '' ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estoque Geral</label>
                        <input type="number" name="estoque" class="form-control" value="<?= $prod_edit ? (int)$prod_edit['estoque'] : '0' ?>" min="0" title="Para produtos sem variação">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-control" value="<?= $prod_edit ? (int)$prod_edit['ordem'] : '0' ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição / Mais informações</label>
                        <textarea name="descricao" class="form-control" rows="2"><?= $prod_edit ? htmlspecialchars($prod_edit['descricao']) : '' ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Foto do Produto <?= $prod_edit && $prod_edit['imagem'] ? '(Deixe em branco para manter a atual)' : '' ?></label>
                        <input type="file" name="imagem" class="form-control" accept="image/*">
                    </div>

                    <!-- Seção de Variações -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">Variações (Cores e Estoque)</h6>
                            <button type="button" class="btn btn-sm btn-outline-success" id="add-variation">
                                <i class="bi bi-plus-circle"></i> Adicionar Variação
                            </button>
                        </div>
                        <small class="text-muted d-block mb-3">Se o produto possui variações, o estoque geral acima será ignorado no frontend.</small>
                        
                        <div id="variations-container">
                            <?php 
                            if (!empty($vars_edit)): 
                                foreach ($vars_edit as $var):
                            ?>
                                <div class="row g-2 align-items-end variation-row">
                                    <div class="col-md-4">
                                        <label class="form-label small">Nome da Cor</label>
                                        <input type="text" name="var_nome[]" class="form-control form-control-sm" value="<?= htmlspecialchars($var['nome_cor']) ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Cor (Hex)</label>
                                        <input type="color" name="var_hex[]" class="form-control form-control-sm form-control-color w-100" value="<?= htmlspecialchars($var['cor_hex']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Estoque desta variação</label>
                                        <input type="number" name="var_estoque[]" class="form-control form-control-sm" value="<?= (int)$var['estoque'] ?>" min="0" required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-variation w-100"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
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
                    <th>Estoque</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $resultado = $conn->query("SELECT * FROM produtos ORDER BY ordem ASC, id DESC");
                while ($prod = $resultado->fetch_assoc()):
                    // Verifica quantidade de variações
                    $q_vars = $conn->query("SELECT COUNT(*) as qtd, SUM(estoque) as total_est FROM produto_variacoes WHERE produto_id = " . $prod['id'])->fetch_assoc();
                    $has_vars = $q_vars['qtd'] > 0;
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
                    <td>
                        <?= htmlspecialchars($prod['nome']) ?>
                        <?php if($has_vars): ?>
                            <br><span class="badge bg-info text-dark" style="font-size: 0.7rem;"><?= $q_vars['qtd'] ?> variações</span>
                        <?php endif; ?>
                    </td>
                    <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                    <td>
                        <?php if($has_vars): ?>
                            <span class="text-muted"><?= $q_vars['total_est'] ?> (Nas vars)</span>
                        <?php else: ?>
                            <?= $prod['estoque'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('variations-container');
    const btnAdd = document.getElementById('add-variation');

    btnAdd.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end variation-row';
        row.innerHTML = `
            <div class="col-md-4">
                <label class="form-label small">Nome da Cor</label>
                <input type="text" name="var_nome[]" class="form-control form-control-sm" placeholder="Ex: Azul Celeste" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Cor (Hex)</label>
                <input type="color" name="var_hex[]" class="form-control form-control-sm form-control-color w-100" value="#000000">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Estoque</label>
                <input type="number" name="var_estoque[]" class="form-control form-control-sm" value="0" min="0" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-danger remove-variation w-100"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variation')) {
            e.target.closest('.variation-row').remove();
        }
    });
});
</script>

</body>
</html>