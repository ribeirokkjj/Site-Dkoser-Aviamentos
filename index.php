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
            <a href="#" class="text-dark text-decoration-none me-4">Ajuda</a>
            <a href="https://wa.me/5547999494487?text=" target="_blank" class="text-dark text-decoration-none">Entre em contato</a>
        </nav>
    </div>
</header>

<section class="banner text-white text-center py-4">
    <div class="container">
        <h2 class="mb-3">Bem vindo à D'koser Aviamentos!</h2>
        <p class="mb-0">Adicione itens ao seu carrinho e finalize seu pedido clicando em <strong>"Finalizar Pedido via Whatsapp"</strong> para prosseguir com a compra. Para dúvidas do funcionamento da compra e do pagamento, clique na aba <strong>"Ajuda"</strong> acima.</p>
    </div>
</section>

<main class="container py-5" style="padding-bottom: 180px !important;">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
        
       <?php
$resultado = $conn->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY ordem ASC, id DESC");

if ($resultado->num_rows > 0):
    while ($prod = $resultado->fetch_assoc()):
        // Verifica se este produto possui variações de cores
        $q_var = $conn->query("SELECT * FROM produto_variacoes WHERE produto_id = " . $prod['id']);
        $has_variations = $q_var->num_rows > 0;
        $variations = [];
        while($v = $q_var->fetch_assoc()) {
            $variations[] = $v;
        }
?>

<div class="col">
    <div class="card h-100 text-center shadow-sm">
        <?php if (!empty($prod['imagem'])): ?>
            <img src="<?= htmlspecialchars($prod['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['nome']) ?>" style="aspect-ratio: 1 / 1; object-fit: cover;">
        <?php else: ?>
            <div class="img-placeholder card-img-top"></div>
        <?php endif; ?>
        
        <div class="card-body d-flex flex-column pb-3">
            <h5 class="card-title"><?= htmlspecialchars($prod['nome']) ?></h5>
            
            <p class="card-text fw-bold mb-2">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
            
            <?php if (!empty(trim($prod['descricao']))): ?>
                <a href="#" class="text-success text-decoration-none mb-3 small" data-bs-toggle="modal" data-bs-target="#modalProduto<?= $prod['id'] ?>">Mais informações</a>
            <?php else: ?>
                <div class="mb-3 small">&nbsp;</div>
            <?php endif; ?>
            
            <!-- Controle de Variações vs Produto Simples -->
            <?php if ($has_variations): ?>
                <div class="mt-auto">
                    <div class="d-flex flex-column align-items-center mb-2">
                        <i class="bi bi-palette-fill fs-4 mb-1" style="color: #2e8b57 !important;"></i>
                        <a href="#" class="text-decoration-none small" style="color: #2e8b57 !important;" data-bs-toggle="modal" data-bs-target="#modalVariacoes<?= $prod['id'] ?>">Ver Cores e Detalhes &gt;&gt;</a>
                    </div>
                    <button class="btn btn-success w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#modalVariacoes<?= $prod['id'] ?>">
                        Escolher & Adicionar
                    </button>
                </div>
            <?php else: ?>
                <div class="mt-auto">
                    <?php 
                        $estoque = $prod['estoque'];
                        $qtd_inicial = $estoque > 0 ? 1 : 0;
                        $opacity_plus = ($estoque <= 1) ? 'opacity: 0.5;' : '';
                        $disabled_plus = ($estoque <= 1) ? 'disabled' : '';
                        $opacity_add = ($estoque == 0) ? 'opacity: 0.5;' : '';
                        $disabled_add = ($estoque == 0) ? 'disabled' : '';
                        $texto_add = ($estoque == 0) ? 'Sem Estoque' : 'Adicionar ao carrinho';
                    ?>
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <span class="me-2 text-muted small">Quantidade</span>
                        <div class="input-group input-group-sm custom-input-group">
                            <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                            <input type="number" class="form-control text-center input-qtd" value="<?= $qtd_inicial ?>" min="<?= $estoque > 0 ? 1 : 0 ?>" max="<?= $estoque ?>">
                            <button class="btn btn-outline-secondary btn-plus" type="button" <?= $disabled_plus ?> style="<?= $opacity_plus ?>">+</button>
                        </div>
                    </div>
                    
                    <button class="btn btn-success w-100 btn-sm btn-add-cart" 
                            data-id="<?= $prod['id'] ?>" 
                            data-nome="<?= htmlspecialchars($prod['nome']) ?>" 
                            data-preco="<?= $prod['preco'] ?>"
                            data-estoque="<?= $estoque ?>"
                            <?= $disabled_add ?> style="<?= $opacity_add ?>">
                        <?= $texto_add ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Variações de Cores -->
