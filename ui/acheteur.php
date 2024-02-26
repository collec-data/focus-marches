<?php

include('inc/localization.php');
$page = "acheteur";
require_once('data/connect.php');
require_once('data/model.php');

///// Sécurisation
$secured = false;
if (is_numeric($_GET['i']))
  $secured = true;

if ($secured == true) {


  $id = $_GET['i'];
  $nom = getNom($connect, $id);
  $title = "Acheteur | Données essentielles du profil d'acheteur " . $nom;
  $desc = "Acheteur | Données essentielles du profil d'acheteur " . $nom;

  include('inc/head.php');
  include('inc/config.php');
  $connect->set_charset("utf8");
  ?>
  <!-- entre heads : ajouter extra css , ... -->
  <link rel="stylesheet" href="assets/leaflet/leaflet.css" />
  <link href="assets/toastr/toastr.min.css" rel="stylesheet" />
  <?php
  require('inc/nav.php');



  $kpi = getKPI($connect, $id, $nb_mois, 0);
  $marches = getDatesMontantsLieu($connect, $id, $nb_mois);
  $sirene = getDataSiretAcheteur($connect, $id);
  $revenuMoyenNational = getMedianeNiveauVie($connect);

  // variables pour integrerPage.php
  $message_info_integration_page = "Copier le code ci-dessous pour intégrer cette page à votre site internet, ainsi les données du tableau de bord seront visibles sur votre site et mises à jour automatiquement.";

  $url = strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
  $iframe_code = "<iframe ";
  $iframe_code .= "src=\"$url?i=";
  $iframe_code .= $id;
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
      <div class="column">
        <h1 class='title is-clearfix' id='h1Fixe'> <span>Tableau de bord de l'acheteur :</span><br><b>
            <?=gettext($nom); ?>
          </b></h1>
      </div>
      <div id="dates" class="column has-text-right">
        <div class='tags has-addons'>
          <span class='tag is-light'>Contrats conclus à partir du </span>
          <span class='tag is-warning'>
            <?=gettext($donnees_a_partir_du); ?>
          </span>
        </div>
        <div class='tags has-addons'>
          <span class='tag is-light'>Données mises à jour le </span>
          <span class='tag is-warning'>
            <?=gettext($donnees_mises_a_jour); ?>
          </span>
        </div>
        <?php include('inc/integrerPage.php'); ?>
    </div>
      </div>


    <p>Cette page vous présente les données essentielles du profil d'acheteur de <b>
      <?=gettext($nom); ?>
      </b>, enrichies avec des données complémentaires.</p>

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

  </div> <!-- ./ main -->

  <?php include('js/common-js.php'); ?>
  <script type="text/javascript">


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

          //// toggle intregration iframe
      $('#integration-iframe').on('click', function () {
          $('#integration-iframe-contenu').toggle();
      });



    }); // document ready
  </script>

  <?php include('inc/footer.php'); ?>