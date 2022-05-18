<?php

$page = "accueil";
$title = "Données essentielles du profil d'acheteur de Territoires Numériques";
$desc = "Données essentielles du profil d'acheteur de Territoires Numériques";

include('inc/head.php');
include('inc/config.php');
?>
<!-- entre heads : ajouter extra css , ... -->

<?php
include('inc/nav.php');
require_once('data/model.php');
require_once('data/connect.php');

$connect->set_charset("utf8");

// getKPI : connect, nom, nombre de mois
$kpi = getKPI($connect, null, $nb_mois, 0);

?>
<div id="bandeauHome">
<!--  <img id="halles" src="img/Dijon_Halles_01.jpg" alt="Les Halles de Dijon - Marché public" />-->
 <img id="halles" src="img/salle-marches-rennes.jpg" alt="Les Halles de Rennes - Marché public" />
  <div id="statsHome">
    <div style="max-width: 940px; padding: 0 20px; margin: 0 auto;">
      <p id="phraseHome">
        A partir du <b><?php echo  /*nf($kpi['periode'])*/$donnees_a_partir_du; ?></b></br>
        il y a eu <b><?php echo  nf($kpi['nombre']) ; ?></b> marchés</br>
        pour un total de <b><?php echo nf($kpi['montant_total']) ; ?></b> €
      </p>
    </div>
  </div>
  <h1 class='is-size-4 is-size-4-mobile'>Données essentielles du profil d'acheteur de Megalis Bretagne</h1>
</div>

