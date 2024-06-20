$( document ).ready(function() {

      var selectTipoAnimal = new Select2('#select2tipoAnimal', {
        url: '/veterinariaUNESC/server/tipoAnimal/general',
      })

      var selectEspecieAnimal = new Select2('#select2especieAnimal', {
        url: '/veterinariaUNESC/server/especie/general',
      })

      var selectRacaAnimal = new Select2('#select2racaAnimal', {
        url: '/veterinariaUNESC/server/raca/general',
      })

      selectTipoAnimal.on('change', function(e) {
        var TipoAnimalSelecionado = $(this).val();

        $('#select2especieAnimal').val(null).trigger('change');
        selectEspecieAnimal = new Select2('#select2especieAnimal', {
          url: '/veterinariaUNESC/server/especie/general',
          idTipoAnimal: TipoAnimalSelecionado,
        })
      
    });

      $('#nrTelefone').inputmask("(99) 99999-9999", { autoUnmask: true });

});
    


function salvarCadastroPessoas() {
    Loading.on();
    var formData = $("#formCadastroPessoas").serialize();
  
    $.ajax({
      url: "/veterinariaUNESC/server/pessoas/controlar",
      method: "POST",
      data: formData,
      success: function (response) {
        if (response.RESULT) {
          sessionStorage.setItem('notificarSucesso', 'true');
          window.location.href = '/veterinariaUNESC/paginas/listPessoas';
        }
      }, 
      error: function (xhr, status, error) {
        Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      },
      complete: function () {
        Loading.off();
      },
    });
  }
  
  function excluirCadastroPessoas() {
    bootbox.confirm({
      className: "bootbox-delete",
      size: "extra-large",
      title: "Confirmar exclusão?",
      centerVertical: true,
      message:
        "Você tem <b>certeza</b> que deseja excluir esse registro? Esta ação poderá ser desfeita.",
      buttons: {
        cancel: {
          label: '<i class="bi bi-arrow-left"></i> Cancelar',
        },
        confirm: {
          label: '<i class="bi bi-check-lg"></i> Confirmar',
        },
      },
      callback: function (result) {
        if (result) {
          $("#bootbox-delete").modal("hide");
          Loading.on();
          var formData = $("#formCadastroPessoas").serialize();
          $.ajax({
            url: "/veterinariaUNESC/server/pessoas/excluiPessoa",
            method: "POST",
            data: formData,
            success: function (response) {
              if (response.RESULT) {
                sessionStorage.setItem('notificarSucesso', 'true');
                window.location.href = '/veterinariaUNESC/paginas/listPessoas';
              }
            },
            error: function (xhr, status, error) {
              Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
            },
            complete: function () {
              Loading.off();
            },
          });
        }
      },
    });
  }
  