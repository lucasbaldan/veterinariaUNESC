$(document).ready(function () {

    $('#ExpandFicha').click(function () {
        $('.accordion-collapse').each(function () {
            let collapseInstance = new bootstrap.Collapse(this, {
                toggle: false
            });
            collapseInstance.show();
        });
    });
    
      var selectTipoAnimal = new Select2("#select2tipoAnimal", {
        url: "/veterinariaUNESC/server/tipoAnimal/general",
      });
    
      var selectEspecieAnimal = new Select2("#select2especieAnimal", {
        url: "/veterinariaUNESC/server/especie/general",
      });
    
      var selectRacaAnimal = new Select2("#select2racaAnimal", {
        url: "/veterinariaUNESC/server/raca/general",
      });

      selectTipoAnimal.on("change", function (e) {
        var TipoAnimalSelecionado = $(this).val();
        $("#select2especieAnimal").val(null).trigger("change");
    
        if (TipoAnimalSelecionado) {
          selectEspecieAnimal = new Select2("#select2especieAnimal", {
            url: "/veterinariaUNESC/server/especie/general",
            idTipoAnimal: TipoAnimalSelecionado,
          });
          $("#select2especieAnimal").prop("disabled", false);
        } else {
          $("#select2racaAnimal").val(null).trigger("change");
          $("#select2especieAnimal, #select2racaAnimal").prop("disabled", true);
        }
      });
    
      selectEspecieAnimal.on("change", function (e) {
        var EspecieSelecionado = $(this).val();
        $("#select2racaAnimal").val(null).trigger("change");
    
        if (EspecieSelecionado) {
          selectRacaAnimal = new Select2("#select2racaAnimal", {
            url: "/veterinariaUNESC/server/raca/general",
            idEspecie: EspecieSelecionado,
          });
          $("#select2racaAnimal").prop("disabled", false);
        } else {
          $("#select2racaAnimal").prop("disabled", true);
        }
      });

      $("#nrTelefoneProprietario").inputmask("(99) 99999-9999", { autoUnmask: true });

});

$("#alterarAnimalFicha").on("click", function () {
    $("#select2racaAnimal, #animal, #select2tipoAnimal, #select2especieAnimal, #select2racaAnimal, #dsSexo, #idade, #anoNascimento").prop("disabled", false);
    $('#alterouAnimal').val('S');
    if($('#select2TipoAnimal').val() == '') {
        $("#select2especieAnimal").prop("disabled", true);
      }
      if($('#select2especieAnimal').val() == '') {
        $("#select2racaAnimal").prop("disabled", true);
      }

      if ($('#select2tipoAnimal').val() !== '' && $('#select2especieAnimal').val() === '') {
        var TipoAnimalSelecionado = $('#select2tipoAnimal').val();
        selectEspecieAnimal = new Select2("#select2especieAnimal", {
            url: "/veterinariaUNESC/server/especie/general",
            idTipoAnimal: TipoAnimalSelecionado,
          });
          $("#select2especieAnimal").prop("disabled", false);
    }

    if ($('#select2especieAnimal').val() !== '' && $('#select2racaAnimal').val() === '') {
        var EspecieSelecionado = $('#select2especieAnimal').val();
        selectRacaAnimal = new Select2("#select2racaAnimal", {
            url: "/veterinariaUNESC/server/raca/general",
            idEspecie: EspecieSelecionado,
          });
          $("#select2racaAnimal").prop("disabled", false);
    }
});

$("#alterarDonoFicha").on("click", function () {
    $("#donoNaoDeclarado, #nmProprietario, #nrTelefoneProprietario").prop("disabled", false);
    $('#alterouDono').val('S');
});

$("#donoNaoDeclarado").on("change", function () {
    if ($(this).is(":checked")) {
        $("#nmProprietario, #nrTelefoneProprietario").val('');
        $("#nmProprietario, #nrTelefoneProprietario").prop("disabled", true);
    }
  });


  $("#buscaRapidaVeterinario").on("click", function () {
    try {
      Loading.on();

      var ajaxModal = $.ajax({
        url: "/veterinariaUNESC/modais/buscaRapidaPessoa",
        method: "POST",
      });

      var script = $.getScript(
        "/veterinariaUNESC/public/js/buscaRapidaPessoaModal.js"
      );

      $.when(ajaxModal, script)
        .done(function (respostaAjaxModal) {
          bootbox.dialog({
            title: "Busca Rápida - Pessoa",
            size: "extra-large",
            message: respostaAjaxModal[0],
            className: "search-pessoa",
            onShown: function() {constructModalBuscaPessoa();}
          });
        })
        .fail(function (xhr, status, error) {})
        .always(function () {
          Loading.off();
        });
    } catch (e) {
      Loading.off();
    }
  });

  