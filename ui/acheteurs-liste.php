<?php

include('inc/localization.php');
$page = "acheteurs";
$title = "Marchés publics " . gettext("NOM_REGION");
$desc = "Marchés publics " . gettext("NOM_REGION");

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
  <div class="container">
    <h1 class='title'>Liste d'acheteurs du profil d'acheteur de
      <?php echo gettext("NOM_OPSN") ?>
    </h1>
    <div class="columns">
      <div class="column">
        <p><img src="img/TN_ruralite_connectee_400.png"></p>
      </div>
      <div class="column">
        <p>Cliquez sur chaque élement de la liste pour découvrir le profil détaillé de l'acheteur. Le montant affiché
          correspond au total des marchés passés par cet acheteur. Le tri est alphabetique.</p>
        <p> Sigles utilisées :</p>
        <div class="columns">
          <div class="column tags has-addons">
            <span class="tag is-light">CA</span> <span class="tag is-white">Communauté d'Agglomération</span><br>
            <span class="tag is-light">CC</span> <span class="tag is-white">Communauté de Communes</span><br>
          </div>
          <div class=" column tags has-addons">
            <span class="tag is-light">CD</span> <span class="tag is-white">Conseil Départemental</span><br>
            <span class="tag is-light">CU</span> <span class="tag is-white">Communauté Urbaine</span><br>
          </div>
          <div class=" column tags has-addons">
            <span class="tag is-light">SDIS</span> <span class="tag is-white">Service Départemental d'Incendie et de
              Secours</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div id="mapa"></div> -->

  <?php
  try {
    $sql = "SELECT m.id_acheteur id, a.nom_acheteur nom, a.nom_ui nom_ui, sum(m.montant) montant, count(id) nombre
  FROM marche m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur
  GROUP BY a.nom_acheteur
  ORDER BY nom_ui ASC ";

    $result = $connect->query($sql);

    if (mysqli_num_rows($result) > 0) {
      echo "<div id='listeAcheteurs'>";
      $nb_cols = 0;
      $initiale = "";

      while ($r = mysqli_fetch_assoc($result)) {
        // colonnes
        if ($initiale !== mb_substr($r['nom_ui'], 0, 1)) {
          $initiale = mb_substr($r['nom_ui'], 0, 1);
          if ($nb_cols === 0)
            echo "<div>";
          else
            echo "</div><div>";
          echo "<h3>" . $initiale . "</h3>";
        }
        $nb_cols++;

        $nom = $r['nom_ui'];
        if (strlen($r['nom_ui']) > 25) {
          $nom = substr($r['nom_ui'], 0, 25) . "...";
        }

        // echo '<div class="tags has-addons">
        //   <span class="tag is-success" title="' . $r['nom'] . '"><a href="acheteur.php?i=' . strtolower(str_replace(" ", "_", $r['nom'])) . '">' . clean($nom) . '</a></span>
        //   <span class="tag">' . nf($r['montant']) . '€</span></div>';
        ($r['nombre'] > 1) ? $contrats = 'contrats' : $contrats = 'contrat';

        echo '<div class="tags has-addons">
        <span class="tag is-success tag-org" title="' . $r['nom'] . '"><a href="acheteur.php?i=' . $r['id'] . '">' . clean($nom) . '</a>
        </span>
        <span class="tag tag-nb">' . nf($r['nombre']) . ' ' . $contrats . '</span>
        <span class="tag tag-montant">' . nf($r['montant']) . '€</span>
        </div>';
      }
      echo "</div></div>";
    }

    mysqli_free_result($result);
  } catch (Exception $e) {
    echo "Nous n'avons pas trouvé des données : " . $e->getMessage();
  }

  ?>
</div> <!-- ./ main -->

<?php include('js/common-js.php'); ?>

<!-- <script src="assets/datatables/jquery.dataTables.min.js"></script>
<script src="assets/datatables/dataTables.buttons.min.js"></script>
<script src="assets/datatables/buttons.flash.min.js"></script>
<script src="assets/datatables/jszip.min.js"></script>
<script src="assets/datatables/pdfmake.min.js"></script>
<script src="assets/datatables/vfs_fonts.js"></script>
<script src="assets/datatables/buttons.html5.min.js"></script>
<script src="assets/datatables/buttons.print.min.js "></script> -->

<!-- <script src="assets/leaflet/leaflet.js"></script> -->
<script src="assets/jquery/jquery-3.3.1.min.js"></script>
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

<?php include('inc/footer.php'); ?>