var dataTableGruposUsuarios;
$(document).ready(function () {
  dataTableGruposUsuarios = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true, 
    language: {
      url: "/veterinaria/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinaria/server/gruposUsuarios/grid",
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
      complete: function(xhr, textStatus) {
        if (xhr.status === 302) {
            var redirectUrl = xhr.getResponseHeader('Location');
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }
    },
    },
    columns: [
      { data: "CD_GRUPO_USUARIOS" },
      { data: "NM_GRUPO_USUARIOS" },
      { data: "FL_ATIVO" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_GRUPO_USUARIOS;
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
      dataTableGruposUsuarios.draw();
    }, 500);
  });

});

function openCadastro($id = null) {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinaria/modais/cadastroGruposUsuarios",
      method: "POST",
      data: { id: $id },
    });

    var script = $.getScript("/veterinaria/public/js/CadastroGrupoUsuariosModal.js?v=" + window.scriptVersao);

    $.when(ajaxModal, script).done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Cadastro dos Grupos de Usuários",
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
