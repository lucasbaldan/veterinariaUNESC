var dataTablePessoas;
$(document).ready(function () {
  dataTablePessoas = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/usuarios/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoUsuario').val();
        d.columns[1].search.value = $('#pesquisaNomeUsuario').val();
        d.columns[2].search.value = $('#pesquisaNomeGrupoUsuarios').val();
        d.columns[3].search.value = $('#pesquisaUsuarioAtivo').val();
      },
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
    },
    columns: [
      { data: "CD_USUARIO" },
      { data: "NM_USUARIO" },
      { data: "NM_GRUPO_USUARIOS" },
      { data: "FL_ATIVO" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var Id = data.CD_USUARIO;
        
        var cellText = cell.text();
        cell.html(
          '<span class="dataTable-item" onclick="openCadastro(' +
          Id +
          ')">' +
          cellText +
          "</span>"
        );
      });
    },
  });

    $('#pesquisaCodigoUsuario, #pesquisaNomeUsuario, #pesquisaNomeGrupoUsuarios, #pesquisaUsuarioAtivo').on('keyup clear input', function() {
      if (this.timer) clearTimeout(this.timer);
      this.timer = setTimeout(function() {
        dataTablePessoas.draw();
      }, 500);
    });
});

function openCadastro(id = '') {
  Loading.on();
  var form = $('<form action="/veterinariaUNESC/paginas/cadastroUsuarios" method="POST"><input type="hidden" name="id" value="' + id + '"></form>');
  $('body').append(form);
  form.submit();
}