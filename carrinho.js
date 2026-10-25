// Carrega os dados do localStorage ou inicia vazio
let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

// Botões de Quantidade (+ e -)
document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', (e) => {
        let input = e.target.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
    btn.addEventListener('click', (e) => {
        let input = e.target.nextElementSibling;
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });
});

// Adicionar ao Carrinho
document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', (e) => {
        let id = btn.getAttribute('data-id');
        let nome = btn.getAttribute('data-nome');
        let preco = parseFloat(btn.getAttribute('data-preco'));
        let qtdContainer = btn.previousElementSibling;
        let qtd = parseInt(qtdContainer.querySelector('.input-qtd').value);

        let itemExistente = carrinho.find(item => item.id === id);
        if (itemExistente) {
            itemExistente.qtd += qtd;
        } else {
            carrinho.push({ id, nome, preco, qtd });
        }
        
        salvarEAtualizar();
        qtdContainer.querySelector('.input-qtd').value = 1;
    });
});

function salvarEAtualizar() {
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    atualizarInterface();
}

function atualizarInterface() {
    let totalItens = 0;
    let valorTotal = 0;
    let lista = document.getElementById('lista-carrinho');
    
    if (lista) lista.innerHTML = '';

    if (carrinho.length === 0 && lista) {
        lista.innerHTML = '<p class="text-center text-muted my-3">Seu carrinho está vazio.</p>';
    }

    carrinho.forEach(item => {
        let subtotal = item.preco * item.qtd;
        totalItens += item.qtd;
        valorTotal += subtotal;

        // Renderiza o item no modal
        if (lista) {
            lista.innerHTML += `
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h6 class="mb-0">${item.nome}</h6>
                        <small class="text-muted">${item.qtd} un. x R$ ${item.preco.toFixed(2).replace('.', ',')} = R$ ${subtotal.toFixed(2).replace('.', ',')}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger btn-remover" data-id="${item.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
        }
    });

    // Atualiza os totais na barra e no modal
    document.getElementById('cart-count').innerText = totalItens;
    document.getElementById('cart-total').innerText = valorTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    
    let modalTotal = document.getElementById('modal-cart-total');
    if (modalTotal) modalTotal.innerText = valorTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

    // Adiciona evento de remoção aos botões lixeiras
    document.querySelectorAll('.btn-remover').forEach(btn => {
        btn.addEventListener('click', (e) => {
            let id = btn.closest('button').getAttribute('data-id');
            carrinho = carrinho.filter(item => item.id !== id);
            salvarEAtualizar();
        });
    });
}

// Finalizar Pedido WhatsApp
function finalizarPedido() {
    if (carrinho.length === 0) return alert("O carrinho está vazio!");

    let numeroWhatsapp = "5547996532958"; // Insira seu número aqui
    let texto = "Olá! Gostaria de finalizar o seguinte pedido:\n\n";
    let total = 0;

    carrinho.forEach(item => {
        let subtotal = item.preco * item.qtd;
        total += subtotal;
        texto += `${item.qtd}x ${item.nome} (R$ ${subtotal.toFixed(2).replace('.', ',')})\n`;
    });

    texto += `\n*Total Estimado: R$ ${total.toFixed(2).replace('.', ',')}*`;
    window.open(`https://wa.me/${numeroWhatsapp}?text=${encodeURIComponent(texto)}`, '_blank');
}

document.getElementById('btn-finalizar-whatsapp').addEventListener('click', finalizarPedido);
let btnFinalizarModal = document.getElementById('btn-finalizar-modal');
if (btnFinalizarModal) btnFinalizarModal.addEventListener('click', finalizarPedido);

// Atualiza tudo ao carregar a página
atualizarInterface();