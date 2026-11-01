// Carrega os dados do localStorage ou inicia vazio
let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
let freteSelecionado = { cidade: '', uf: '', cep: '', valor: 0, valido: false };

// Lista oficial de cidades atendidas e seus valores de frete
const fretesPermitidos = {
    'ARAQUARI': 16.00,
    'BALNEARIO BARRA DO SUL': 16.00,
    'GARUVA': 15.00,
    'ITAPOA': 15.00,
    'JOINVILLE': 15.00,
    'SAO FRANCISCO DO SUL': 16.00
};

// Remove acentos e converte para caixa alta para comparar cidades com precisão
function normalizarTexto(str) {
    return str ? str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().trim() : "";
}

// Função para recalcular o estoque disponível considerando o que já está no carrinho
function atualizarBotoesEstoque() {
    document.querySelectorAll('.btn-add-cart').forEach(btnAdd => {
        let maxEstoque = parseInt(btnAdd.getAttribute('data-estoque')) || 0;
        let id = btnAdd.getAttribute('data-id');
        
        // Isola o elemento pai do card (tanto produto simples quanto variação na modal)
        let container = btnAdd.closest('.card-body') || btnAdd.closest('.col') || btnAdd.parentElement;
        if (!container) return;
        
        let input = container.querySelector('.input-qtd');
        let btnPlus = container.querySelector('.btn-plus');
        
        let itemNoCarrinho = carrinho.find(item => item.id === id);
        let qtdNoCarrinho = itemNoCarrinho ? itemNoCarrinho.qtd : 0;
        let estoqueRestante = maxEstoque - qtdNoCarrinho;

        if (input && btnPlus) {
            if (maxEstoque === 0 || estoqueRestante <= 0) {
                input.value = 0;
                btnPlus.disabled = true;
                btnPlus.style.opacity = '0.5';
                
                btnAdd.disabled = true;
                btnAdd.style.opacity = '0.5';
                btnAdd.innerText = maxEstoque === 0 ? "Sem estoque" : "Esgotado";
            } else {
                if (parseInt(input.value) === 0) input.value = 1;
                
                if (parseInt(input.value) >= estoqueRestante) {
                    btnPlus.disabled = true;
                    btnPlus.style.opacity = '0.5';
                } else {
                    btnPlus.disabled = false;
                    btnPlus.style.opacity = '1';
                }

                btnAdd.disabled = false;
                btnAdd.style.opacity = '1';
                if (id.includes('-var-')) {
                    btnAdd.innerText = "Adicionar";
                } else {
                    btnAdd.innerText = "Adicionar ao carrinho";
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    atualizarInterface();
    atualizarBotoesEstoque();
});

// Mascara e auto-formatação do campo de CEP
document.addEventListener('input', (e) => {
    if (e.target.id === 'cep-input') {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        e.target.value = v;
    }
});

// Validação de digitação manual nos inputs de quantidade
document.addEventListener('change', (e) => {
    if (e.target.classList.contains('input-qtd')) {
        let input = e.target;
        let container = input.closest('.card-body') || input.closest('.col') || input.parentElement;
        if (!container) return;

        let btnAdd = container.querySelector('.btn-add-cart');
        if (!btnAdd) return;

        let maxEstoque = parseInt(btnAdd.getAttribute('data-estoque')) || 0;
        let id = btnAdd.getAttribute('data-id');
        let itemNoCarrinho = carrinho.find(item => item.id === id);
        let qtdNoCarrinho = itemNoCarrinho ? itemNoCarrinho.qtd : 0;
        let estoqueDisponivel = maxEstoque - qtdNoCarrinho;

        let val = parseInt(input.value) || 0;
        if (estoqueDisponivel <= 0) {
            input.value = 0;
        } else {
            if (val < 1) val = 1;
            if (val > estoqueDisponivel) val = estoqueDisponivel;
            input.value = val;
        }

        let btnPlus = container.querySelector('.btn-plus');
        if (btnPlus) {
            if (parseInt(input.value) >= estoqueDisponivel) {
                btnPlus.disabled = true;
                btnPlus.style.opacity = '0.5';
            } else {
                btnPlus.disabled = false;
                btnPlus.style.opacity = '1';
            }
        }
    }
});

document.addEventListener('click', (e) => {
    // Cálculo do Frete via ViaCEP API
    if (e.target.id === 'btn-calcular-frete') {
        let cepInput = document.getElementById('cep-input').value.replace(/\D/g, '');
        let containerResultado = document.getElementById('frete-resultado');
        let secaoEndereco = document.getElementById('secao-endereco');

        if (cepInput.length !== 8) {
            containerResultado.innerHTML = '<span class="text-danger fw-bold">Por favor, digite um CEP válido (8 dígitos).</span>';
            if (secaoEndereco) secaoEndereco.classList.add('d-none');
            return;
        }

        containerResultado.innerHTML = '<span class="text-info">Consultando cidade...</span>';

        fetch(`https://viacep.com.br/ws/${cepInput}/json/`)
            .then(res => res.json())
            .then(data => {
                if (data.erro) {
                    containerResultado.innerHTML = '<span class="text-danger fw-bold">CEP não encontrado.</span>';
                    freteSelecionado = { cidade: '', uf: '', cep: '', valor: 0, valido: false };
                    if (secaoEndereco) secaoEndereco.classList.add('d-none');
                    salvarEAtualizar();
                    return;
                }

                let cidadeNormalizada = normalizarTexto(data.localidade);

                if (fretesPermitidos.hasOwnProperty(cidadeNormalizada)) {
                    let valorFrete = fretesPermitidos[cidadeNormalizada];
                    freteSelecionado = {
                        cidade: data.localidade,
                        uf: data.uf,
                        cep: document.getElementById('cep-input').value,
                        valor: valorFrete,
                        valido: true
                    };

                    containerResultado.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mt-2 p-2 border bg-white rounded text-success fw-bold">
                            <span><i class="bi bi-truck me-1"></i> Entrega para ${data.localidade} (${data.uf})</span>
                            <span>R$ ${valorFrete.toFixed(2).replace('.', ',')}</span>
                        </div>
                    `;

                    // Exibe a seção de endereço e preenche o que veio da API
                    if (secaoEndereco) {
                        secaoEndereco.classList.remove('d-none');
                        document.getElementById('rua-input').value = data.logradouro || '';
                        document.getElementById('bairro-input').value = data.bairro || '';
                    }
                } else {
                    freteSelecionado = { cidade: data.localidade, uf: data.uf, cep: '', valor: 0, valido: false };
                    containerResultado.innerHTML = `
                        <div class="mt-2 p-2 border bg-white rounded text-danger fw-bold">
                            Desculpe, ainda não realizamos entregas para ${data.localidade} (${data.uf}).
                        </div>
                    `;
                    if (secaoEndereco) secaoEndereco.classList.add('d-none');
                }

                salvarEAtualizar();
            })
            .catch(() => {
                containerResultado.innerHTML = '<span class="text-danger fw-bold">Erro ao consultar o CEP. Tente novamente.</span>';
                freteSelecionado = { cidade: '', uf: '', cep: '', valor: 0, valido: false };
                if (secaoEndereco) secaoEndereco.classList.add('d-none');
                salvarEAtualizar();
            });
    }

    // Quantidade (+) Produto/Variação
    let btnPlus = e.target.closest('.btn-plus');
    if (btnPlus && !btnPlus.disabled) {
        let input = btnPlus.previousElementSibling;
        let container = btnPlus.closest('.card-body') || btnPlus.closest('.col') || btnPlus.parentElement.parentElement;
        let btnAdd = container.querySelector('.btn-add-cart');
        let maxEstoque = btnAdd ? parseInt(btnAdd.getAttribute('data-estoque')) || 0 : Infinity;
        
        let itemNoCarrinho = carrinho.find(item => item.id === btnAdd.getAttribute('data-id'));
        let qtdNoCarrinho = itemNoCarrinho ? itemNoCarrinho.qtd : 0;
        let estoqueDisponivel = maxEstoque - qtdNoCarrinho;

        let currentVal = parseInt(input.value) || 0;
        
        if (currentVal < estoqueDisponivel) {
            input.value = currentVal + 1;
        }
        
        if (parseInt(input.value) >= estoqueDisponivel) {
            btnPlus.disabled = true;
            btnPlus.style.opacity = '0.5';
        }
    }
    
    // Quantidade (-) Produto/Variação
    let btnMinus = e.target.closest('.btn-minus');
    if (btnMinus) {
        let input = btnMinus.nextElementSibling;
        let container = btnMinus.closest('.card-body') || btnMinus.closest('.col') || btnMinus.parentElement.parentElement;
        let btnAdd = container.querySelector('.btn-add-cart');
        let maxEstoque = btnAdd ? parseInt(btnAdd.getAttribute('data-estoque')) || 0 : Infinity;

        let itemNoCarrinho = carrinho.find(item => item.id === btnAdd.getAttribute('data-id'));
        let qtdNoCarrinho = itemNoCarrinho ? itemNoCarrinho.qtd : 0;
        let estoqueDisponivel = maxEstoque - qtdNoCarrinho;

        let currentVal = parseInt(input.value) || 0;
        let minVal = estoqueDisponivel > 0 ? 1 : 0;

        if (currentVal > minVal) {
            input.value = currentVal - 1;
        }

        let btnPlus = input.nextElementSibling;
        if (btnPlus && currentVal - 1 < estoqueDisponivel) {
            btnPlus.disabled = false;
            btnPlus.style.opacity = '1';
        }
    }

    // Aumentar quantidade no carrinho
    let btnCartPlus = e.target.closest('.btn-cart-plus');
    if (btnCartPlus && !btnCartPlus.disabled) {
        let id = btnCartPlus.getAttribute('data-id');
        let item = carrinho.find(i => i.id === id);
        
        if (item && item.qtd < item.maxEstoque) {
            item.qtd++;
            salvarEAtualizar();
        }
    }

    // Diminuir quantidade no carrinho
    let btnCartMinus = e.target.closest('.btn-cart-minus');
    if (btnCartMinus) {
        let id = btnCartMinus.getAttribute('data-id');
        let item = carrinho.find(i => i.id === id);
        if (item && item.qtd > 1) {
            item.qtd--;
            salvarEAtualizar();
        }
    }

    // Limpar carrinho
    if (e.target.closest('#btn-limpar-carrinho')) {
        if (carrinho.length > 0) {
            if (confirm("Tem certeza que deseja esvaziar todo o carrinho?")) {
                carrinho = [];
                freteSelecionado = { cidade: '', uf: '', cep: '', valor: 0, valido: false };
                let containerResultado = document.getElementById('frete-resultado');
                let cepInput = document.getElementById('cep-input');
                let secaoEndereco = document.getElementById('secao-endereco');
                if (containerResultado) containerResultado.innerHTML = '';
                if (cepInput) cepInput.value = '';
                if (secaoEndereco) secaoEndereco.classList.add('d-none');
                salvarEAtualizar();
            }
        }
    }

    // Adicionar ao carrinho
    let btnAddCart = e.target.closest('.btn-add-cart');
    if (btnAddCart && !btnAddCart.disabled) {
        let id = btnAddCart.getAttribute('data-id');
        let nome = btnAddCart.getAttribute('data-nome');
        let preco = parseFloat(btnAddCart.getAttribute('data-preco'));
        let maxEstoque = parseInt(btnAddCart.getAttribute('data-estoque')) || 0;
        
        let container = btnAddCart.closest('.card-body') || btnAddCart.closest('.col') || btnAddCart.parentElement;
        let qtdInput = container.querySelector('.input-qtd');
        let qtd = qtdInput ? (parseInt(qtdInput.value) || 0) : 1;

        if (maxEstoque === 0 || qtd === 0) return;

        let itemExistente = carrinho.find(item => item.id === id);
        let qtdNoCarrinho = itemExistente ? itemExistente.qtd : 0;
        
        if (qtd + qtdNoCarrinho > maxEstoque) return;

        if (itemExistente) {
            itemExistente.qtd += qtd;
            itemExistente.maxEstoque = maxEstoque;
        } else {
            carrinho.push({ id, nome, preco, qtd, maxEstoque });
        }
        
        salvarEAtualizar();

        let toastEl = document.getElementById('cartToast');
        if (toastEl) {
            let toastMsg = document.getElementById('toastMessage');
            toastMsg.innerText = `${qtd}x ${nome} adicionado!`;
            let toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        }
    }

    // Remover item do carrinho
    let btnRemover = e.target.closest('.btn-remover');
    if (btnRemover) {
        let id = btnRemover.getAttribute('data-id');
        carrinho = carrinho.filter(item => item.id !== id);
        salvarEAtualizar();
    }
});

function salvarEAtualizar() {
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    atualizarInterface();
    atualizarBotoesEstoque();
}

function atualizarInterface() {
    let totalItens = 0;
    let valorProdutos = 0;
    let lista = document.getElementById('lista-carrinho');
    let btnLimpar = document.getElementById('btn-limpar-carrinho');
    
    if (lista) lista.innerHTML = '';

    if (carrinho.length === 0 && lista) {
        lista.innerHTML = '<p class="text-center text-muted my-4">Seu carrinho está vazio.</p>';
        if (btnLimpar) btnLimpar.style.display = 'none';
    } else {
        if (btnLimpar) btnLimpar.style.display = 'inline-block';
    }

    carrinho.forEach(item => {
        let subtotal = item.preco * item.qtd;
        totalItens += item.qtd;
        valorProdutos += subtotal;

        let plusDisabled = (item.qtd >= item.maxEstoque) ? 'disabled style="opacity: 0.5;"' : '';

        if (lista) {
            lista.innerHTML += `
                <div class="d-flex flex-column mb-3 border-bottom pb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 pe-3">${item.nome}</h6>
                        <button class="btn btn-sm btn-outline-danger btn-remover py-0 px-2" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-group input-group-sm custom-input-group" style="width: auto;">
                            <button class="btn btn-outline-secondary btn-cart-minus" data-id="${item.id}" type="button">-</button>
                            <input type="text" class="form-control text-center px-1" value="${item.qtd}" style="max-width: 40px;" readonly>
                            <button class="btn btn-outline-secondary btn-cart-plus" data-id="${item.id}" type="button" ${plusDisabled}>+</button>
                        </div>
                        <small class="text-muted fw-bold">R$ ${subtotal.toFixed(2).replace('.', ',')}</small>
                    </div>
                </div>
            `;
        }
    });

    let totalGeral = valorProdutos + (freteSelecionado.valido ? freteSelecionado.valor : 0);

    let cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) cartCountEl.innerText = totalItens;
    
    let cartTotalEl = document.getElementById('cart-total');
    if (cartTotalEl) cartTotalEl.innerText = totalGeral.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    
    let modalTotal = document.getElementById('modal-cart-total');
    if (modalTotal) modalTotal.innerText = totalGeral.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

    let modalFreteLinha = document.getElementById('modal-frete-linha');
    if (modalFreteLinha) {
        if (freteSelecionado.valido) {
            modalFreteLinha.innerText = `Frete (${freteSelecionado.cidade}): R$ ${freteSelecionado.valor.toFixed(2).replace('.', ',')}`;
        } else {
            modalFreteLinha.innerText = `Frete: R$ 0,00`;
        }
    }

    let modalProdutosLinha = document.getElementById('modal-produtos-linha');
    if (modalProdutosLinha) {
        modalProdutosLinha.innerText = `Produtos: R$ ${valorProdutos.toFixed(2).replace('.', ',')}`;
    }
}

function finalizarPedido() {
    if (carrinho.length === 0) {
        alert("O carrinho está vazio!");
        return;
    }

    if (!freteSelecionado.valido) {
        alert("Por favor, informe um CEP válido de uma cidade atendida antes de finalizar o pedido.");
        return;
    }

    let rua = document.getElementById('rua-input')?.value.trim();
    let numero = document.getElementById('numero-input')?.value.trim();
    let bairro = document.getElementById('bairro-input')?.value.trim();
    let complemento = document.getElementById('complemento-input')?.value.trim();

    if (!rua || !numero || !bairro) {
        alert("Por favor, preencha a Rua, Número e Bairro para o envio da entrega.");
        return;
    }

    let numeroWhatsapp = "5547999494487";
    let texto = "Olá! Gostaria de finalizar o seguinte pedido:\n\n";
    let valorProdutos = 0;

    carrinho.forEach(item => {
        let subtotal = item.preco * item.qtd;
        valorProdutos += subtotal;
        texto += `${item.qtd}x ${item.nome} (R$ ${subtotal.toFixed(2).replace('.', ',')})\n`;
    });

    let totalGeral = valorProdutos + freteSelecionado.valor;

    texto += `\n*Produtos:* R$ ${valorProdutos.toFixed(2).replace('.', ',')}`;
    texto += `\n*Frete (${freteSelecionado.cidade}):* R$ ${freteSelecionado.valor.toFixed(2).replace('.', ',')}`;
    texto += `\n*Total a Pagar:* R$ ${totalGeral.toFixed(2).replace('.', ',')}\n`;

    texto += `\n*Endereço para Entrega:*`;
    texto += `\nEndereço: ${rua}, Nº ${numero}`;
    texto += `\nBairro: ${bairro}`;
    texto += `\nCidade: ${freteSelecionado.cidade} - ${freteSelecionado.uf}`;
    texto += `\nCEP: ${freteSelecionado.cep}`;
    if (complemento) {
        texto += `\nComplemento / Ref.: ${complemento}`;
    }

    window.open(`https://wa.me/${numeroWhatsapp}?text=${encodeURIComponent(texto)}`, '_blank');
}

let btnFinalizarModal = document.getElementById('btn-finalizar-modal');
if (btnFinalizarModal) btnFinalizarModal.addEventListener('click', finalizarPedido);