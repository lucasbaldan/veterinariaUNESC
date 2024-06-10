class Notificacao {
    constructor(opcoes = {}) {
        const configuracaoPadrao = {
            dismissible: true,
            duration: 5000,
            position: {
                x: 'right',
                y: 'top',
            },
            types: [
                {
                    type: 'success',
                    background: 'green',
                    icon: '<i class="bi bi-check-circle-fill"></i>',
                },
                {
                    type: 'error',
                    background: 'indianred',
                    icon: '<i class="bi bi-x-octagon-fill"></i>'
                },
                {
                    type: 'warning',
                    background: 'orange',
                    icon: '<i class="bi bi-exclamation-triangle-fill"></i>'
                }
            ]
        };

        // Mescla as opções personalizadas com as configurações padrão
        const options = { ...configuracaoPadrao, ...opcoes };

        this.notyf = new Notyf(options);
    }

    push(mensagem = null, tipo) {
        this.notyf.open({
            type: tipo,
            message: mensagem
        });
    }

    static NotificacaoSucesso() {
        const notificao = new Notificacao();
        notificao.push('Operação Efetuada com Sucesso!', 'success');
    }
    static NotificacaoErro(erro) {
        const notificao = new Notificacao();
        notificao.push('Erro ao Efetuar Operação! <br><br>' + erro, 'error');
    }
    static NotificacaoAviso(aviso) {
        const notificao = new Notificacao();
        notificao.push('Atenção! <br>' + aviso, 'warning');
    }
}
window.Notificacao = Notificacao;

class Loading{
     static on() {
        $('#loading').removeClass('d-none');
    }

    static off(){
        $('#loading').addClass('d-none');
    }
}

class AnimarBotaoLoading {
    constructor(botaoId) {
        this.botaoId = botaoId;
        this.botaoOriginal = $('#' + botaoId).html();
    }

    animar() {
        $('#' + this.botaoId).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Carregando...');
    }

    restaurar() {
        $('#' + this.botaoId).prop('disabled', false).html(this.botaoOriginal);
    }
}

class Select2 {
    constructor(selector, options = {}) {
        this.selector = selector;
        this.dropdownParent = options.dropdownParent || null;
        this.url = options.url || '';
        this.placeholder = options.placeholder || 'Selecione...';

        this.initialize();
    }

    initialize() {
        $(this.selector).select2({
            dropdownParent: this.dropdownParent ? $(this.dropdownParent) : null,
            allowClear: true,
            placeholder: this.placeholder,
            theme: 'bootstrap-5',
            width: '100%',
            ajax: {
                delay: 500,
                url: this.url,
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        buscaSelect2: params.term,
                        forSelect2: true,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.RETURN
                    };
                },
            }
        });
    }
}




