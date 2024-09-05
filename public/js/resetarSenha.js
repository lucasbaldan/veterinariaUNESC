$('#alterarSenha').on('click', function () {
    try {
        Loading.on();

        var ajaxModal = $.ajax({
            url: "/veterinaria/modais/resetarSenha",
            method: "POST",
        });

        $.when(ajaxModal).done(function (respostaAjaxModal) {
            bootbox.dialog({
                title: "Alterar Senha",
                message: respostaAjaxModal,
                className: 'cad-alterar-senha',
                buttons: {
                    cancel: {
                        label: " <i class='bi bi-arrow-left'></i> Cancelar",
                        className: 'btn-secondary',
                    },
                    confirm: {
                        label: "Alterar <i class='bi bi-key-fill'></i>",
                        className: 'btn-primary',
                        callback: function () {
                            var formData = $('#resetarSenha').serialize();

                            $.ajax({
                                url: "/veterinaria/server/usuarios/alterarSenha",
                                method: "POST",
                                data: formData,
                                complete: function(xhr, textStatus) {
                                    if (xhr.status === 302) {
                                        var redirectUrl = xhr.getResponseHeader('Location');
                                        if (redirectUrl) {
                                            window.location.href = redirectUrl;
                                        }
                                    }
                                },
                                success: function (response) {
                                    Notificacao.NotificacaoSucesso();
                                    bootbox.hideAll();

                                },
                                error: function (xhr, status, error) {
                                    Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
                                }
                            });
                            return false;
                        }
                    }
                }
            });
        })
        .fail(function (xhr, status, error) {
            bootbox.alert("Erro ao carregar o modal: " + error);
        })
        .always(function () {
            Loading.off();
        });
    } catch (e) {
        Loading.off();
        Notificacao.NotificacaoErro(e.message);
    }
});