<div id="main">
    <div class="container">

      <h2 class="is-size-2 has-text-centered is-size-2-desktop is-size-3-mobile">Echelle régionale</h2>

      <h2 id="dates" class="has-text-centered subtitle">Cet outil explore les données essentielles de plus de <b class='under-warning'>1 700 acheteurs publics</b> de Bourgogne Franche-Comté, adhérents au GIP Ternum BFC. Les contrats de type accords-cadres sont exclus à ce jour, mais ils seront intégrés dans une prochaine version de l'outil.</h2>

    <div id="dates" class="has-text-centered">
      <span class='tags has-addons'>
        <span class='tag is-light'>Contrats conclus à partir du </span>
        <span class='tag is-warning'><?php echo $donnees_a_partir_du;?></span>
        <span class='tag is-light'>Données mises à jour le </span>
        <span class='tag is-warning'><?php echo $donnees_mises_a_jour;?></span>
      </span>
    </div>
  </div>


  <!-- <div class="container testeurs">
    <p><i class="fas fa-flask"></i> <b>Note pour nos testeurs</b>
      <br>Pour des questions de qualité des données, nous avons fait le choix de réutiliser les données des contrats publiés après le 1er janvier 2019. Des 1 590 contrats publiés après cette date, nous avons écarté :
      <br>> 380 accords-cadres
      <br>> 14 contrats avec le titulaire mal renseigné (dénomination ou siret absent)
      <br>> 2 contrats avec l'acheteur mal renseigné (nom ou siret absent)
      <br>Il y a donc 1 194 visibles dans cette application.
    </p>
  </div> -->


  <?php
  //// 1. Indicateurs clés
  ?>

  <div class="container">
  <h3>Indicateurs clés</h3>
  <p>Principaux indicateurs des <b><?php echo $nb_mois;?> derniers mois</b>.</p>
  <div class="kpis columns">
    <div class="column kpi">
      <i class="far fa-calendar-alt fa-3x has-text-blue-light pb-1"></i>
      <span>Période</span>
      <b id="kpi-periode"><?php echo ceil( $kpi['periode'] ); ?> mois</b>
    </div>
    <div class="column kpi">
      <i class="far fa-handshake fa-3x has-text-blue-light pb5"></i>
      <span title="Nombre de contrats">Nb de contrats</span>
      <b id="kpi-nb-marches"><?php echo nf( $kpi['nombre'] ); ?></b>
    </div>
    <div class="column kpi">
      <i class="fas fa-calculator fa-3x has-text-blue-light"></i>
      <span>Montant total</span>
      <b id="kpi-montant-total"><?php echo nf( $kpi['montant_total'] ); ?> €</b>
    </div>
    <!-- <div class="column kpi">
      <i class="fas fa-divide fa-3x has-text-blue-light "></i>
      <span>Montant moyen</span>
      <b id="kpi-moyenne"><?php echo nf( $kpi['moyenne'] ); ?> €</b>
    </div> -->
    <div class="column kpi">
      <i class="fas fa-users fa-3x has-text-blue-light "></i>
      <span title="Nombre d'acheteurs">Nb Acheteurs</span>
      <b id="kpi-max"><?php echo nf( $kpi['nb_acheteurs'] ); ?> </b>
    </div>
    <div class="column kpi">
      <i class="fas fa-users fa-3x has-text-blue-light "></i>
      <span title="Nombre de fournisseurs">Nb Fournisseurs</span>
      <b id="kpi-max"><?php echo nf( $kpi['nb_fournisseurs'] ); ?> </b>
    </div>
  </div>
    </div>


  <section class="section">
    <div class="container">
  <?php
  //// 2. Distribution par catégorie principale d'achat
  $cats = getCategoriesPrincipales ($connect, $nb_mois, 0, "acheteur");
  include('inc/categories-principales-html.php');
  ?>
    </div>
  </section>



  <section class="section">
    <div class="container">

  <?php
  //// 3. Qui achète ?

  $acheteursTotal       = getAcheteursList($connect, 12, null);
  $acheteursServices    = getAcheteursList($connect, 12, 'services');
  $acheteursTravaux     = getAcheteursList($connect, 12, 'travaux');
  $acheteursFournitures = getAcheteursList($connect, 12, 'fournitures');
  ?>
    <h3>Qui achète ?</h3>
    <p>Top 12 des acheteurs classés par montant total des contrats conclus au cours de <b><?php echo $nb_mois;?> derniers mois</b>. Survolez les noms les acheteurs pour les afficher en entier.</p>
    <div id="acheteurs">
    <ul class="tab-container">
      <li class="tab-link current" data-tab="tab-1">Tous les marchés</li>
      <li class="tab-link" data-tab="tab-2">Services</li>
      <li class="tab-link" data-tab="tab-3">Travaux</li>
      <li class="tab-link" data-tab="tab-4">Fournitures</li>
    </ul>

    <!-- Onglet 1 -->
    <div id="tab-1" class="tab-content current">
      <div class="columns">
        <div class="column is-one-third tagsBlock quiListe">
            <?php
            foreach ($acheteursTotal as $a)
            {
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-total-bg'>" . nf($a[2]) . " €</span></div>\n";
            }
            ?>
          </div>
          <div class="column">
            <div id="topAcheteurs"></div>
          </div>
      </div>
    </div>


    <!-- Onglet Services -->
    <div id="tab-2" class="tab-content">
      <div class="columns">
        <div class="column is-one-third tagsBlock quiListe">
            <?php
            foreach ($acheteursServices as $a)
            {
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-services-bg'>" . nf($a[2]) . " €</span></div>\n";
            }
            ?>
          </div>
          <div class="column">
            <div id="topAcheteursServices"></div>
          </div>
      </div>
    </div>

    <!-- Onglet Travaux -->
    <div id="tab-3" class="tab-content">
      <div class="columns">
      <div class="column is-one-third tagsBlock quiListe">
          <?php
          foreach ($acheteursTravaux as $a)
          {
            echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-travaux-bg'>" . nf($a[2]) . " €</span></div>\n";
          }
          ?>
        </div>
        <div class="column">
          <div id="topAcheteursTravaux"></div>
        </div>
    </div>
  </div>

    <!-- Onglet Fournitures -->
    <div id="tab-4" class="tab-content">
      <div class="columns">

      <div class="column is-one-third tagsBlock quiListe">
          <?php
          foreach ($acheteursFournitures as $a)
          {
            echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-fournitures-bg'>" . nf($a[2]) . " €</span></div>\n";
          }
          ?>
        </div>
        <div class="column">
          <div id="topAcheteursFournitures"></div>
        </div>
    </div>
  </div>

  <p><button id="getListeAcheteurs" class="button has-text-link is-white"><i class="fas fa-list-ol"></i>&nbsp;Liste complète des organismes acheteurs </button></p>
