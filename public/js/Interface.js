window.onload = function () {
    Loading.off();

    $('.call-screen').on('click', function (event) {
        Loading.on();
    });
};

$('#sairSistema').on('click', function () {
    $.ajax({
        url: '/veterinaria/server/usuarios/deslogar',
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
            Loading.on();
        },
        success: function (response) {
            window.location.href = '/veterinaria/paginas/login';
        },
        error: function (xhr, status, error) {
            window.location.href = '/veterinaria/paginas/login';
        },
        complete: function () {
            window.location.href = '/veterinaria/paginas/login';
        }
    });
});



class Notificacao {
    constructor(opcoes = {}) {
        const configuracaoPadrao = {
            dismissible: true,
            duration: 6000,
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
        notificao.push('<b>Atenção!</b> <br>' + aviso, 'warning');
    }
}
window.Notificacao = Notificacao;

class Loading {
    static on() {
        $('#loading').removeClass('d-none');
    }

    static off() {
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
        this.idTipoAnimal = options.idTipoAnimal || null;
        this.idEspecie = options.idEspecie || null;

        return this.initialize(); // Retornar a instância jQuery diretamente
    }

    initialize() {
        const select2Instance = $(this.selector).select2({
            language: {
                noResults: function () {
                    return "Nenhum resultado encontrado";
                },
                searching: function () {
                    return "Procurando...";
                },
                // Adicione mais traduções conforme necessário
            },
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
                data: (params) => {
                    const requestData = {
                        buscaSelect2: params.term,
                        forSelect2: true,
                    };

                    if (this.idTipoAnimal) {
                        requestData.idTipoAnimal = this.idTipoAnimal;
                    }
                    if (this.idEspecie) {
                        requestData.idEspecie = this.idEspecie;
                    }

                    return requestData;
                },
                processResults: function (data) {
                    return {
                        results: data.RETURN
                    };
                },
            }
        });

        return select2Instance; // Retornar a instância jQuery
    }
}

function bloquearCamposPessoa(extras) {
    $(
        "#nmPessoa, #nrTelefone, #dsEmail, #nrCRMV, #select2cdCidade, #select2cdBairro, #select2cdLogradouro, #cpfPessoa, #dataNascimento" + (extras != null ? (", " + extras) : ' ')
    ).prop("disabled", true);
}

function desbloquearCamposPessoa(extras) {
    $(
        "#nmPessoa, #nrTelefone, #dsEmail, #nrCRMV, #select2cdCidade, #select2cdBairro, #select2cdLogradouro, #cpfPessoa, #dataNascimento" + (extras != null ? (", " + extras) : ' ')
    ).prop("disabled", false);
}