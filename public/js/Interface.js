class Notificacao {
    constructor(opcoes = {}) {
        const configuracaoPadrao = {
            dismissible: true,
            duration: 10000,
            position: {
                x: 'right',
                y: 'top',
            },
            types: [
                {
                    type: 'success',
                    background: 'green',
                    icon: '<i class="bi bi-check-circle-fill"></i>'
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

    push(mensagem, tipo) {
        this.notyf.open({
            type: tipo,
            message: mensagem
        });
    }
}

// Adiciona a classe Notificacao ao escopo global (window)

class Loading{
     static on() {
        $('#loading').removeClass('d-none');
    }

    static off(){
        $('#loading').addClass('d-none');
    }
}





window.Notificacao = Notificacao;