</div>
</div>
</section>


<section class="section">
    <div class="container">
  <?php
  //// 4. Qui réalise  ?



  $titulairesTotal       = getTitulairesList($connect, 12, null, 0, $nb_mois);
  $titulairesServices    = getTitulairesList($connect, 12, 'services', 0, $nb_mois);
  $titulairesTravaux     = getTitulairesList($connect, 12, 'travaux', 0, $nb_mois);
  $titulairesFournitures = getTitulairesList($connect, 12, 'fournitures', 0, $nb_mois);
  ?>
  <!-- <div class="container"> -->
    <h3>Qui réalise ?</h3>
    <p>Top 12 des fournisseurs classés par montant total des contrats remportés au cours des <b><?php echo $nb_mois;?> derniers mois</b>. Survolez les noms des fournisseurs pour les afficher en entier.</p>
    <div id="titulaires">
    <ul class="tab-container">
      <li class="tab-link current" data-tab="tab-t1">Tous les marchés</li>
      <li class="tab-link" data-tab="tab-t2">Services</li>
      <li class="tab-link" data-tab="tab-t3">Travaux</li>
      <li class="tab-link" data-tab="tab-t4">Fournitures</li>
    </ul>

    <!-- Onglet 1 -->
    <div id="tab-t1" class="tab-content current">
      <div class="columns">
        <div class="column is-one-third tagsBlock quiListe">
            <?php
            foreach ($titulairesTotal as $a)
            {
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-total-bg'>" . nf($a[2]) . " €</span></div>\n";
            }
            ?>
          </div>
          <div class="column">
            <div id="topTitulaires"></div>
          </div>
      </div>
    </div>

    <!-- Onglet Services -->
    <div id="tab-t2" class="tab-content">
      <div class="columns">
        <div class="column is-one-third tagsBlock quiListe">
            <?php
            foreach ($titulairesServices as $a)
            {
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-services-bg'>" . nf($a[2]) . " €</span></div>\n";
            }
            ?>
          </div>
          <div class="column">
            <div id="topTitulairesServices"></div>
          </div>
      </div>
    </div>

    <!-- Onglet Travaux -->
    <div id="tab-t3" class="tab-content">
      <div class="columns">
      <div class="column is-one-third tagsBlock quiListe">
          <?php
          foreach ($titulairesTravaux as $a)
          {
            echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-travaux-bg'>" . nf($a[2]) . " €</span></div>\n";
          }
          ?>
        </div>
        <div class="column">
          <div id="topTitulairesTravaux"></div>
        </div>
    </div>
  </div>

    <!-- Onglet Fournitures -->
    <div id="tab-t4" class="tab-content">
      <div class="columns">

      <div class="column is-one-third tagsBlock quiListe">
          <?php
          foreach ($titulairesFournitures as $a)
          {
            echo "<div class='tags has-addons'><span class='tag is-light' title='" . $a[0] . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-fournitures-bg'>" . nf($a[2]) . " €</span></div>\n";
          }
          ?>
        </div>
        <div class="column">
          <div id="topTitulairesFournitures"></div>
        </div>
    </div>
  </div>

  <p><button id="getListeTitulaires" class="button has-text-link is-white"><i class="fas fa-list-ol"></i>&nbsp;Liste complète des fournisseurs </button></p>
</div>
</div>
</section>



<?php
//// 5. Nature des marchés
?>

    <div class="container">

<h3>Nature des contrats</h3>
<p>Répartition des contrats par nature du marché public, en montant et en nombre. La période observée est de <b><?php echo $nb_mois;?> mois</b> et les marchés sont groupés par mois. </p>
<div class="columns sequence">
  <?php
  $natures = getNatures2($connect, 0, $nb_mois);
  foreach ($natures as $nature):
    ?>
    <div class="column">
      <h4><?php echo $nature['nom_nature'];?></h4>
      <div class="miniCharts" id="nature_<?php echo $nature['id_nature'];?>"></div>
      <ul class="natures">
        <li><span>Montant</span> <?php echo nf($nature['total']);?> €</li>
        <li><span>Nombre</span> <?php echo nf($nature['nb_nature']);?> marchés</li>
      </ul>
    </div>
  <?php
