<?php

$page = "acheteur";
$title = "Acheteur | Données essentielles du profil d'acheteur ".gettext("Territoires Numériques");
$desc = "Acheteur | Données essentielles du profil d'acheteur ".gettext("Territoires Numériques");

include('inc/head.php');
include('inc/config.php');
include('inc/localization.php');
?>
<!-- entre heads : ajouter extra css , ... -->
<link rel="stylesheet" href="assets/leaflet/leaflet.css" />

<?php
include('inc/nav.php');
require_once('data/connect.php');
require_once('data/model.php');

$connect->set_charset("utf8");

///// Sécurisation
$secured = false;
if (is_numeric($_GET['i'])) $secured = true;

if ($secured == true)
{
  $id = $_GET['i'];
  $nom = getNom($connect, $id);
  $kpi = getKPI($connect, $id, $nb_mois, 0);
  $marches = getDatesMontantsLieu($connect, $id, $nb_mois);
  $sirene = getDataSiretAcheteur($connect, $id);
  $revenuMoyenNational = getMedianeNiveauVie($connect);

  $url=strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?');
  $iframe_code="<iframe ";
  $iframe_code.= "src=\"$url?i=";
  $iframe_code.=$id;
  $iframe_code.="\" ";
  $iframe_code.= "referrerpolicy=\"strict-origin-when-cross-origin\" ";
  $iframe_code.= "style=\"border: 0;\" ";
  $iframe_code.= "title=\"Marque blanche focus-marches\" width=\"100%\" height=\"600px\">";
  $iframe_code.= "</iframe>";

}

// L"affichage de certains éléments dépend de si on est face à une collectivité ou pas
$colter = false;
if ($sirene['categorieJuridiqueUniteLegale'] === '7210')
{
  $colter = true;
}

?>
<div id="main">

  <div class="container">
      <div class="columns">
      <div class="column ">
        <h1 class='title is-clearfix' id='h1Fixe'> <span>Tableau de bord de l'acheteur :</span><br><b><?php echo $nom;?></b></h1>
      </div>
      <div id="dates" class="column has-text-right">
        <div class='tags has-addons'>
          <span class='tag is-light'>Contrats conclus à partir du </span>
          <span class='tag is-warning'><?php echo $donnees_a_partir_du;?></span>
        </div>
        <div class='tags has-addons'>
          <span class='tag is-light'>Données mises à jour le </span>
          <span class='tag is-warning'><?php echo $donnees_mises_a_jour;?></span>
        </div>
      </div>
    </div>

      <p>Cette page vous présente les données essentielles du profil d'acheteur de <b><?php echo $nom;?></b>, enrichies avec des données complémentaires.</p>

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
    <textarea class="textarea"><?php echo $iframe_code;?></textarea>
</div>

<!-- Modal Liste titulaires -->

  <div id="modalListe" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
    <section class="modal-card-body">
      <div id="enCharge">
        <p>Je cherche les données, une seconde ... :)</p>
        <p><img src="img/spinner-wedges.gif"></p>
      </div>
      <div id="modalMessageList">
        <h2 class="modalH2">Liste de fournisseurs <small>(les marchés sont groupés par fournisseur)</small></h2>
        <table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableList"  style="width:100%;margin-top:10px; ">
          <thead>
            <tr>
              <th width="6%">Détails</th>
              <th width="68%">Nom</th>
              <th width="26%">Montant</th>
            </tr>
          </thead>
        </table>
      </div>
      <button id="ferme-modal-liste" class="has-text-centered button is-btn-marron">Fermer</button>
    </section>
  </div><!-- ./modal-card -->
</div><!-- ./modalListe -->




<div id="modalMarche" class="modal mini-fiche-marche">
  <div class="modal-background"></div>
  <div class="modal-card">
     <header class="modal-card-head">
      <p class="modal-card-title"><i class="far fa-handshake pb5"></i> Détails du contrat</p>
        <button id="ferme-marche" class="has-text-centered button is-btn-marron">Fermer</button>
    </header>
    <section class="modal-card-body">
    <div id="enCharge">
      <p>Je cherche les données, une seconde ... :)</p>
      <p><img src="img/spinner-wedges.gif"></p>
    </div>

    <div id="modalMessage">

      <div class="columns">

        <!-- Colonne 1 -->
        <div id="details-1" class="column is-one-quarter">
          <h4>Montant</h4>
          <p id="m_montant" class="roboto l"></p>
          <h4>Durée</h4>
          <p id="m_duree"></p>
          <h4>Lieu d'exécution</h4>
          <p id="m_lieu"></p>
          <h4>Date de notification </h4>
          <p id="m_date_notification"></p>
          <h4>Type de marché</h4>
          <p id="m_nature">Marché de partenariat</p>
          <h4>Procédure</h4>
          <p id="m_procedure">Procédure adaptée</p>
          <h4>Forme de prix</h4>
          <p id="m_forme_prix">Révisable</p>
          <span id="m_id"></span>
        </div>

        <!-- Colonne 2 -->
        <div class="column">

          <div class="columns">
            <!-- Colonne 2 A -->
            <div class="column">
              <h4>Acheteur</h4>
              <p>
                <span id="m_acheteur"></span>
                <span id="m_acheteur_siret" class="siret"></span></p>
            </div>

            <!-- Colonne 2 B -->
            <div class="column">
              <h4>Titulaire</h4>
              <p>
                <span id="m_titulaire"></span> <span id="m_titulaire_siret" class="siret"></span></p>
               <p>
                <span id="m_titulaire_a" class="link"></span>
              </p>
            </div>
          </div><!-- ./Colonnes 2A et 2B -->
          <div id="m_wrap_cpv">
            <h4>Code CPV : </h4>
            <p><span id="m_cpv_code"></span> - <span id="m_cpv_libelle"></span></p>
          </div>
          <h4>Objet</h4>
          <p id="m_objet"></p>
      </div>
    </div>
