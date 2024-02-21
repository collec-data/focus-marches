$(document).ready(function () {

  // vider
  $('input').val("");
  $('select').prop("selectedIndex", 0);

  var date_min = '';
  var date_max = '';

  // var francais =
  // {
  //   "sProcessing":     "Traitement en cours...",
  //   "sSearch":         "Filtrer les résultats&nbsp;",
  //   "sLengthMenu":     "Afficher _MENU_ marchés",
  //   "sInfo":           "Affichage du marché _START_ &agrave; _END_ sur _TOTAL_ marchés",
  //   "sInfoEmpty":      "Affichage du marché 0 &agrave; 0 sur 0 marchés",
  //   "sInfoFiltered":   "(filtr&eacute; de _MAX_ marchés au total)",
  //   "sInfoPostFix":    "",
  //   "sLoadingRecords": "Chargement en cours...",
  //   "sZeroRecords":    "Aucun marché &agrave; afficher",
  //   "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
  //   "oPaginate": {
  //     "sFirst":      "Premier",
  //     "sPrevious":   "Pr&eacute;c&eacute;dent",
  //     "sNext":       "Suivant",
  //     "sLast":       "Dernier"
  //   },
  //   "oAria": {
  //     "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
  //     "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
  //   }
  // };

  // demarrer la table
  // format € https://datatables.net/manual/data/renderers#Number-helper
  var tableUI = $('#tableUI').DataTable({
    "responsive": true,
    "dom": '<"wrapper"Bfltip>',
    "language": francais,
    "columns": [
      { "data": "id", "orderable": false, "width": "5%" },
      { "data": "code_cpv", "width": "25%" },
      { "data": "acheteur", "width": "30%" },
      { "data": "titulaire", "width": "20%" },
      { "data": "date", "orderable": false, "width": "10%" },
      // render: $.fn.dataTable.render.number( ' ', '.', 0, '', ' mois' ) },
      {
        "data": "montant", "width": "10%",
        render: $.fn.dataTable.render.number(' ', '.', 0, '', '€')
      }
    ],
    "paging": true,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
    "order": [[2, "asc"], [3, "asc"]]
  });



  /* ---------------------------------------------
  | Recherche                                    |
  ------------------------------------------------
  */
  var recherche = function (type, valeur) {
    /* Contrôle des montants min et max */
    if (parseInt($('#in_montant_min').val()) >= parseInt($('#in_montant_max').val())) {
      alert("Le montant minimum est égal ou supérieure au montant maximum.\nVeuillez choisir un montant inférieur.");
      return;
    }

    /* Contrôle des durées min et max */
    if (parseInt($('#in_duree_min').val()) >= parseInt($('#in_duree_max').val())) {
      alert("La durée minimum est égal ou supérieure à la durée maximum.\nVeuillez choisir une durée inférieure.");
      return;
    }

    /* Contrôle des dates min et max */
    date_min = new Date($('#in_date_min').val());
    date_max = new Date($('#in_date_max').val());

    if (date_min >= date_max) {
      alert("La date de début est égal ou supérieure à la date de fin. \nVeuillez choisir une date antérieure.");
      return;
    }

    $('#rechercheBouton').addClass('is-loading');

    switch (type) {
      case null:
        var url = "data/getRecherche.php?code_cpv=" + $('#in_code_cpv').val() +
          "&libelle_cpv=" + $('#in_libelle_cpv').val() +
          "&acheteur=" + ($('#in_id_acheteur').val() || 0) +
          "&titulaire=" + ($('#in_id_titulaire').val() || 0) +
          "&lieu=" + $('#in_lieu').val() +
          "&objet=" + $('#in_objet').val() +
          "&montant_min=" + ($('#in_montant_min').val() || 0) +
          "&montant_max=" + ($('#in_montant_max').val() || 0) +
          "&duree_min=" + ($('#in_duree_min').val() || 0) +
          "&duree_max=" + ($('#in_duree_max').val() || 0) +
          "&date_min=" + ($('#in_date_min').val() || 0) +
          "&date_max=" + ($('#in_date_max').val() || 0) +
          "&forme_prix=" + $('#in_forme_prix').val() +
          "&nature=" + $('#in_nature').val() +
          "&procedure=" + $('#in_procedure').val();
        break;

      case 'acheteur':
        var url = "data/getRecherche.php?code_cpv=&libelle_cpv=&titulaire=0&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=0&date_max=0&forme_prix=0&nature=0&procedure=0&acheteur=" + valeur;
        break;

      case 'titulaire':
        var url = "data/getRecherche.php?code_cpv=&libelle_cpv=&acheteur=0&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=0&date_max=0&forme_prix=0&nature=0&procedure=0&titulaire=" + valeur;
        break;

      case 'cpv':
        var url = "data/getRecherche.php?libelle_cpv=&titulaire=0&acheteur=0&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=0&date_max=0&forme_prix=0&nature=0&procedure=0&code_cpv=" + valeur;
        break;
    }

    /* Charger données
    function ajax.url().load( callback, resetPaging ) */
    tableUI.ajax.url(url).load(function () {
      $('#rechercheBouton').removeClass('is-loading');

      // A-t-on des données ?
      if (tableUI.data().length === 0) {
        $('#rechercheSansResultats').css('display', 'block');
        $('#rechercheResultats').css('display', 'none');
      }
      else {
        $('#rechercheSansResultats').css('display', 'none');
        $('#rechercheResultats').css('display', 'block');

        //// Récupérer les données de la table et les convertir pour le graphique
        createTimeline(tableUI);
      }

    });

  };


  /* ---------------------------------------------
  | createTimeline                              |
  -----------------------------------------------
  Récupérer les données de la table et les convertir pour le graphique
  */
  var createTimeline = function (t) {
    var x_serv = [], y_serv = [], text_serv = [];
    var x_trav = [], y_trav = [], text_trav = [];
    var x_four = [], y_four = [], text_four = [];
    var moyenne = 0, moyenne_x = [], moyenne_y = [];
    var nb_marches = 0, montant_total = 0;
    var marches_sans_date = 0;
    var montant_max = 0;

    t.data().each(function (d) {
      //// Stats

      // marchés qui n'ont pas de date
      if (d.date_notification === '0000-00-00') marches_sans_date++;

      // nombre de marchés
      nb_marches++;

      // cumul des montants
      montant_total += parseInt(d.montant);

      // montant max
      if (parseInt(d.montant) > montant_max) {
        montant_max = parseInt(d.montant);
      }

      // moyenne_x stocke les dates. Cela servira au axe et au kpi de la période
      if (moyenne_x.indexOf(d.date_notification) === -1) {
        moyenne_x.push(d.date_notification);
      }


      switch (d.categorie) {
        case 'Fournitures':
          x_four.push(d.date_notification);
          y_four.push(parseInt(d.montant));
          text_four.push(
            d.acheteur + "<br>" + d.libelle_cpv + "<br>"
            + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
          );
          break;

        case 'Travaux':
          x_trav.push(d.date_notification);
          y_trav.push(parseInt(d.montant));
          text_trav.push(
            d.acheteur + "<br>" + d.libelle_cpv + "<br>"
            + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
          );
          break;

        case 'Services':
          x_serv.push(d.date_notification);
          y_serv.push(parseInt(d.montant));
          text_serv.push(
            d.acheteur + "<br>" + d.libelle_cpv + "<br>"
            + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
          );
          break;

      }
    }); // each

    // stats : moyenne
    moyenne = (montant_total / nb_marches).toFixed(0);
    if (isNaN(moyenne)) {
      moyenne = 0;
    }

    // stats medianne
    function median(values) {
      values.sort(function (a, b) { return a - b; });

      if (values.length === 0) return 0

      var half = Math.floor(values.length / 2);

      if (values.length % 2) return values[half];
      else return (values[half - 1] + values[half]) / 2.0;
    }

    // stats UI
    $('#kpi-nb-marches').html(new Intl.NumberFormat('fr-FR').format(nb_marches));
    $('#kpi-montant-total').html(new Intl.NumberFormat('fr-FR').format(montant_total) + " €");
    $('#kpi-moyenne').html(new Intl.NumberFormat('fr-FR').format(moyenne) + " €");

    if (marches_sans_date === 0) {
      $('#marches-sans-date').html(""); // vider des vieux messages si tout va bien !
    }

    if (marches_sans_date === 1) {
      $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a un marché dont la date n'a pas été saisie et qui n'est pas affiché dans le graphique.</p>");
    }

    if (marches_sans_date > 1) {
      $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a " + marches_sans_date + " marchés dont la date n'a pas été saisie et qui ne sont pas affichés dans le graphique.</p>");
    }


    // stats : array dimmension y
    for (i in moyenne_x) {
      moyenne_y.push(moyenne);
    }

    // stats : période. Supprimer les dates non remplies
    // moyenne_x stocke les dates pour axe et kpi de la période
    // si pas de date_min et max saisies dans le formulaire
    // on les calcule à partir des dates des marchés
    if (date_min == undefined || date_max == undefined) {
      var periode_arr = moyenne_x.filter(function (v, i, a) {
        return v != '0000-00-00';
      });
      periode_arr = periode_arr.sort();
      var date_min = new Date(periode_arr.shift());
      var date_max = new Date(periode_arr.pop());
    }

    var diff = getMonthDiff(date_min, date_max);

    if (isNaN(diff) || diff == 0) {
      periode = "< 1 mois";
    }
    else {
      periode = diff + " mois";
    }
    $('#kpi-periode').html(periode);

    // montant max
    $('#kpi-max').html(new Intl.NumberFormat('fr-FR').format(montant_max) + " €");


    // taille des cercles
    var size_bubble = 20;
    if (t.data().length > 20) size_bubble = 14;
    if (t.data().length > 40) size_bubble = 13;
    if (t.data().length > 60) size_bubble = 12;
    if (t.data().length > 80) size_bubble = 11;
    if (t.data().length > 100) size_bubble = 10;

    // opacité des cercles
    var opacity = 0.7;

    var trace_serv = {
      name: 'Services',
      x: x_serv,
      y: y_serv,
      mode: 'markers',
      marker: {
        opacity: opacity,
        size: size_bubble,
        color: 'rgb(44, 160, 101)',
        line: {
          color: 'rgb(255, 255, 255)',
          width: 1
        },
        symbol: "square"
      },
      text: text_serv,
      hoverinfo: 'text' /// ne pas afficher X & Y
    };

    var trace_trav = {
      name: 'Travaux',
      x: x_trav,
      y: y_trav,
      mode: 'markers',
      marker: {
        opacity: opacity,
        size: size_bubble,
        color: 'rgb(93, 164, 214)',
        line: {
          color: 'rgb(255, 255, 255)',
          width: 1
        },
        symbol: "star-diamond"
      },
      text: text_trav,
      hoverinfo: 'text'  /// ne pas afficher X & Y
    };

    var trace_four = {
      name: 'Fournitures',
      x: x_four,
      y: y_four,
      mode: 'markers',
      marker: {
        opacity: opacity,
        size: size_bubble,
        color: 'rgb(255, 144, 14)',
        line: {
          color: 'rgb(255, 255, 255)',
          width: 1
        },
      },
      text: text_four,
      hoverinfo: 'text'  /// ne pas afficher X & Y
    };

    var moyenne_text = "Moyenne: " + new Intl.NumberFormat('fr-FR').format(parseInt(moyenne_y)) + " €";
    var trace_moyenne = {
      name: 'Moyenne',
      mode: 'lines',
      x: moyenne_x,
      y: moyenne_y,
      line:
      {
        shape: 'linear',
        dash: 'dot'
      },
      type: 'scatter',
      text: moyenne_text,
      hoverinfo: 'text' /// ne pas afficher X & Y
    }

    var data = [trace_serv, trace_trav, trace_four, trace_moyenne];

    var layout = {
      showlegend: true,
      legend: { bgcolor: '#fff', bordercolor: '#f5f5f5', borderwidth: "1" },
      xaxis: { title: { text: "DATE", font: { size: 16, color: '#111' } } },
      yaxis: { hoverformat: "-.2r€", title: { text: "MONTANT (€)", font: { size: 16, color: '#111' } } },
      autosize: true,
      height: 600,
      /*width: 920,*/
      autosize: true,
      hovermode: 'closest'
    };



    Plotly.newPlot('rechercheTempChart', data, layout, optionsPlotly);
  };

  /* ---------------------------------------------
  | Fenêtre modal                               |
  ---------------------------------------------
  */
  $('#tableUI').on('click', ".voirMarche", function () {
    $('#modalMessage').css('display', 'none');
    $('#enCharge').css('display', 'block');
    $('#modalMarche').addClass('is-active');
    var id = $(this).attr("data-id");

    $.ajax({
      url: "data/getMarcheJSON.php",
      type: 'POST',
      data: 'id=' + id,
      dataType: 'html',
      success: function (data, statut) {
        data = JSON.parse(data);
        $('#m_id').html(data.m_id);
        $('#m_cpv_code').attr("data-cpv", data.m_cpv_code);
        $('#m_cpv_libelle').html(data.m_cpv_libelle);
        $('#m_acheteur').html(data.m_acheteur);
        $('#m_acheteur_siret').html(data.m_acheteur_siret);
        $('#m_acheteur_btn').attr('data-id-acheteur', data.m_acheteur_btn);
        $('#m_acheteur_a').html('<a href="acheteur.php?i=' + data.m_acheteur_btn + '"><i class="fas fa-link"></i>&nbsp;Page de l\'acheteur</a>');
        $('#m_titulaire').html(data.m_titulaire);
        $('#m_titulaire_btn').attr('data-id-titulaire', data.m_titulaire_btn);
        $('#m_titulaire_a').html('<a href="titulaire.php?i=' + data.m_titulaire_btn + '"><i class="fas fa-link"></i>&nbsp;Page du titulaire</a>');
        $('#m_titulaire_siret').html(data.m_titulaire_siret);
        $('#m_procedure').html(data.m_procedure);
        $('#m_nature').html(data.m_nature);
        $('#m_forme_prix').html(data.m_forme_prix);
        $('#m_date_notification').html(data.m_date_notification);
        $('#m_duree').html(data.m_duree);
        $('#m_montant').html(data.m_montant);
        $('#m_lieu').html(data.m_lieu);
        $('#m_objet').html(data.m_objet);

        $('#enCharge').css('display', 'none');
        $('#modalMessage').css('display', 'block');
      },
      error: function (resultat, statut, erreur) {
        $('#enCharge').css('display', 'none');
        $('#modalMessage').html("<p>C'est assez génant, mais quelque chose n'a pas fonctionné ...</p>");
      }
    });
  });

  //// Vider les details du marché lors de la fermeture
  var champsDetail = ['#m_id', '#m_cpv_libelle', '#m_acheteur', '#m_acheteur_siret',
    '#m_titulaire', '#m_titulaire_siret', '#m_procedure',
    '#m_nature', '#m_forme_prix', '#m_date_notification', '#m_duree',
    '#m_montant', '#m_lieu', '#m_objet'];

  var viderDetails = function () {
    $('#m_cpv_code').attr("data-cpv", '');
    $('#m_acheteur_btn').attr('data-id-acheteur', '');
    $('#m_titulaire_btn').attr('data-id-titulaire', '');
    for (i in champsDetail) {
      $(champsDetail[i]).empty();
    }

  };

  //// Lancer une recherche
  $('#modalMarche').on('click', '.plus-acheteur', function () {
    $('#modalMarche').removeClass('is-active');
    $('#enCharge').css('display', 'block');
    recherche('acheteur', $(this).attr('data-id-acheteur'));
    viderDetails();
  });
  $('#modalMarche').on('click', '.plus-titulaire', function () {
    $('#modalMarche').removeClass('is-active');
    $('#enCharge').css('display', 'block');
    recherche('titulaire', $(this).attr('data-id-titulaire'));
    viderDetails();
  });
  $('#modalMarche').on('click', '.plus-cpv', function () {
    $('#modalMarche').removeClass('is-active');
    $('#enCharge').css('display', 'block');
    recherche('cpv', $(this).attr('data-cpv'));
    viderDetails();
  });

  //// Fermer modal
  $('.modal-card .delete, .modal-background, #ferme-marche').on('click', function () {
    $('#modalMarche').removeClass('is-active');
    $('#enCharge').css('display', 'block');
    viderDetails();
  });


  //// Recherche
  $('#rechercheBouton').on('click', function () {
    recherche(null, null); // aucune restriction à la recherche
  });



  /* ---------------------------------------------
  | Auto complétion                               |
  ------------------------------------------------
  */
  var ajaxSelect = function (url, id_visible, id_cachee, id_select) {
    $(id_visible).on('keyup', function () {
      // valeralos id se borramos
      if ($(id_visible).val() === "") {
        $(id_cachee).val("");
      }

      if ($(id_visible).val().length > 0) {
        $.ajax({
          url: url,
          type: 'POST',
          data: 'entite=' + $(id_visible).val(),
          dataType: 'html',
          success: function (data, statut) {
            $(id_select).html(data);
            $(id_select).css("display", "block");
            // click sur élement de la liste ajax
            $(id_select + ' li').on('click', function () {
              $(id_cachee).val($(this).attr("class"));
              $(id_visible).val($(this).html());
              $(id_select).html("");
              $(id_select).css("display", "none");
            });
          },
          error: function (resultat, statut, erreur) {
            $(id_select).html('<p>Pas de résultats</p>');
          }
        });
      }
    });
  };


  // Auto complétion acheteurs
  ajaxSelect('data/getAcheteurs.php', '#in_nom_acheteur', '#in_id_acheteur', '#acheteur_select');

  // Auto complétion titulaires
  ajaxSelect('data/getTitulaires.php', '#in_denomination_sociale', '#in_id_titulaire', '#denomination_select');

  //// toggle aide recherche
  $('#aideRechercheButton').on('click', function () {
    $('#aideRecherche').toggle();
  });

  //// toggle aide charte
  $('#rechercheTempAide').on('click', function () {
    $('#rechercheTempContenu').toggle();
  });

  //// toggle options
  $('#rechercheSimple').on('click', function () {
    if ($('#rechercheSimple').hasClass('is-white')) {
      $('#rechercheOptions').toggle();
      $('#rechercheSimple').removeClass('is-white');
      $('#rechercheSimple').addClass('is-info');
      $('#rechercheAvancee').removeClass('is-info');
      $('#rechercheAvancee').addClass('is-white');
    }
  });

  $('#rechercheAvancee').on('click', function () {
    if ($('#rechercheAvancee').hasClass('is-white')) {
      $('#rechercheOptions').toggle();
      $('#rechercheAvancee').removeClass('is-white');
      $('#rechercheAvancee').addClass('is-info');
      $('#rechercheSimple').removeClass('is-info');
      $('#rechercheSimple').addClass('is-white');
    }
  });
});
