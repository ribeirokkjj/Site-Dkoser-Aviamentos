<?php
session_start();

// MUDAR A SENHA AQUI
$senha_correta = '428900'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['senha'] === $senha_correta) {
        $_SESSION['admin_logado'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $erro = "<div class='alert alert-danger'>Senha incorreta! Tente novamente.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - D'koser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
    <div class="text-center mb-4">
        <img src="assets/images/logo.png" alt="Logo" width="80" class="mb-3">
        <h4>Acesso Restrito</h4>
    </div>
    
    <?= $erro ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label">Senha de Acesso:</label>
            <input type="password" name="senha" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-success w-100">Entrar</button>
    </form>
    <div class="text-center mt-3">
        <a href="index.php" class="text-secondary text-decoration-none small">Voltar para a loja</a>
    </div>
</div>

</body>
</html>