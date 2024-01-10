<?php

include('inc/localization.php');
$page = "titulaires";
$title = "Marchés publics en " . gettext("NOM_REGION");
$desc = "Marchés publics en " . gettext("NOM_REGION");

include('inc/head.php');
?>
<!-- entre heads : ajouter extra css , ... -->

<link rel="stylesheet" href="assets/leaflet/leaflet.css" />

<?php
include('inc/nav.php');
require_once('data/model.php');
require_once('data/connect.php');

$connect->set_charset("utf8");

?>

<div id="main">
  <div class="container wide">
    <h1 class='title'>Les fournisseurs répertoriés dans les profils d'acheteur de
      <?php echo gettext("NOM_OPSN") ?>
    </h1>
    <div class="columns">
      <div class="column">
        <p>Cliquez sur chaque élement de la liste pour découvrir le profil détaillé du titulaire. Le montant affiché
          correspond au total des contrats gagnés par le titulaire. La table est triée alphabetiquement par la
          dénomination des titulaires.</p>
      </div>
    </div>
    <div id="enChargeFlask">
      <p>
        <img src="img/flask.gif" alt="En charge" /><br>
        On prépare beaucoup de données, un instant :)
      </p>
    </div>
    <table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableUI"
      style="width:100%">
      <thead>
        <tr>
          <th width="7%">Annuaire</th>
          <th width="7%">CP</th>
          <th width="36%">Titulaire</th>
          <th width="20%">NAF</th>
          <th width="7%">Nombre contrats</th>
          <th width="10%">Montant contrats</th>
          <th width="10%">Effectifs</th>
          <th width="10%" title="dernier chiffre d'affaires communiqué (source : infogreffe)">CA</th>
          <th width="10%" title="dernier chiffre de résultat communiqué (source : infogreffe)">Résultat</th>
        </tr>
      </thead>
    </table>

  </div>
</div> <!-- ./ main -->

<?php include('js/common-js.php'); ?>

<script src="assets/datatables/jquery.dataTables.min.js"></script>
<script src="assets/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
<script src="assets/datatables/dataTables.buttons.min.js"></script>
<script src="assets/datatables/buttons.flash.min.js"></script>
<script src="assets/datatables/jszip.min.js"></script>
<script src="assets/datatables/pdfmake.min.js"></script>
<script src="assets/datatables/vfs_fonts.js"></script>
<script src="assets/datatables/buttons.html5.min.js"></script>
<script src="assets/datatables/buttons.print.min.js "></script>

<script ype="text/javascript">
  $(document).ready(function () {

    //// Surcharge des traductions
    francais.sEmptyTable = "Je cherche les données ...";
    francais.sInfo = "Affichage des organismes _START_ &agrave; _END_ sur _TOTAL_ organismes";
    francais.sZeroRecords = "Aucun marché &agrave; afficher";

    //// Configuration de la table
    var tableUI = $('#tableUI').DataTable({
      "responsive": true,
      "dom": '<"wrapper"Bfltip>',
      "language": francais,
      "columns": [
        { "data": "annuaire_lien", "orderable": false, "width": "7%"},
        { "data": "cp", "width": "7%" },
        { "data": "denomination_sociale", "width": "30%" },
        { "data": "naf", "width": "20%" },
        { "data": "nb", "width": "7%" },
        {
          "data": "total", "width": "10%",
          render: $.fn.dataTable.render.number(' ', '.', 0, '', '€')
        },
        { "data": "libelle_tranche_etablissement", "orderable": false, "width": "10%" },
        {
          "data": "ca_1", "width": "10%",
          render: $.fn.dataTable.render.number('&nbsp;', '.', 0, '', '€')
        },
        {
          "data": "resultat_1", "width": "10%",
          render: $.fn.dataTable.render.number('&nbsp;', '.', 0, '', '€')
        }
      ],
      "paging": true,
      "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
      // "order": [[ 2, "asc" ],[ 4, "asc" ]]
    });

    $('#enChargeFlask').toggle();

    let url = "data/getListTitulairesEtendue.php";
    tableUI.ajax.url(url).load(function () {
      $('#enChargeFlask').toggle();
    });
  }); // document ready
</script>

<?php include('inc/footer.php'); ?>