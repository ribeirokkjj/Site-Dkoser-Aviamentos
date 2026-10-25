<?php
    include "config/conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D'koser Aviamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>

<header class="sticky-top py-2 bg-white shadow-sm">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center">
            <div class="logo-placeholder me-3">
                <img src="assets/images/logo.png" alt="">
            </div>
            <h1 class="h4 mb-0">D'koser Aviamentos</h1>
        </div>
        <nav class="mt-2 mt-md-0">
            <a href="#" class="text-dark text-decoration-none me-4">COMO FUNCIONA</a>
            <a href="https://wa.me/5547996532958?text=" target="_blank" class="text-dark text-decoration-none">Entre em contato</a>
        </nav>
    </div>
</header>

<section class="banner text-white text-center py-4">
    <div class="container">
        <h2 class="mb-3">Bem vindo à D'koser Aviamentos!</h2>
        <p class="mb-0">Adicione itens ao seu carrinho e finalize seu pedido clicando em <strong>"Finalizar Pedido via Whatsapp"</strong> para prosseguir com a compra. Para dúvidas do funcionamento da compra e do pagamento, clique na aba <strong>"COMO FUNCIONA"</strong>.</p>
    </div>
</section>

<main class="container py-5 custom-main-padding">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
        
        <?php
        // Busca os produtos ativos, ordenando pela coluna 'ordem' e depois pelos mais recentes
        $resultado = $conn->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY ordem ASC, id DESC");
        
        if ($resultado->num_rows > 0):
            while ($prod = $resultado->fetch_assoc()):
        ?>
        
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <!-- Exibição da Imagem -->
                <?php if (!empty($prod['imagem'])): ?>
                    <img src="<?= htmlspecialchars($prod['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['nome']) ?>" style="aspect-ratio: 1 / 1; object-fit: cover;">
                <?php else: ?>
                    <div class="img-placeholder card-img-top"></div>
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($prod['nome']) ?></h5>
                    
                    <!-- Link de Mais informações -->
                    <?php if (!empty(trim($prod['descricao']))): ?>
                        <a href="#" class="text-success text-decoration-none mb-2 small" data-bs-toggle="modal" data-bs-target="#modalProduto<?= $prod['id'] ?>">Mais informações</a>
                    <?php else: ?>
                        <div class="mb-2 small">&nbsp;</div> <!-- Espaçamento caso não tenha descrição -->
                    <?php endif; ?>
                    
                    <p class="card-text fw-bold">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
                    
                    <div class="d-flex justify-content-center align-items-center mb-3 mt-auto">
                        <span class="me-2 text-muted small">Quantidade</span>
                        <div class="input-group input-group-sm custom-input-group">
                            <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                            <input type="text" class="form-control text-center input-qtd" value="1" readonly>
                            <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                        </div>
                    </div>
                    
                    <!-- Adicionei data-attributes aqui para facilitar o JS do carrinho no próximo passo -->
                    <button class="btn btn-success w-100 btn-sm btn-add-cart" 
                            data-id="<?= $prod['id'] ?>" 
                            data-nome="<?= htmlspecialchars($prod['nome']) ?>" 
                            data-preco="<?= $prod['preco'] ?>">
                        Adicionar ao carrinho
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Descrição do Produto -->
        <?php if (!empty(trim($prod['descricao']))): ?>
        <div class="modal fade" id="modalProduto<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalProdutoLabel<?= $prod['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProdutoLabel<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nome']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body text-start">
                        <?= nl2br(htmlspecialchars($prod['descricao'])) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php 
            endwhile; 
        else:
        ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Nenhum produto disponível no momento.</h4>
            </div>
        <?php endif; ?>

    </div>
</main>

<footer class="cart-bar text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
    
    <a href="#" class="d-flex align-items-center text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalCarrinho">
        <i class="bi bi-cart fs-3 me-3"></i>
        <div>
            <p class="mb-0 fw-bold">Carrinho (<span id="cart-count">0</span> Itens)</p>
            <span class="small">Visualizar Pedido Completo</span>
        </div>
    </a>

    <div class="d-flex align-items-center">
        <span class="me-4 fw-bold">Total Estimado: R$ <span id="cart-total">0,00</span></span>
        <button class="btn btn-success px-4 rounded-pill d-flex align-items-center" id="btn-finalizar-whatsapp">
            <i class="bi bi-whatsapp fs-5 me-3"></i>
            Finalizar Pedido Via Whatsapp
        </button>
    </div>
        
    </div>
</footer>

<!-- Modal do Carrinho -->
<div class="modal fade" id="modalCarrinho" tabindex="-1" aria-labelledby="modalCarrinhoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCarrinhoLabel">Seu Carrinho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="lista-carrinho">
                <!-- Itens serão injetados pelo JS aqui -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <span class="fw-bold">Total: R$ <span id="modal-cart-total">0,00</span></span>
                <button class="btn btn-success" id="btn-finalizar-modal">
                    <i class="bi bi-whatsapp"></i> Finalizar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="carrinho.js"></script>
</body>
</html>