endforeach;
?>
</div>
<div>
<?php include('inc/aideNature.php');?>
</div>
</div>


<?php
//// 6. Procédure des marchés
?>

    <div class="container">

<h3>Procédure suivie</h3>
<p>Classement des contrats selon la procédure suivie lors de la consultation. La période observée est de <b><?php echo $nb_mois;?> mois</b> </p>
<div class="columns sequence">
  <div class="column">
    <h4>Montant des contrats par procédure</h4>
    <div id="procedureMT"></div>
  </div>
<div class="column">
  <h4>Nombre de contrats par procédure</h4>
  <div id="procedureNB"></div>
</div>
</div>
<?php
$procedure = getProcedures($connect, 0, $nb_mois);
include('inc/aideProcedures.php');
?>
<!--
  <h2 class="is-title has-text-centered is-size-2-desktop is-size-2-mobile">Echelle départementale</h2> -->
</div>


    <div class="container">
  <?php
  //// 7. Départements : nombre et montant des marchés


  $depts = getDepts($connect, $nb_mois);
  ?>


  <h3>Contrats par département</h3>
  <div class="columns">
    <div class="column">
      <h4>Montant des contrats par département</h4>
      <div id="deptsMontant"></div>
    </div>
    <div class="column">
      <h4>Nombre de contrats par département</h4>
      <div id="deptsNB"></div>
    </div>
  </div>
</div>


    <div class="container">
  <?php
  //// 8. Distribution - sankey



  $s_services = getMontantCPVLieu($connect, 'services', $nb_mois);
  $s_travaux = getMontantCPVLieu($connect, 'travaux', $nb_mois);
  $s_fournitures = getMontantCPVLieu($connect, 'fournitures', $nb_mois);
  $sankey = array_merge_recursive($s_services, $s_travaux, $s_fournitures);
  ?>
  <h3>Distribution des achats par département</h3>
  <div id="distribCPVLieux"></div>

  <p class="has-text-right"><button id="aideCPVLieuxBtn" class="button has-text-link is-link-bg is-small"><i class="fas fa-question-circle"></i>&nbsp;Comment lire ce graphique ?</button></p>
  <div id="aideCPVLieux"s>
    <article class="message is-link">
      <div class="message-body">
        <p>Le <b>côté gauche</b> montre les grandes catégories de marchés publics. Ces catégories sont triées par le montant total des marchés qu’elles représentent. Au survol de ces catégories, on met en surbrillance tous les liens avec les départements qui ont lancé le marché.</p>
        <p>Le <b>côté droit</b> montre les départements triés par la première catégorie de gauche. Au survol de ces départements, on met en surbrillance tous les liens avec les catégories auxquelles correspondent leurs marchés.</p>
        <p>Au survol des <b>liens</b>, on obtient des informations complémentaires sur la catégorie du marché et le département lié. </p>
      </div>
    </article>
  </div>
</div>

<!-- Modal Liste acheteurs / titulaires -->

  <div id="modalListe" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
    <section class="modal-card-body">
      <div id="enCharge">
        <p>Je cherche les données, une seconde ... :)</p>
        <p><img src="img/spinner-wedges.gif"></p>
      </div>
      <div id="modalMessageList">
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



<!-- Modal -->

  <div id="modalMarche" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
    <section class="modal-card-body">
      <div id="enChargeMarche">
        <p>Je cherche les données, une seconde ... :)</p>
        <p><img src="img/spinner-wedges.gif"></p>
      </div>
      <div id="modalMessageMarche">
        <table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableMarche" style="width:100%;margin-top:10px; ">
          <thead>
            <tr>
              <th width="">Détails</th>
              <th width="">Acheteur</th>
              <th width="">CPV</th>
              <!-- <th width="">Titulaire</th> -->
              <th width="">Montant</th>
            </tr>
          </thead>
        </table>
      </div>

      <button id="ferme-modal-marche" class="is-center-block has-text-centered button is-btn-marron">Fermer</button>
    </section>
  </div><!-- ./modal-card -->
