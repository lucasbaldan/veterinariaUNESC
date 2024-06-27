var dataTableTipoAnimal;
$(document).ready(function () {
  dataTableTipoAnimal = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true, 
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/tipoAnimal/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoGrupoUsuarios').val();
        d.columns[1].search.value = $('#pesquisaNomeGrupoUsuarios').val();
        d.columns[2].search.value = $('#pesquisaAtivoGrupoUsuarios').val();
      },
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
    },
    columns: [
      { data: "cd_grupo_usuarios" },
      { data: "nm_grupo_usuarios" },
      { data: "fl_ativo" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.cd_grupo_usuarios;
        var cellText = cell.text();
        cell.html(
          '<span class="dataTable-item" onclick="openCadastro(' +
            recordId +
            ')">' +
            cellText +
            "</span>"
        );
      });
    },
  });


  $('#pesquisaCodigoGrupoUsuarios, #pesquisaNomeGrupoUsuarios, #pesquisaAtivoGrupoUsuarios').on('keyup clear input', function() {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function() {
      dataTableTipoAnimal.draw();
    }, 2000);
  });

});

function openCadastro($id = null) {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinariaUNESC/modais/cadastroTipoAnimal",
      method: "POST",
      data: { id: $id },
    });

    var script = $.getScript("/veterinariaUNESC/public/js/CadastroTipoAnimalModal.js");

    $.when(ajaxModal, script).done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Cadastro de Tipo de Animal",
          centerVertical: true,
          message: respostaAjaxModal[0],
        });
      })
      .fail(function (xhr, status, error) {
      })
      .always(function () {
        Loading.off();
      });
  } catch (e) {
    Loading.off();
  }
}
