<?php

include('inc/localization.php');
$page = "acheteur";
require_once('data/connect.php');
require_once('data/model.php');
require_once('data/validateurs.php');


///// Sécurisation
$secured = false;
if (is_numeric($_GET['i']))
  $secured = true;

if(isset($_GET['hide_filter']) && is_bool($_GET['hide_filter']) && $_GET['hide_filter'] == true){
  $secured = true;
}

if (isset($_GET['date_min']) && is_date($_GET['date_min']) && $secured == true) {
  $date_min = $_GET['date_min'];
  $secured = true;
}

if (isset($_GET['date_max']) && is_date($_GET['date_max']) && $secured == true) {
  $date_min = $_GET['date_min'];
  $secured = true;
}

if ($secured == true) {
  $hide_filter = false;
  if($_GET['hide_filter'] == true){
    $hide_filter = true;
  }
  $id = $_GET['i'];
  $nom = getNom($connect, $id);
  $title = "Acheteur | Données essentielles du profil d'acheteur " . $nom;
  $desc = "Acheteur | Données essentielles du profil d'acheteur " . $nom;
  $date_min = isset($_GET['date_min']) ?  $_GET['date_min'] : null;
  $date_max = isset($_GET['date_max']) ? $_GET['date_max'] : null;

  include('inc/head.php');
  include('inc/config.php');
  $connect->set_charset("utf8");
  ?>
  <!-- entre heads : ajouter extra css , ... -->
  <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
  <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
  <?php
  include('inc/nav.php');

  //mise à jour périodicité si sélection de date min et max, surcharge la périodicité par défaut présente dans config.php
  if(isset($date_min)) {
  $debut = new DateTime($date_min);
  }
  if(!isset($date_max)) {
    $fin = new DateTime(date('Y-m-d'));
  } else {
    $fin = new DateTime($date_max);
  }
  $fin = new DateTime($date_max);
  $interval = $debut->diff($fin);
  $yearsInMonths = $interval->format('%r%y') * 12;
  $months = $interval->format('%r%m');
  $nb_mois = $yearsInMonths + $months;

  $donnees_a_partir_du = $formatter->format(new DateTime($date_min));

  $kpi = getKPI($connect, $id, $nb_mois, 0, $date_min, $date_max);
  $marches = getDatesMontantsLieu($connect, $id, $nb_mois, $date_min, $date_max);
  $sirene = getDataSiretAcheteur($connect, $id);
  $revenuMoyenNational = getMedianeNiveauVie($connect);

  $default_value_date_min = isset($date_min) ? $date_min : null;
  $default_value_date_max = isset($date_max) ? $date_max : "";

  $hidden_filter = $hide_filter == true ? "hidden"  : "";

  $url = strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
  $iframe_code = "<iframe ";
  $iframe_code .= "src=\"$url?i=";
  $iframe_code .= $id;
  $iframe_code .= isset($date_min) ? "&date_min=" . $date_min : "";
  $iframe_code .= isset($date_max) ? "&date_max=" . $date_max : "";
  $iframe_code .= "&hide_filter=" . ($hide_filter == true ? "true" : "false" );
  $iframe_code .= "\" ";
  $iframe_code .= "referrerpolicy=\"strict-origin-when-cross-origin\" ";
  $iframe_code .= "style=\"border: 0;\" ";
  $iframe_code .= "title=\"Marque blanche focus-marches\" width=\"100%\" height=\"600px\">";
  $iframe_code .= "</iframe>";

}

// L"affichage de certains éléments dépend de si on est face à une collectivité ou pas
$colter = false;
if ($sirene['categorieJuridiqueUniteLegale'] === '7210') {
  $colter = true;
}