</div><!-- ./modalListe -->



</div> <!--./ main -->

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

<script>
$( document ).ready(function() {

// $('#kpi_odo_montant').animateNumber({ number: <?php // echo nf( $kpi['nombre'] ); ?> });

$('.count').each(function () {
  var $this = $(this);
  jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
    duration: 1000,
    easing: 'swing',
    step: function () {
      $this.text(Math.ceil(this.Counter));
    }
  });
});

  $('#aideCPVLieuxBtn').click(function()
  {
    $('#aideCPVLieux').toggle();
  });

  $('#aideNatureButton').click(function()
  {
    $('#aideNature').toggle();
  });

  $('#aideProcedureButton').click(function()
  {
    $('#aideProcedure').toggle();
  });

/* --------------------------------------
    Graphiques plotly
   --------------------------------------*/

var layout = { showlegend: false, autosize: true, hovermode:'closest', yaxis: { automargin: true }, margin: { l: 50, r: 50, b: 40, t: 40, pad: 4 }
};

var layoutMini = { showlegend: false, autosize: true, hovermode:'closest', margin: { l: 50, r: 50, b: 120, t: 0, pad: 0 }};

var layoutCategories = { showlegend: false, autosize: true, hovermode:'closest', height: 350, margin: { l: 50, r: 50, b: 60, t: 20, pad: 0}, showticklabels: false };

var layoutQui = { showlegend: false, autosize: true, hovermode:'closest', margin: { l: 50, r: 50, b: 120, t: 0, pad: 0 }};

var layoutCols = { showlegend: false, autosize: true, hovermode:'closest',
margin: { l: 20, r: 20, b: 0, t: 0, pad: 5 }, height: 150,
xaxis: { autorange: true, showgrid: false, zeroline: false, showline: false, autotick: true, ticks: '', showticklabels: false },
yaxis: { autorange: true, showgrid: false, zeroline: false, showline: false, autotick: true, ticks: '', showticklabels: false }
};


<?php
// Code pour générer les 3 graphiques des catégories principales
include('inc/categories-principales-js.php');
?>



  /* setQui
  ---------------
  Fonction pour afficher avec des indicateurs en HTML + barres Plotlyjs
  qui achete ou qui réalise les marchés
  */
  var setQui = function (id, x, y)
  {
    var chartData = [{ type: 'bar', y: y, x: x, marker:{ color: okabe_ito_reverse, line: { color: okabe_ito_reverse_border, width: 1 } }, orientation: 'v'}];
    Plotly.newPlot( id, chartData, layoutMini, optionsPlotly);
  };


<?php
  ///// Qui achète ?
  $noms = "";
  foreach ($acheteursTotal as $a) $noms .= '"' . $a[0] . '",';
?>
setQui('topAcheteurs',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($acheteursTotal, 2)); ?>] );

<?php
  $noms = "";
  foreach ($acheteursServices as $a) $noms .= '"' . $a[0] . '",';
?>
setQui('topAcheteursServices',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($acheteursServices, 2)); ?>] );

<?php
  $noms = "";
  foreach ($acheteursTravaux as $a) $noms .= '"' . $a[0] . '",';
?>
setQui('topAcheteursTravaux',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($acheteursTravaux, 2)); ?>] );

<?php
  $noms = "";
  foreach ($acheteursFournitures as $a) $noms .= '"' . $a[0] . '",';
?>
setQui('topAcheteursFournitures',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($acheteursFournitures, 2)); ?>] );


<?php
  ///// Qui réalise ?
  $noms = "";
  foreach ($titulairesTotal as $a) $noms .= '"' . coupe($a[0], 24) . '",';
?>
setQui('topTitulaires',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($titulairesTotal, 2)); ?>] );

<?php
  $noms = "";
  foreach ($titulairesServices as $a) $noms .= '"' . coupe($a[0], 24) . '",';
?>
setQui('topTitulairesServices',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($titulairesServices, 2)); ?>] );

<?php
  $noms = "";
  foreach ($titulairesTravaux as $a) $noms .= '"' . coupe($a[0], 24) . '",';
