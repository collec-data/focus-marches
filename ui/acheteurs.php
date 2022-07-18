<?php

$page = "acheteurs";
$title = "Les organismes du profil d'acheteur de ".gettext("Ternum BFC");
$desc = "Les organismes du profil d'acheteur de ".gettext("Ternum BFC");

include('inc/head.php');
include('inc/localization.php');
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
  <div class="container">
  <h1 class='title'>Les organismes du profil d'acheteur de Mégalis Bretagne</h1>
  <div class="columns">
    <!-- <div class="column"><p><img src="img/TN_ruralite_connectee_400.png" ></p></div> -->
    <div class="column">
      <p>Cliquez sur chaque élement de la liste pour découvrir le profil détaillé de l'acheteur. Le montant affiché correspond au total des marchés passés par cet acheteur. La table est triée alphabetiquement par les organismes.</p>
    </div>
</div>
<div id="enChargeFlask">
  <p>
    <img src="img/flask.gif" alt="En charge" /><br>
    On prépare beaucoup de données, un instant :)
  </p>
</div>

<table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableUI"  style="width:100%">
  <thead>
    <tr>
      <th width="7%">Dépt.</th>
      <th width="36%">Organisme</th>
      <th width="20%">Cat. juridique</th>
      <th width="7%">Nombre contrats</th>
      <th width="10%">Montant contrats</th>
      <th width="10%">Effectifs</th>
    </tr>
  </thead>
</table>

</div>
</div> <!-- ./ main -->

<?php include('js/common-js.php');?>

<script src="assets/datatables/jquery.dataTables.min.js"></script>
<script src="assets/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
<script src="assets/datatables/dataTables.buttons.min.js"></script>
<script src="assets/datatables/buttons.flash.min.js"></script>
<script src="assets/datatables/jszip.min.js"></script>
<script src="assets/datatables/pdfmake.min.js"></script>
<script src="assets/datatables/vfs_fonts.js"></script>
<script src="assets/datatables/buttons.html5.min.js"></script>
<script src="assets/datatables/buttons.print.min.js "></script>

<!-- <script src="assets/leaflet/leaflet.js"></script> -->
<!-- <script src="assets/jquery/jquery-3.3.1.min.js"></script> -->
<!-- <script src="assets/geojson/region-bourgogne-franche-comte.geojson"></script> -->
<!-- <script>
$( document ).ready(function() {

  var dijon = [47.316, 5.016];
  var zoom = 7;
  var map = L.map('mapa').setView(dijon, zoom);
  var tiles = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

  L.tileLayer( tiles, { attribution: attribution } ).addTo(map);
  function styleReg(feature){
    return{
      fillColor: '#23d160',
      weight: 2,
      opacity: 0.5,
      color: '#0093b6',
      fillOpacity: 0.4
    };
  }

  var regionGeoJs = L.geoJson(regionBFC,{style:styleReg}).addTo(map);

});
</script> -->

<script ype="text/javascript">
$( document ).ready(function() {

  //// Surcharge des traductions
  francais.sEmptyTable = "Je cherche les données ...";
  francais.sInfo = "Affichage des organismes _START_ &agrave; _END_ sur _TOTAL_ organismes";
  francais.sZeroRecords = "Aucun marché &agrave; afficher";

  console.log(francais);
  //// Configuration de la table
  var tableUI = $('#tableUI').DataTable({
    "responsive": true,
    "dom": '<"wrapper"Bfltip>',
    "language": francais,
    "columns": [
      { "data": "dep", "width": "7%"  },
      { "data": "denomination_sociale", "width": "36%"  },
      { "data": "libelle_categories_juridiques", "width": "20%" },
      { "data": "nb", "width": "7%" },
      { "data": "total", "width": "10%",
      render: $.fn.dataTable.render.number( ' ', '.', 0, '', '€' ) },
      { "data": "libelle_tranche_etablissement", "orderable": false,  "width": "10%" }
    ],
    "paging": true,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
    // "order": [[ 2, "asc" ],[ 4, "asc" ]]
  });

  $('#enChargeFlask').toggle();

  let url = "data/getListAcheteursEtendue.php";
  tableUI.ajax.url( url ).load( function()
  {
    if (tableUI.data().length === 0)
    {
      console.log('pas de données');
    }

    $('#enChargeFlask').toggle();
  });
}); // document ready
</script>

<?php include('inc/footer.php'); ?>
