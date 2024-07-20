// REFERENTE AO CADASTRO DO GRUPO DE USUÁRIOS

function salvarCadastroGrupoUsuarios() {
  botaoAnimado = new AnimarBotaoLoading("btnSalvar");
  botaoAnimadoExcluir = new AnimarBotaoLoading("btnExcluir");
  botaoAnimado.animar();
  botaoAnimadoExcluir.animar();
  Loading.on();
  var formData = $("#formCadastroGruposUsuarios").serialize();

  // console.log("FORM: ", formData);

  $.ajax({
    url: "/veterinariaUNESC/server/gruposUsuarios/salvaGrupoUsuarios",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        Notificacao.NotificacaoSucesso();
        bootbox.hideAll();
        if (typeof dataTableGruposUsuarios !== "undefined") {
          dataTableGruposUsuarios.ajax.reload();
        }

        if(response.RETURN !== 'undefined' && response.RETURN !== '' && response.RETURN !== null) {
          $('#cdGrupoUsuarios').val(response.RETURN);
          $('#cdGrupoUsuarios2').val(response.RETURN);
        }
      }
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
    },
    complete: function () {
      Loading.off();
      botaoAnimado.restaurar();
      botaoAnimadoExcluir.restaurar();
    },
  });
}

function excluirCadastroGrupoUsuarios() {
  bootbox.confirm({
    className: "bootbox-delete",
    size: "extra-large",
    title: "Confirmar exclusão?",
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
        btnSalvaGrupoUsuarios = new AnimarBotaoLoading("btnSalvar");
        btnExcluiGruposUsuarios = new AnimarBotaoLoading("btnExcluir");
        btnSalvaGrupoUsuarios.animar();
        btnExcluiGruposUsuarios.animar();

        $("#bootbox-delete").modal("hide");
        Loading.on();
        var formData = $("#formCadastroGruposUsuarios").serialize();
        $.ajax({
          url: "/veterinariaUNESC/server/gruposUsuarios/excluiGruposUsuarios",
          method: "POST",
          data: formData,
          success: function (response) {
            window.location.href = '/veterinariaUNESC/paginas/listAcessosGruposUsuarios';
            // if (response.RESULT) {
            //   Notificacao.NotificacaoSucesso();
            //   bootbox.hideAll();
            //   if (typeof dataTableGruposUsuarios !== "undefined") {
            //     dataTableGruposUsuarios.ajax.reload();
            //   }
            // }
          },
          error: function (xhr, status, error) {
            Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
          },
          complete: function () {
            Loading.off();
            btnSalvaGrupoUsuarios.restaurar();
            btnExcluiGruposUsuarios.restaurar();
          },
        });
      }
    },
  });
}

// CAPTURA O EVENTO DE CLICK 

$(document).ready(function () {
  $('#tab1-btn').on('click', function () {
      // Handle tab switching
      $('.tabs li, .tab-content').removeClass('active');
      $(this).addClass('active');
      $('#tab1').addClass('active');
  });

  $('#tab2-btn').on('click', function () {
      // Handle tab switching
      $('.tabs li, .tab-content').removeClass('active');
      $(this).addClass('active');
      $('#tab2').addClass('active');

      // Copy value from tab1 input to tab2 input
      var cdGrupoUsuarios = $('#cdGrupoUsuarios').val();
      $('#cdGrupoUsuarios2').val(cdGrupoUsuarios);
  });
});

// REFERENTE À GESTÃO DE ACESSOS DO GRUPO DE USUARIOS

const notificacao = new Notificacao({
  duration: 10000,
  position: {
    x: 'end',
    y: 'top',
  },
});

function salvarCadastroAcessosGruposUsuarios() {
   Loading.on();
  var formData = $("#formCadastroAcessosGruposUsuarios").serialize();
  $.ajax({
    url: "/veterinariaUNESC/server/gruposUsuarios/salvaAcessos",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        // sessionStorage.setItem('notificarSucesso', 'true');
        notificacao.push('Permissões salvas com Sucesso!', 'success');
        // window.location.href = '/veterinariaUNESC/paginas/listPessoas';
      }
      Loading.off();
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoErro(xhr.responseJSON.MESSAGE);
      Loading.off();
    },
  });

}

