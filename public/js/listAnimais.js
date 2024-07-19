var dataTableAnimais;
$(document).ready(function () {

  if (sessionStorage.getItem('notificarSucesso') === 'true') {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem('notificarSucesso');
}

  dataTableAnimais = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true, 
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/animais/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $('#pesquisaCodigoAnimal').val();
        d.columns[1].search.value = $('#pesquisaNomeAnimal').val();
        d.columns[2].search.value = $('#pesquisaNomeDonoAnimal').val();
        d.columns[3].search.value = $('#pesquisaEspecieAnimal').val();
        d.columns[4].search.value = $('#pesquisaRacaAnimal').val();
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
      { data: "CD_ANIMAL" },
      { data: "NM_ANIMAL" },
      { data: "NOME_DONO" },
      { data: "ESPECIE_DESCRICAO" },
      { data: "RACA_DESCRICAO" },
      
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_ANIMAL;
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


  $('#pesquisaCodigoAnimal, #pesquisaNomeAnimal, #pesquisaNomeDonoAnimal, #pesquisaEspecieAnimal, #pesquisaRacaAnimal').on('keyup clear input', function() {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function() {
      dataTableAnimais.draw();
    }, 2000);
  });

});

function openCadastro(id = '') {
  Loading.on();
  var form = $('<form action="/veterinariaUNESC/paginas/cadastroAnimais" method="post"><input type="hidden" name="id" value="' + id + '"></form>');
  $('body').append(form);
  form.submit();
}


