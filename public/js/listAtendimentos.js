var dataTableAnimais;
$(document).ready(function () {
  if (sessionStorage.getItem("notificarSucesso") === "true") {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem("notificarSucesso");
  }

  dataTableAtendimentos = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true,
    language: {
      url: "/veterinariaUNESC/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinariaUNESC/server/atendimentos/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $("#pesquisaCodigoAnimal").val();
        d.columns[1].search.value = $("#pesquisaNomeAnimal").val();
        d.columns[2].search.value = $("#pesquisaDescricaoTipoAnimal").val();
        d.columns[3].search.value = $("#pesquisaNomeDonoAnimal").val();
        d.columns[4].search.value = $("#pesquisaEspecieAnimal").val();
        d.columns[5].search.value = $("#pesquisaRacaAnimal").val();
      },
      dataSrc: function (json) {
        json.draw = json.RETURN.draw;
        json.recordsTotal = json.RETURN.recordsTotal;
        json.recordsFiltered = json.RETURN.recordsFiltered;
        return json.RETURN.data;
      },
    },
    columns: [
      { data: "CD_FICHA_LPV" },
      { data: "DT_FICHA" },
      { data: "nm_animal" },
      { data: "nm_tipo_animal" },
      { data: "nm_especie" },
      { data: "nm_raca" },
      { data: "sexo" },
      { data: "nm_dono" },
      { data: "nm_veterinario" },
      { data: "cidade_propridade" },
      { data: "DS_MATERIAL_RECEBIDO" },
      { data: "DS_DIAGNOSTICO_PRESUNTIVO" },
      { data: "FL_AVALIACAO_TUMORAL_COM_MARGEM" },
      { data: "DS_EPIDEMIOLOGIA_HISTORIA_CLINICA" },
      { data: "DS_LESOES_MACROSCOPICAS" },
      { data: "DS_LESOES_HISTOLOGICAS" },
      { data: "DS_DIAGNOSTICO" },
      { data: "DS_RELATORIO" },
    ],
    processing: true,
    serverSide: true,
    createdRow: function (row, data, dataIndex) {
      $("td", row).each(function (index) {
        var cell = $(this);
        var recordId = data.CD_FICHA_LPV;
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

  $(
    "#pesquisaCodigoAnimal, #pesquisaNomeAnimal, #pesquisaDescricaoTipoAnimal, #pesquisaNomeDonoAnimal, #pesquisaEspecieAnimal, #pesquisaRacaAnimal"
  ).on("keyup clear input", function () {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function () {
      dataTableAnimais.draw();
    }, 2000);
  });
});

function openCadastro(id = "") {
  Loading.on();
  var form = $(
    '<form action="/veterinariaUNESC/paginas/fichaLPV" method="post"><input type="hidden" name="id" value="' +
      id +
      '"></form>'
  );
  $("body").append(form);
  form.submit();
}