?>
setQui('topTitulairesTravaux',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($titulairesTravaux, 2)); ?>] );

<?php
  $noms = "";
  foreach ($titulairesFournitures as $a) $noms .= '"' . coupe($a[0], 24) . '",';
?>
setQui('topTitulairesFournitures',
[<?php echo $noms; ?>],
[<?php echo implode(',', array_column($titulairesFournitures, 2)); ?>] );





/* --------------------------------------
  Nature des marchés
  --------------------------------------*/

var linePlot = function (id, x, y)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'scatter'}], layoutCols, optionsPlotly );
};
var splinePlot = function (id, x, y, couleur)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'scatter',mode: 'lines',line: {shape: 'hv', color: couleur }}], layoutCols, {displayModeBar: false} );
};
var barPlot = function (id, x, y, couleur, border)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'bar', marker: {color: couleur,line: { color: border, width: 1 } }}], layoutCols, {displayModeBar: false} );
};

<?php
for ($i=1; $i<5; $i++)
{
  $data = getNatureByDate($connect, 0, $i, $nb_mois);
  echo "barPlot('nature_$i', ["
  . $data['date'] . "], ["
  . $data['total'] . "], "
  . " couleursNatures[" . ($i-1) . "],"
  . " couleursNaturesBorders[" . ($i-1) . "]);\n";
}


/* --------------------------------------
  Procédures utilisées
  --------------------------------------*/

?>
var layoutProcedures = { showlegend: false, autosize: true, hovermode:'closest',
margin: { l: 200, r: 20, b: 40, t: 30, pad: 5 }
};

var procedureNBData = [
  {
    type: 'bar',
    x: [<?php echo $procedure['nb_procedure'];?>],
    y: [<?php echo $procedure['nom_procedure'];?>],
    marker:{
      color: okabe_ito_reverse,
      line: { color: okabe_ito_reverse_border, width: 1 }
    },
    orientation: 'h'
  }
];

Plotly.newPlot( "procedureNB", procedureNBData, layoutProcedures, optionsPlotly);

var procedureMTData = [
  {
    type: 'bar',
    x: [<?php echo $procedure['total_procedure'];?>],
    y: [<?php echo $procedure['nom_procedure'];?>],
    marker:{
      color: okabe_ito_reverse,
      line: { color: okabe_ito_reverse_border, width: 1 }
    },
    orientation: 'h'
  }
];

Plotly.newPlot( "procedureMT", procedureMTData, layoutProcedures, optionsPlotly);





///// depts : montant des marchés
var deptsMontantData = [{
  type: 'bar',
  x: [<?php echo $depts['montant']; ?>],
  y: [<?php echo $depts['lieu']; ?>],
  marker:{
    color: okabe_ito,
    line: { color: okabe_ito_border, width: 1 }
  },
  orientation: 'h'
}];
Plotly.newPlot('deptsMontant', deptsMontantData, layout, optionsPlotly);


///// depts : nombre marchés
var deptsNBData = [{
  type: 'bar',
  x: [<?php echo $depts['nombre']; ?>],
  y: [<?php echo $depts['lieu']; ?>],
  marker:{
    color: okabe_ito,
    line: { color: okabe_ito_border, width: 1 }
  },
  orientation: 'h'
}];
Plotly.newPlot('deptsNB', deptsNBData, layout, optionsPlotly);


///// Distribution des achats par département
var data =
[{
  type: "sankey", orientation: "h", textfont: { family: 'Roboto' }, arrangement: 'snap',
  // node : extremos, pad: separacion vertical, line : border
  node:
  {
    pad: 20, thickness: 30, line: { color: "#fff", width: 1 },
    // TODO A variabiliser
    label: ["Services", "Travaux", "Fournitures", "Ille-et-Vilaine", "Morbihan", "Côtes-d'Armor", "Finistère", "Loire-Atlantique", "Maine-et-Loire", "Aisne","Paris","Nord"],
    color: okabe_ito_sankey
  },
  link: {
    /*color: "#eee",
    line: { color: "#bbb", width: 0.5 },*/
    source: [<?php echo implode( ',', $sankey['source']); ?>],
    target: [<?php echo implode( ',', $sankey['target']); ?>],
    value:  [<?php echo implode( ',', $sankey['values']); ?>]
  }
}];

