var dataTablePessoas;
$(document).ready(function () {

  if (sessionStorage.getItem('notificarSucesso') === 'true') {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem('notificarSucesso');
}


  dataTablePessoas = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: "/veterinaria/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinaria/server/laboratorios/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoLaboratorios').val();
        d.columns[1].search.value = $('#pesquisaNomeLaboratorios').val();
        d.columns[2].search.value = $('#pesquisaAtivoLaboratorios').val();
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
      { data: "CD_LABORATORIO" },
      { data: "NM_LABORATORIO" },
      { data: "FL_ATIVO" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_LABORATORIO;
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

    $('#pesquisaCodigoLaboratorios, #pesquisaNomeLaboratorios, #pesquisaAtivoLaboratorios').on('keyup clear input', function() {
      if (this.timer) clearTimeout(this.timer);
      this.timer = setTimeout(function() {
        dataTableLaboratorioss.draw();
      }, 500);
    });
});

function openCadastro(id = '') {
  Loading.on();
  var form = $('<form action="/veterinaria/paginas/cadastroLaboratorios" method="post"><input type="hidden" name="id" value="' + id + '"></form>');
  $('body').append(form);
  form.submit();
}