?>
<div id="main">

  <div class="container">
    <div class="columns">
      <div class="column ">
        <h1 class='title is-clearfix' id='h1Fixe'> <span>Tableau de bord de l'acheteur :</span><br><b>
            <?php echo $nom; ?>
          </b></h1>
      </div>
      <div id="dates" class="column has-text-right">
        <div class='tags has-addons'>
          <span class='tag is-light'>Contrats conclus à partir du </span>
          <span class='tag is-warning'>
            <?php echo $donnees_a_partir_du; ?>
          </span>
        </div>
        <div class='tags has-addons'>
          <span class='tag is-light'>Données mises à jour le </span>
          <span class='tag is-warning'>
            <?php echo $donnees_mises_a_jour; ?>
          </span>
        </div>
      </div>
    </div>
    <p>Cette page vous présente les données essentielles du profil d'acheteur de <b>
        <?php echo $nom; ?>
      </b>, enrichies avec des données complémentaires.</p>

      <div class="filtreDates" <?php echo $hidden_filter ?>>
        <div class="columns">
            <div class="column">
              <label>Date min</label>
              <input id="in_date_min" type="date" value=<?=$default_value_date_min?> pattern="\d{4}-\d{2}-\d{2}">
            </div>
            <div class="column">
              <label>Date max</label>
              <input id="in_date_max" type="date" value=<?=$default_value_date_max?> pattern="\d{4}-\d{2}-\d{2}">
            </div>
            <button id="filtrerBoutonAcheteur" class="button is-info button-filtre-date" type="button" role="button"
              aria-label="search">Filtrer</button>
        </div>
      </div>

    <?php
    include('widget-acheteur-localisation.php');
    ?>

    <?php
    include('widget-acheteur-indicateurs.php');
    ?>

    <?php
    include('widget-acheteur-distribution.php');
    ?>

    <?php
    include('widget-acheteur-qui-realise.php');
    ?>

    <?php
    include('widget-acheteur-distrib-temporelle.php');
    ?>

    <?php
    include('widget-acheteur-tous-marches.php');
    ?>

    <?php
    include('widget-acheteur-nature.php');
    ?>

    <?php
    include('widget-acheteur-procedure-suivi.php');
    ?>

    <div id="integration-iframe" class="container">
      <h3>Intégrer à son site</h3>
      <p>Copier le code ci-dessous pour intégrer cette page à votre site internet.</p>
      <textarea class="textarea"><?php echo $iframe_code; ?></textarea>
    </div>





  </div> <!-- ./ main -->

  <?php include('js/common-js.php'); ?>
  <script type="text/javascript">

    // Filtre date
    $('#filtrerBoutonAcheteur').on('click', function () {
      $('filtrerBoutonAcheteur').addClass('is-loading');

        /* Contrôle des dates min et max */
      const date_min = $('#in_date_min').val();
      const date_max = $('#in_date_max').val();

      if (date_min >= date_max && date_max !== '') {
        alert("La date de début est égal ou supérieure à la date de fin. \nVeuillez choisir une date antérieure.");
        return;
      }

      let dates_selection = '';
      if (date_min) {
        dates_selection += `&date_min=${date_min}`;
      }
      if (date_max) {
        dates_selection += `&date_max=${date_max}`;
      }

      window.location.href = `acheteur.php?i=<?php echo $id; ?>${dates_selection}`;
  });


    // Clipboard

    var clipboard = new ClipboardJS('.btnCopy');

    clipboard.on('success', function (e) {
      toastr.options = {
        "positionClass": "toast-bottom-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "20",
        "hideDuration": "50",
        "timeOut": "500",
        "extendedTimeOut": "500",
      }
      toastr.success('<b>Copié !</b>')

    });

    clipboard.on('error', function (e) {
      toastr["error"]("<b>Failed!</b")
    });
  </script>
  <script src="assets/jquery/jquery-3.3.1.min.js"></script>


  <!-- <script src="assets/datatables/datatables.min.js"></script> -->
  <script src="assets/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/datatables/dataTables.buttons.min.js"></script>
  <script src="assets/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
  <script src="assets/datatables/buttons.flash.min.js"></script>
  <script src="assets/datatables/jszip.min.js"></script>
  <script src="assets/datatables/pdfmake.min.js"></script>
  <script src="assets/datatables/vfs_fonts.js"></script>
  <script src="assets/datatables/buttons.html5.min.js"></script>
  <script src="assets/datatables/buttons.print.min.js "></script>

  <script>
    $(document).ready(function () {

      // Display header / footer /  integration-iframe if not in an iFrame.
      if (window.top != window.self) {
        // Finally show header.
        document.getElementById("navWrap").hidden = true;
        document.getElementById("integration-iframe").hidden = true;
        document.getElementById("footerWrap").hidden = true;
      }


      //// Tabs
      $('.tabContainer').hide();
      $('.tabContainer:first').show();
      $('.tabs li a').click(function () {
        var t = $(this).attr('id');
        if (!$(this).hasClass('is-active')) {
          $('.tabs li').removeClass('is-active');
          $(this).parent().addClass('is-active')
          $('.tabContainer').hide();
          $('#' + t + 'C').fadeIn('slow');
        }
      });

    }); // document ready
  </script>

  <?php include('inc/footer.php'); ?>