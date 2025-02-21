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
    url: "/veterinaria/server/gruposUsuarios/salvaAcessos",
    method: "POST",
    data: formData,
    success: function (response) {
      if (response.RESULT) {
        // sessionStorage.setItem('notificarSucesso', 'true');
        notificacao.push('Permissões salvas com Sucesso!', 'success');
        // window.location.href = '/veterinaria/paginas/listPessoas';
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