</div><!-- ./ modalMessage -->
</section>
</div>
</div>

</div> <!-- ./ main -->

<?php include('js/common-js.php');?>
    <script type="text/javascript">



        // $('button').tooltip({
        //     trigger: 'click',
        //     placement: 'bottom'
        // });
        //
        // function setTooltip(message) {
        //     $('button').tooltip('hide')
        //         .attr('data-original-title', message)
        //         .tooltip('show');
        // }
        //
        // function hideTooltip() {
        //     setTimeout(function() {
        //         $('button').tooltip('hide');
        //     }, 1000);
        // }

        // Clipboard

        var clipboard = new ClipboardJS('button');

        clipboard.on('success', function(e) {
            console.log('Copié!');
            // setTooltip('Copié');
            // hideTooltip();
        });

        clipboard.on('error', function(e) {
            console.log('Failed!');
            // setTooltip('Failed!');
            // hideTooltip();
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
$( document ).ready(function() {

    // Display header / footer /  integration-iframe if not in an iFrame.
    if (window.top != window.self) {
        // Finally show header.
        document.getElementById("navWrap").hidden =true;
        document.getElementById("integration-iframe").hidden =true;
        document.getElementById("footerWrap").hidden =true;
    }


  //// Tabs
  $('.tabContainer').hide();
  $('.tabContainer:first').show();
  $('.tabs li a').click(function()
  {
    var t = $(this).attr('id');
    if (! $(this).hasClass('is-active'))
    {
      $('.tabs li').removeClass('is-active');
      $( this ).parent().addClass('is-active')
      $('.tabContainer').hide();
      $('#'+ t + 'C').fadeIn('slow');
    }
  });


  $('#aideCPVLieuxBtn').click(function()
  {
    $('#aideCPVLieux').toggle();
  });

    //// toggle aide charte
    $('#rechercheTempAide').on('click', function ()
    {
      $('#rechercheTempContenu').toggle();
    });




/* --------------------------------------
    Modal liste acheteurs
   --------------------------------------*/
//// Initialiser la table une fois
var tableList = $('#tableList').DataTable({
  "responsive": true,
  "dom": '<"wrapper"Bfltip>',
  "language": francais_neutre,
  "columns": [
    { "data": "details" },
    { "data": "nom" },
    { "data": "montant",
    render: $.fn.dataTable.render.number( ' ', '.', 0, '', '€' ) }
  ],
  "paging": true,
  "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
  "order": [[ 1, "asc" ],[ 2, "asc" ]],
  "columnDefs": [{
      targets: 0,
      className: 'dt-body-right'
  }]
});


//// Ouvrir la modal liste - version titulaires
$('#getListeTitulaires').on('click', function()
{
  $('#modalMessageList').css('display', 'none');
  $('#modalListe #enCharge').css('display', 'block');
  $('#modalListe').addClass('is-active');

  tableList.ajax.url( 'data/getListTitulaires.php?m=<?php echo $nb_mois;?>&i=<?php echo $id;?>' ).load( function()
  {
    if (tableList.data().length === 0)
    {
      console.log('pas de données');
      // $('#rechercheSansResultats').css('display', 'block');
      // $('#rechercheResultats').css('display', 'none');
    }
    else
    {
      $('#modalMessageList').css('display', 'block');
      $('#modalListe #enCharge').css('display', 'none');
      console.log("On a " + tableList.data().length + " lignes de données");
      // $('#rechercheSansResultats').css('display', 'none');
      // $('#rechercheResultats').css('display', 'block');
    }
  });
}); // END Ouvrir modal


  //// Fermer modal liste acheteurs et titulaires
  $('.modal-card .delete, .modal-background, #ferme-modal-liste').on('click', function ()
  {
    $('#modalListe').removeClass('is-active');
    $('#modalListe #enCharge').css('display', 'block');
    $('input[type="search"]').html("");
  });






}); // document ready
</script>

<?php include('inc/footer.php'); ?>
