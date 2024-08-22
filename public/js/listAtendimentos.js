var dataTableAtendimentos;
$(document).ready(function () {

  if (sessionStorage.getItem("notificarSucesso") === "true") {
    Notificacao.NotificacaoSucesso();
    sessionStorage.removeItem("notificarSucesso");
  }

  dataTableAtendimentos = $("#gridDataTable").DataTable({
    scrollX: true,
    orderCellsTop: true,
    fixedHeader: true,
    autoWidth: true,
    language: {
      url: "/veterinaria/public/languages/datatablePt-BR.json",
    },
    ajax: {
      url: "/veterinaria/server/atendimentos/grid",
      type: "POST",
      data: function (d) {
        d.columns[0].search.value = $("#pesquisaCodigoAtendimento").val();
        d.columns[1].search.value = $("#pesquisaDataAtendimentoInicio").val()+'|'+$("#pesquisaDataAtendimentoFim").val();
        d.columns[2].search.value = $("#pesquisaNomeAnimalAtendimento").val();
        d.columns[3].search.value = $("#pesquisaEspecieAnimalAtendimento").val();
        d.columns[4].search.value = $("#pesquisaRacaAnimalAtendimento").val();
        d.columns[5].search.value = $("#pesquisaSexoAnimalAtendimento").val();
        d.columns[6].search.value = $("#pesquisaCastrado").val();
        d.columns[7].search.value = $("#pesquisaIdadeAnoInicio").val()+'|'+$("#pesquisaIdadeAnoFim").val();
        d.columns[8].search.value = $("#pesquisaIdadeMesInicio").val()+'|'+$("#pesquisaIdadeMesFim").val();
        d.columns[9].search.value = $("#pesquisaIdadeDiaInicio").val()+'|'+$("#pesquisaIdadeDiaFim").val();
        d.columns[10].search.value = $("#pesquisaDonoAnimalAtendimento").val();
        d.columns[11].search.value = $("#pesquisaVeterinarioAtendimento").val();
        d.columns[12].search.value = $("#pesquisaMunicipioOrigemAtendimento").val();
        d.columns[13].search.value = $("#pesquisaMaterialAtendimento").val();
        d.columns[14].search.value = $("#pesquisaDiagnosticoPresuntivoAtendimento").val();
        d.columns[15].search.value = $("#pesquisaAvalicaoTumoralAtendimento").val();
        d.columns[16].search.value = $("#pesquisaEpidemiologiaAtendimento").val();
        d.columns[17].search.value = $("#pesquisaLesoesMacrocospiasAtendimento").val();
        d.columns[18].search.value = $("#pesquisaLesoesHistologicasAtendimento").val();
        d.columns[19].search.value = $("#pesquisaDiagnosticoAtendimento").val();
        d.columns[20].search.value = $("#pesquisaRelatorioAtendimento").val();
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
      { data: "CD_FICHA_LPV"},
      { data: "DT_FICHA" },
      { data: "NM_ANIMAL" },
      { data: "NM_ESPECIE" },
      { data: "NM_RACA" },
      { data: "SEXO" },
      { data: "CASTRADO" },
      { data: "IDADE_ANIMAL_ANO" },
      { data: "IDADE_ANIMAL_MES" },
      { data: "IDADE_ANIMAL_DIA" },
      { data: "NM_TUTOR" },
      { data: "NM_VETERINARIO" },
      { data: "CIDADE_PROPRIEDADE" },
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
          '<span class="dataTable-item" onclick="editarAtendimento(' +
            recordId +
            ')">' +
            cellText +
            "</span>"
        );
      });
    },
  });

  $("#pesquisaCodigoAtendimento, #pesquisaDataAtendimentoInicio, #pesquisaDataAtendimentoFim, #pesquisaNomeAnimalAtendimento, #pesquisaEspecieAnimalAtendimento, #pesquisaRacaAnimalAtendimento, #pesquisaSexoAnimalAtendimento, #pesquisaDonoAnimalAtendimento, #pesquisaVeterinarioAtendimento, #pesquisaMunicipioOrigemAtendimento, #pesquisaMaterialAtendimento, #pesquisaDiagnosticoPresuntivoAtendimento, #pesquisaAvalicaoTumoralAtendimento, #pesquisaEpidemiologiaAtendimento, #pesquisaLesoesMacrocospiasAtendimento, #pesquisaLesoesHistologicasAtendimento, #pesquisaDiagnosticoAtendimento, #pesquisaRelatorioAtendimento, #pesquisaCastrado, #pesquisaIdadeAnoInicio, #pesquisaIdadeAnoFim, #pesquisaIdadeMesInicio, #pesquisaIdadeMesFim, #pesquisaIdadeDiaInicio, #pesquisaIdadeDiaFim"
).on("keyup clear input", function () {
    if (this.timer) clearTimeout(this.timer);
    this.timer = setTimeout(function () {
      dataTableAtendimentos.draw();
    }, 2000);
  });
});