var layoutSankey = { font: { size: 16 }, height: 700, /*width: 920,*/ autosize: true, hoverinfo:"", margin: { l: 20, r: 20, b: 60, t: 40, pad: 0} };

Plotly.react('distribCPVLieux', data, layoutSankey, optionsPlotly)


/* --------------------------------------
    Modal liste acheteurs et titulaires
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

//// Ouvrir la modal liste - version acheteurs
// $('#getListeAcheteurs, #getListeTitulaires').on('click', function()
$('#getListeAcheteurs').on('click', function()
{
  $('#modalMessageList').css('display', 'none');
  $('#enCharge').css('display', 'block');
  $('#modalListe').addClass('is-active');

  /*
  Charger données
  function ajax.url().load( callback, resetPaging )
  */
  tableList.ajax.url( 'data/getListAcheteurs.php?m=<?php echo $nb_mois;?>' ).load( function()
  {
    // $('#rechercheBouton').removeClass('is-loading');
    // A-t-on des données ?
    if (tableList.data().length === 0)
    {
      console.log('pas de données');
      // $('#rechercheSansResultats').css('display', 'block');
      // $('#rechercheResultats').css('display', 'none');
    }
    else
    {
      $('#modalMessageList').css('display', 'block');
      $('#enCharge').css('display', 'none');
      console.log("On a " + tableList.data().length + " lignes de données");
      // $('#rechercheSansResultats').css('display', 'none');
      // $('#rechercheResultats').css('display', 'block');
    }
  });
}); // END Ouvrir modal


//// Ouvrir la modal liste - version titulaires
$('#getListeTitulaires').on('click', function()
{
  $('#modalMessageList').css('display', 'none');
  $('#enCharge').css('display', 'block');
  $('#modalListe').addClass('is-active');

  tableList.ajax.url( 'data/getListTitulaires.php?m=<?php echo $nb_mois;?>' ).load( function()
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
      $('#enCharge').css('display', 'none');
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
    $('#enCharge').css('display', 'block');
    $('input[type="search"]').html("");
  });







  /* --------------------------------------
      Modal liste marchés par service
     --------------------------------------*/
  //// Initialiser la table une fois
  var tableMarche = $('#tableMarche').DataTable({
    "responsive": true,
    "dom": '<"wrapper"Bfltip>',
    "language": francais,
    "columns": [
      { "data": "details" },
      { "data": "acheteur" },
      { "data": "libelle_cpv" },
      // { "data": "titulaire" },
      { "data": "montant",
      render: $.fn.dataTable.render.number( ' ', '.', 0, '', '€' ) }
    ],
    "paging": true,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
    "order": [[ 2, "desc" ],[ 0, "asc" ]],
    "columnDefs": [{
        targets: 0,
        className: 'dt-body-right'
    }]
  });

  //// Ouvrir la modal liste - version acheteurs
  $('.getTypeMarches').on('click', function()
  {
    var type = $(this).data('type');
    $('#modalMessageMarche').css('display', 'none');
    $('#enChargeMarche').css('display', 'block');
    $('#modalMarche').addClass('is-active');

    // /*
    // Charger données
    // function ajax.url().load( callback, resetPaging )
    // */

    tableMarche.ajax.url( 'data/getTypeMarche.php?type=' + type ).load( function()
    {
      if (tableMarche.data().length === 0)
      {
        console.log('pas de données');
      }
      else
      {
        $('#modalMessageMarche').css('display', 'block');
        $('#enChargeMarche').css('display', 'none');
        console.log("On a " + tableMarche.data().length + " lignes de données");
      }
    });
  }); // END Ouvrir modal


    //// Fermer modal liste acheteurs et titulaires
    $('.modal-card .delete, .modal-background, #ferme-modal-marche').on('click', function ()
    {
      $('#modalMarche').removeClass('is-active');
      $('#enChargeMarche').css('display', 'block');
      $('input[type="search"]').html("");
    });






}); // document ready
</script>

<?php include('inc/footer.php'); ?>