<?php if ($has_variations): ?>
<div class="modal fade" id="modalVariacoes<?= $prod['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border: 3px solid #676768; border-radius: 12px;"> 
            <div class="modal-header d-flex align-items-center" style="border-bottom: 1px solid #dee2e6;">
                <i class="bi bi-palette-fill fs-4 me-2" style="color: #2e8b57 !important;"></i>
                <h5 class="modal-title mb-0" style="color: #2e8b57 !important;">Variações Disponíveis</h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3 justify-content-center">
                    <?php foreach($variations as $var): ?>
                    <?php 
                        $estoque_var = $var['estoque'];
                        $qtd_inicial_var = $estoque_var > 0 ? 1 : 0;
                        $opacity_plus_var = ($estoque_var <= 1) ? 'opacity: 0.5;' : '';
                        $disabled_plus_var = ($estoque_var <= 1) ? 'disabled' : '';
                        $opacity_add_var = ($estoque_var == 0) ? 'opacity: 0.5;' : '';
                        $disabled_add_var = ($estoque_var == 0) ? 'disabled' : '';
                        $texto_add_var = ($estoque_var == 0) ? 'Esgotado' : 'Adicionar';
                    ?>
                    <div class="col">
                        <div class="d-flex flex-column align-items-center p-2 bg-light border rounded h-100">
                            <div style="background-color: <?= htmlspecialchars($var['cor_hex']) ?>; width: 100%; aspect-ratio: 4/3; border-radius: 6px; margin-bottom: 8px; border: 1px solid #ccc;"></div>
                            <p class="mb-2 fw-bold text-dark text-center" style="font-size: 0.9rem;"><?= htmlspecialchars($var['nome_cor']) ?></p>
                            <div class="d-flex justify-content-center align-items-center mb-2 w-100">
                                <div class="input-group input-group-sm custom-input-group mx-auto" style="max-width: 90px;">
                                    <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                                    <input type="number" class="form-control text-center input-qtd px-1" value="<?= $qtd_inicial_var ?>" min="<?= $estoque_var > 0 ? 1 : 0 ?>" max="<?= $estoque_var ?>">
                                    <button class="btn btn-outline-secondary btn-plus" type="button" <?= $disabled_plus_var ?> style="<?= $opacity_plus_var ?>">+</button>
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm w-100 btn-add-cart mt-auto" style="font-size: 0.8rem; <?= $opacity_add_var ?>"
                                    data-id="<?= $prod['id'] ?>-var-<?= $var['id'] ?>" 
                                    data-nome="<?= htmlspecialchars($prod['nome'] . ' (' . $var['nome_cor'] . ')') ?>" 
                                    data-preco="<?= $prod['preco'] ?>"
                                    data-estoque="<?= $estoque_var ?>"
                                    <?= $disabled_add_var ?>>
                                <?= $texto_add_var ?>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary w-100 fw-bold" data-bs-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal de Descrição do Produto Original Restored -->
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
</main>

<!-- TOAST ALERT (Notificação de tempo) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000; margin-bottom: 75px;">
    <div id="cartToast" class="toast align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2500">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMessage">
                Item adicionado ao carrinho!
            </div>
        </div>
    </div>
</div>

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
                <!-- Items are dynamically injected here by JS -->
            </div>

            <!-- Shipping Calculator & Address Section -->
            <div class="p-3 bg-light border-top border-bottom">
                <label class="form-label fw-bold small mb-1">Calcular Frete e Entrega (CEP):</label>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" id="cep-input" class="form-control" placeholder="00000-000" maxlength="9">
                    <button class="btn btn-outline-secondary" type="button" id="btn-calcular-frete">Calcular</button>
                </div>
                <div id="frete-resultado" class="small mb-2"></div>

                <!-- Endereço de Entrega (Exibido após calcular CEP válido) -->
                <div id="secao-endereco" class="d-none mt-3">
                    <h6 class="fw-bold small border-bottom pb-1 mb-2 text-success">Endereço de Entrega:</h6>
                    <div class="row g-2">
                        <div class="col-8">
                            <input type="text" id="rua-input" class="form-control form-control-sm" placeholder="Rua / Avenida *" required>
                        </div>
                        <div class="col-4">
                            <input type="text" id="numero-input" class="form-control form-control-sm" placeholder="Número *" required>
                        </div>
                        <div class="col-6">
                            <input type="text" id="bairro-input" class="form-control form-control-sm" placeholder="Bairro *" required>
                        </div>
                        <div class="col-6">
                            <input type="text" id="complemento-input" class="form-control form-control-sm" placeholder="Comp. / Ponto de Ref.">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer d-flex flex-column align-items-stretch">
                <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                    <button class="btn btn-sm btn-outline-danger" id="btn-limpar-carrinho">
                        <i class="bi bi-trash"></i> Esvaziar Carrinho
                    </button>
                    <div class="text-end">
                        <small class="text-muted d-block" id="modal-frete-linha">Frete: R$ 0,00</small>
                        <small class="text-muted d-block" id="modal-produtos-linha">Produtos: R$ 0,00</small>
                        <span class="fw-bold fs-5">Total: R$ <span id="modal-cart-total">0,00</span></span>
                    </div>
                </div>
                <button class="btn btn-success w-100" id="btn-finalizar-modal">
                    <i class="bi bi-whatsapp"></i> Finalizar Pedido Via Whatsapp
                </button>

                <!-- Informação sobre o pagamento e rastreio -->
                <div class="text-center mt-2 p-2 bg-white rounded border border-light-subtle small text-muted">
                    <i class="bi bi-info-circle-fill text-success me-1"></i>
                    <span>O vendedor no WhatsApp enviará o link para pagamento e todas as orientações para você acompanhar a entrega.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="carrinho.js"></script>
</body>
</html>