$('#btnFichaLPV').on('click', function () {
  toggleCheckboxes(
    ['flAcessarFichaLPV', 'flEditarFichaLPV', 'flInserirFichaLPV', 'flExcluirFichaLPV'],
    'iconFichaLPV'
  );
});

$('#btnCadastroPessoas').on('click', function () {
  toggleCheckboxes(
    ['flAcessarCadastroPessoas', 'flEditarCadastroPessoas', 'flInserirCadastroPessoas', 'flExcluirCadastroPessoas'],
    'iconCadastroPessoas'
  );
});

$('#btnCadastroUsuarios').on('click', function () {
  toggleCheckboxes(
    ['flAcessarCadastroUsuarios', 'flEditarCadastroUsuarios', 'flInserirCadastroUsuarios', 'flExcluirCadastroUsuarios'],
    'iconCadastroUsuarios'
  );
});

$('#btnCadastroGruposUsuarios').on('click', function () {
  toggleCheckboxes(
    ['flAcessarCadastroGruposUsuarios', 'flEditarCadastroGruposUsuarios', 'flInserirCadastroGruposUsuarios', 'flExcluirCadastroGruposUsuarios'],
    'iconGruposUsuarios'
  );
});

// $('#btnControleAcessos').on('click', function () {
//   toggleCheckboxes(
//     ['flAcessarControleAcessos', 'flEditarControleAcessos', 'flInserirControleAcessos', 'flExcluirControleAcessos'],
//     'iconControleAcessos'
//   );
// });

$('#btnControleAcessos').on('click', function () {
  toggleCheckboxes(
    ['flAcessarControleAcessos', 'flEditarControleAcessos'],
    'iconControleAcessos'
  );
});

$('#btnAnimal').on('click', function () {
  toggleCheckboxes(
    ['flAcessarAnimal', 'flEditarAnimal', 'flInserirAnimal', 'flExcluirAnimal'],
    'iconAnimal'
  );
});

$('#btnTipoAnimal').on('click', function () {
  toggleCheckboxes(
    ['flAcessarTipoAnimal', 'flEditarTipoAnimal', 'flInserirTipoAnimal', 'flExcluirTipoAnimal'],
    'iconTipoAnimal'
  );
});

$('#btnEspecie').on('click', function () {
  toggleCheckboxes(
    ['flAcessarEspecie', 'flEditarEspecie', 'flInserirEspecie', 'flExcluirEspecie'],
    'iconEspecie'
  );
});

$('#btnRaca').on('click', function () {
  toggleCheckboxes(
    ['flAcessarRaca', 'flEditarRaca', 'flInserirRaca', 'flExcluirRaca'],
    'iconRaca'
  );
});

$('#btnMunicipio').on('click', function () {
  toggleCheckboxes(
    ['flAcessarMunicipio', 'flEditarMunicipio', 'flInserirMunicipio', 'flExcluirMunicipio'],
    'iconMunicipio'
  );
});

$('#btnBairro').on('click', function () {
  toggleCheckboxes(
    ['flAcessarBairro', 'flEditarBairro', 'flInserirBairro', 'flExcluirBairro'],
    'iconBairro'
  );
});

$('#btnLogradouro').on('click', function () {
  toggleCheckboxes(
    ['flAcessarLogradouro', 'flEditarLogradouro', 'flInserirLogradouro', 'flExcluirLogradouro'],
    'iconLogradouro'
  );
});

// $('#btnRelatorios').on('click', function () {
//   toggleCheckboxes(
//     ['flAcessarRelatorios', 'flEditarRelatorios', 'flInserirRelatorios', 'flExcluirRelatorios'],
//     'iconRelatorios'
//   );
// });

$('#btnRelatorios').on('click', function () {
  toggleCheckboxes(
    ['flAcessarRelatorios'],
    'iconRelatorios'
  );
});

function toggleCheckboxes(checkboxIds, iconId) {
  let checkboxes = checkboxIds.map(id => document.getElementById(id));
  let allChecked = checkboxes.every(checkbox => checkbox.checked);
  let icon = document.getElementById(iconId);

  if (allChecked) {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    icon.className = 'bi bi-square';
  } else {
    checkboxes.forEach(checkbox => checkbox.checked = true);
    icon.className = 'bi bi-check-square';
  }
}