function openCadastro() {
  try {
    Loading.on();

    var ajaxModal = $.ajax({
      url: "/veterinaria/modais/buscaRapidaAnimal",
      method: "POST",
    });

    var script = $.getScript(
      "/veterinaria/public/js/buscaRapidaAnimalModal.js?v=" + window.scriptVersao
    );

    $.when(ajaxModal, script)
      .done(function (respostaAjaxModal) {
        bootbox.dialog({
          title: "Busca Rápida - Animal",
          size: "extra-large",
          message: respostaAjaxModal[0],
          className: "search-animal",
          onShown: function() {constructModalBuscaAnimal();}
        });
      })
      .fail(function (xhr, status, error) {})
      .always(function () {
        Loading.off();
      });
  } catch (e) {
    Loading.off();
  }
}

function iniciarAtendimento(id) {
  Loading.on();
  if(id == null){
    Notificacao.NotificacaoErro('Houve um erro ao processar o pedido <br><br> Tente novamente mais tarde!');
    return
  }
  var form = $('<form action="/veterinaria/paginas/fichaLPV" method="post"><input type="hidden" name="idAnimal" value="' + id + '"></form>');
  $('body').append(form);
  form.submit();
}

function editarAtendimento(id) {
  Loading.on();
  if(id == null){
    Notificacao.NotificacaoErro('Houve um erro ao processar o pedido <br><br> Tente novamente mais tarde!');
    return
  }
  var form = $('<form action="/veterinaria/paginas/fichaLPV" method="post"><input type="hidden" name="idFicha" value="' + id + '"></form>');
  $('body').append(form);
  form.submit();
}

function gerarCSVAtendimentos() {
  Loading.on();

  let formData = {
    pesquisaCodigoAtendimento: $("#pesquisaCodigoAtendimento").val(),
    pesquisaDataAtendimento: $("#pesquisaDataAtendimentoInicio").val()+'|'+$("#pesquisaDataAtendimentoFim").val(),
    pesquisaNomeAnimalAtendimento: $("#pesquisaNomeAnimalAtendimento").val(),
    pesquisaNomeTipoAnimalAtendimento: $("#pesquisaNomeTipoAnimalAtendimento").val(),
    pesquisaEspecieAnimalAtendimento: $("#pesquisaEspecieAnimalAtendimento").val(),
    pesquisaRacaAnimalAtendimento: $("#pesquisaRacaAnimalAtendimento").val(),
    pesquisaSexoAnimalAtendimento: $("#pesquisaSexoAnimalAtendimento").val(),
    pesquisaDonoAnimalAtendimento: $("#pesquisaDonoAnimalAtendimento").val(),
    pesquisaVeterinarioAtendimento: $("#pesquisaVeterinarioAtendimento").val(),
    pesquisaMunicipioOrigemAtendimento: $("#pesquisaMunicipioOrigemAtendimento").val(),
    pesquisaMaterialAtendimento: $("#pesquisaMaterialAtendimento").val(),
    pesquisaDiagnosticoPresuntivoAtendimento: $("#pesquisaDiagnosticoPresuntivoAtendimento").val(),
    pesquisaAvalicaoTumoralAtendimento: $("#pesquisaAvalicaoTumoralAtendimento").val(),
    pesquisaEpidemiologiaAtendimento: $("#pesquisaEpidemiologiaAtendimento").val(),
    pesquisaLesoesMacrocospiasAtendimento: $("#pesquisaLesoesMacrocospiasAtendimento").val(),
    pesquisaLesoesHistologicasAtendimento: $("#pesquisaLesoesHistologicasAtendimento").val(),
    pesquisaDiagnosticoAtendimento: $("#pesquisaDiagnosticoAtendimento").val(),
    pesquisaRelatorioAtendimento: $("#pesquisaRelatorioAtendimento").val()
  };

  $.ajax({
    url: "/veterinaria/server/atendimentos/gerarCSV",
    method: "POST",
    data: formData,
    xhrFields: {
        responseType: 'blob'
    },
    complete: function(xhr, textStatus) {
      if (xhr.status === 302) {
          var redirectUrl = xhr.getResponseHeader('Location');
          if (redirectUrl) {
              window.location.href = redirectUrl;
          }
      }
  },
    success: function (response) {
        var blob = new Blob([response], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = "atendimentos.csv";
        document.body.appendChild(link); // Para Firefox
        link.click();
        document.body.removeChild(link); // Para Firefox
    },
    error: function (xhr, status, error) {
      Notificacao.NotificacaoAviso("Não é possível Exportar para CSV quando a pesquisa apresenta nenhum resultado");
  },
    complete: function () {
        Loading.off();
    }
});
}
