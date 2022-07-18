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
}

// L'affichage de certains éléments dépend de si on est face à une collectivité ou pas
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

        if (isset($sirene['siren']))
        {
      ?>
      <h3>Localisation et contexte</h3>
      <div id="sirene" class="columns">
          <div class="column has-map">
            <div id="mapVertical"></div>
          </div>
          <div class="column">

            <?php
                switch ($sirene['categorieEntreprise'])
                {
                  case 'GE' : $ce = "GE - Grande entreprise";break;
                  case 'PME' : $ce = "PME - Petite et moyenne entreprise";break;
                  case 'ETI' : $ce = "ETI - Entreprise de taille intermédiaire";break;
                  case 'TPE' : $ce = "TPE - Très petite entreprise";break;
                  default : $ce ='-';
                }
            ?>

            <?php
            if ($colter)
            {
              ?>
              <div class="tabs is-centered">
                <ul>
                  <li class="is-active">
                    <a id="tab1"><i class="fas fa-building"></i>&nbsp; L'organisme</a>
                  </li>
                  <?php if ($colter) {?>
                    <li>
                      <a id="tab2"><i class="fas fa-users"></i>&nbsp; La population</a>
                    </li>
                  <?php } ?>
                </ul>
              </div>

            <!-- Tab Siège -->
            <div class="tabContainer" id="tab1C">
              <table class="table is-striped is-fullwidth">
                <tr><td>Dénomination</td><td><?php echo $sirene['denominationUniteLegale'];?></td></tr>
                <tr><td>Date création</td><td><?php echo $sirene['dateCreationUniteLegale'];?></td></tr>
                <tr><td>Sigle</td><td><?php echo $sirene['sigleUniteLegale'];?></td></tr>
                <tr><td>Adresse</td><td><?php echo $sirene['typeVoieEtablissement'] . " " . $sirene['libelleVoieEtablissement'] . " " . $sirene['codePostalEtablissement'] . " " . $sirene['libelleCommuneEtablissement'];?></td></tr>
                <tr><td title="Catégorie entreprise">Cat. entreprise</td><td><?php echo $ce ;?></td></tr>
                <tr><td title="Catégorie juridique">Cat. juridique</td><td><?php echo $sirene['categorieJuridiqueUniteLegale'] . ' - ' . $sirene['libelle_categories_juridiques'];?></td></tr>
                <tr><td>NAF</td><td><?php echo $sirene['code_naf'] . ' - ' . $sirene['libelle_naf'] ;?></td></tr>
                <tr><td>Effectifs</td><td><?php echo $sirene['libelle_tranche_entreprise'] . " (en " . $sirene['anneeEffectifsUniteLegale'] .")";?></td></tr>
              </table>
            </div>
          <!-- Tab Population -->
          <div class="tabContainer" id="tab2C">
            <table class="table is-striped is-fullwidth">
                <tr>
                  <td>Population (2015)</td><td colspan='3'><?php echo $sirene['pop_2015'];?> habitants</td>
                </tr>
                <tr>
                  <td>Ménages</td><td><?php echo $sirene['menages'];?> ménages</td>
                  <td title="Population de 15 ans ou plus Agriculteurs exploitants en 2015">Agriculteurs</td><td><?php echo $sirene['agriculteurs'] . pf($sirene['pop_2015'], $sirene['agriculteurs']);?></td>
                </tr>
                <tr>
                  <td title="Population de 15 ans ou plus Artisans, Commerçants, Chefs entreprise en 2015">Artisans, chefs</td><td><?php echo $sirene['artisans_chefs'] . pf($sirene['pop_2015'], $sirene['artisans_chefs']);?></td>
                  <td title="Population de 15 ans ou plus Cadres ou professions intellectuelles supérieures en 2015">Cadres</td><td><?php echo $sirene['cadres'] . pf($sirene['pop_2015'], $sirene['cadres']);?></td>
                </tr>
                <tr>
                  <td title="Population de 15 ans ou plus professions Intermediaires en 2015">Intermediaires</td><td><?php echo $sirene['intermediaires'] . pf($sirene['pop_2015'], $sirene['intermediaires']);?></td>
                  <td title="Population de 15 ans ou plus Employés en 2015">Employés</td><td><?php echo $sirene['employes'] . pf($sirene['pop_2015'], $sirene['employes']);?></td>
                </tr>
                <tr>
                  <td title="Population de 15 ans ou plus Retraités en 2015">Retraités</td><td><?php echo $sirene['retraites'] . pf($sirene['pop_2015'], $sirene['retraites']);?></td>
                  <td title="Population de 15 ans ou plus Autres sans activité professionnelle en 2015">Autres</td><td><?php echo $sirene['autres'] . pf($sirene['pop_2015'], $sirene['autres']);?></td>
                </tr>
                <tr>
                  <td title="Le niveau de vie est égal au revenu disponible du ménage divisé par le nombre d'unités de consommation (uc). Le niveau de vie est donc le même pour tous les individus d'un même ménage. Les unités de consommation sont généralement calculées selon l'échelle d'équivalence dite de l'OCDE modifiée qui attribue 1 uc au premier adulte du ménage, 0,5 uc aux autres personnes de 14 ans ou plus et 0,3 uc aux enfants de moins de 14 ans. (Source: INSEE)">Médiane niveau vie</td><td colspan='3'>
                  <?php
                    if (is_numeric($sirene['mediane_niveau_vie']))
                    {
                      echo nf( $sirene['mediane_niveau_vie'] ) . " € (" . nf( $revenuMoyenNational ) . " €) <span title='moyenne des mediannes de toutes les communes'>à niveau nationnal</span>";
                    }
                    else
                    {
                      echo 'non disponible';
                    }
                  ?></td>
                </tr>
              </table>
              <p>Survolez les rubriques pour voir leur définiton</p>
            </div>
        <?php
        }
        else
        {
        ?>
          <table class="table is-striped is-fullwidth">
            <tr><td>Dénomination</td><td><?php echo $sirene['denominationUniteLegale'];?></td></tr>
            <tr><td>Date création</td><td><?php echo $sirene['dateCreationUniteLegale'];?></td></tr>
            <tr><td>Sigle</td><td><?php echo $sirene['sigleUniteLegale'];?></td></tr>
            <tr><td>Adresse</td><td><?php echo $sirene['typeVoieEtablissement'] . " " . $sirene['libelleVoieEtablissement'] . " " . $sirene['codePostalEtablissement'] . " " . $sirene['libelleCommuneEtablissement'];?></td></tr>
            <tr><td title="Catégorie entreprise">Cat. entreprise</td><td><?php echo $ce ;?></td></tr>
            <tr><td title="Catégorie juridique">Cat. juridique</td><td><?php echo $sirene['categorieJuridiqueUniteLegale'] . ' - ' . $sirene['libelle_categories_juridiques'];?></td></tr>
            <tr><td>NAF</td><td><?php echo $sirene['code_naf'] . ' - ' . $sirene['libelle_naf'] ;?></td></tr>
            <tr><td>Effectifs</td><td><?php echo $sirene['libelle_tranche_entreprise'] . " (en " . $sirene['anneeEffectifsUniteLegale'] .")";?></td></tr>
          </table>
        <?php
        }
        ?>

          </div>
      </div>
      <?php
      }
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

      <?php
      /*
      donnée à afficher si collectivité
      7210 = Commune et commune nouvelle

      */
      if ($sirene['categorieJuridiqueUniteLegale'] === '7210')
      {
      ?>

        <div class="column kpi">
          <i class="fas fa-users fa-3x has-text-blue-light "></i>
          <span>Population</span>
          <b id="kpi-moyenne" title="<?php echo nf( $sirene['pop_2015'] ); ?> habitants"><?php echo nf( $sirene['pop_2015'] ); ?> hb.</b>
        </div>
        <div class="column kpi" title="Le niveau de vie est égal au revenu disponible du ménage divisé par le nombre d'unités de consommation (uc). Le niveau de vie est donc le même pour tous les individus d'un même ménage. Les unités de consommation sont généralement calculées selon l'échelle d'équivalence dite de l'OCDE modifiée qui attribue 1 uc au premier adulte du ménage, 0,5 uc aux autres personnes de 14 ans ou plus et 0,3 uc aux enfants de moins de 14 ans. (Source: INSEE)">
          <i class="fas fa-euro-sign fa-3x has-text-blue-light "></i>
          <span>Niveau vie moyen</span>
          <b id="kpi-max"><?php
          if (is_numeric($sirene['mediane_niveau_vie']))
          {
            echo nf( $sirene['mediane_niveau_vie'] ) . " €";
          }
          else
          {
            echo 'non disponible';
          }?></b>
          <s id="kpi-niveau-vie"><?php echo nf( $revenuMoyenNational );?>€ de &frac12; en France</s>
        </div>

      <?php
      }
      else
      {
        ?>

        <!-- <div class="column kpi">
          <i class="fas fa-divide fa-3x has-text-blue-light "></i>
          <span>Montant moyen</span>
          <b id="kpi-moyenne"><?php echo nf( $kpi['moyenne'] ); ?> €</b>
        </div> -->
          <div class="column kpi">
          <i class="fas fa-mountain fa-3x has-text-blue-light "></i>
          <span>Montant max.</span>
          <b id="kpi-max"><?php echo nf( $kpi['maximum'] ); ?> €</b>
        </div>
        <div class="column kpi">
          <i class="fas fa-users fa-3x has-text-blue-light "></i>
          <span title="Nombre de fournisseurs">Nb Fournisseurs</span>
          <b id="kpi-max"><?php echo nf( $kpi['nb_fournisseurs'] ); ?> </b>
        </div>

        <?php
      }
      ?>


    </div>
  </div>



  <div class="container">
  <?php
  //// 2. Distribution par catégorie principale d'achat
  $cats = getCategoriesPrincipales ($connect, $nb_mois, $id, "acheteur");
  include('inc/categories-principales-html.php');
  ?>
  </div>



  <?php
  //// Qui achète ?
  $titulairesTotal       = getTitulairesList($connect, 12, null, $id, $nb_mois);
  $titulairesServices    = getTitulairesList($connect, 12, 'services', $id, $nb_mois);
  $titulairesTravaux     = getTitulairesList($connect, 12, 'travaux', $id, $nb_mois);
  $titulairesFournitures = getTitulairesList($connect, 12, 'fournitures', $id, $nb_mois);
  ?>

  <div class="container">
    <h3>Qui a réalisé les marchés ?</h3>
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
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . htmlentities($a[0], ENT_QUOTES) . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-total-bg'>" . nf($a[2]) . " €</span></div>\n";
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
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . htmlentities($a[0], ENT_QUOTES) . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-services-bg'>" . nf($a[2]) . " €</span></div>\n";
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
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . htmlentities($a[0], ENT_QUOTES) . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-travaux-bg'>" . nf($a[2]) . " €</span></div>\n";
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
              echo "<div class='tags has-addons'><span class='tag is-light' title='" . htmlentities($a[0], ENT_QUOTES) . "'>" . coupe($a[0], 24) . "</span>" . "<span class='tag has-fournitures-bg'>" . nf($a[2]) . " €</span></div>\n";
            }
            ?>
          </div>
          <div class="column">
            <div id="topTitulairesFournitures"></div>
          </div>
        </div>
      </div>

      <p><button id="getListeTitulaires" class="button has-text-link is-white"><i class="fas fa-list-ol"></i>&nbsp;Liste des fournisseurs de cet acheteur</button></p>
    </div>
  </div>


  <div class="container">
    <h3>Distribution temporelle des marchés</h3>
    <div id="marches-sans-date"></div>
    <div id="rechercheTempChart"></div>
    <div id="datesMontantsLieuData"></div>
    <?php include ('inc/aideRecherche.php');?>
  </div>

  <div class="container">
    <h3 id="titreTableUI">Tous les marchés de <?php echo $nom;?></h3>
    <p class="pb-40 is-size-9">Ce tableau affiche les principales informations des marchés de cet acheteur. Cliquez sur "Voir" pour accéder au détail de chaque marché. </p>
    <table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableUI"  style="width:100%">
      <thead>
        <tr>
          <th width="7%">Détails</th>
          <th width="38%">CPV <i class="fas fa-question-circle has-text-grey-light" title="Le vocabulaire commun des marchés publics ou CPV (Common Procurement Vocabulary) est composé de codes normalisés, utilisés pour décrire l’objet des contrats à l’aide d’un système unique de classification pour les marchés publics."></i></th>
          <th width="20%">Acheteur</th>
          <th width="25%">Fournisseur</th>
          <th width="5%">Date</th>
          <th width="10%">Montant</th>
        </tr>
      </thead>
    </table>
  </div>




  <div class="container">
    <h3>Nature des marchés</h3>
    <p>Répartition des contrats par nature du marché public, en montant et en nombre. La période observée est de <b><?php echo $nb_mois;?> mois</b> et les marchés sont groupés par mois. </p>
    <div class="columns sequence">
      <?php
      // $natures = getNatures2($connect, $id, $nb_mois);
      $natures = getNaturesAcheteurs($connect, $id, $nb_mois);
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

  <?php include('inc/aideProcedures.php');?>
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

<script src="assets/leaflet/leaflet.js"></script>
<script src="assets/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
  var point = [<?php echo $sirene['latitude'] . ", " . $sirene['longitude']; ?>];
  var zoom = 13;
  var map = L.map('mapVertical').setView(point, zoom);
  var tiles = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

  L.tileLayer( tiles, { attribution: attribution } ).addTo(map);

  //// Popup
  L.marker(point).addTo(map)
    .bindPopup("<?php echo $sirene['denominationUniteLegale'] . "<br>" . $sirene['codePostalEtablissement'] . " - " . $sirene['libelleCommuneEtablissement'];?>")
    .openPopup();
</script>

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
<?php
// différents layouts et fonctions pour paramétrer les graphiques
include('inc/plotly-layouts-fonctions.php');

// Code pour générer les 3 graphiques des catégories principales
include('inc/categories-principales-js.php');
?>


/* --------------------------------------
  Acheteurs et titulaires
  --------------------------------------
*/

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

<?php
for ($i=1; $i<5; $i++)
{
  $data = getNatureByDate($connect, $id, $i, $nb_mois);
  echo "barPlot('nature_$i', ["
  . $data['date'] . "], ["
  . $data['total'] . "], "
  . " couleursNatures[" . ($i-1) . "],"
  . " couleursNaturesBorders[" . ($i-1) . "]);\n";
}

/* --------------------------------------
  Procédures utilisées
  --------------------------------------*/
$procedure = getProcedures($connect, $id, $nb_mois);
?>

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


/* --------------------------------------
  Table de marchés
  --------------------------------------*/
  //// format € https://datatables.net/manual/data/renderers#Number-helper

var tableUI = $('#tableUI').DataTable({
  "responsive": true,
  "dom": '<"wrapper"Bfltip>',
  "language": francais,
  "columns": [
    { "data": "id" },
    { "data": "code_cpv" },
    { "data": "acheteur" },
    { "data": "titulaire" },
    { "data": "date", "orderable": false, "width": "10%" },
    { "data": "montant",
    render: $.fn.dataTable.render.number( ' ', '.', 0, '', '€' ) }
  ],
  "paging": true,
  "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
  "order": [[ 2, "asc" ],[ 3, "asc" ]]
});

/* Charger données
function ajax.url().load( callback, resetPaging ) */
//// calculer la date min : aujoud'hui - $nb_mois
<?php
$date_min = date("Y-m", strtotime("-$nb_mois months"));
?>
var url = "data/getRecherche.php?libelle_cpv=&titulaire=0&acheteur=" + <?php echo $id;?> + "&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=" + <?php echo '"' . $date_min . '"';?> + "&date_max=0&forme_prix=0&nature=0&procedure=0&code_cpv=";

tableUI.ajax.url( url ).load( function()
{
  $('#rechercheBouton').removeClass('is-loading');

  // A-t-on des données ?
  if (tableUI.data().length === 0)
  {
    console.log('pas de données');
    $('#rechercheSansResultats').css('display', 'block');
    $('#rechercheResultats').css('display', 'none');
  }
  else
  {
    console.log("On a " + tableUI.data().length + " lignes de données");
    $('#rechercheSansResultats').css('display', 'none');
    $('#rechercheResultats').css('display', 'block');

    //// Récupérer les données de la table et les convertir pour le graphique
    createTimeline(tableUI);
  }

});


/* ---------------------------------------------
| Fenêtre modal                               |
---------------------------------------------
*/
$('#tableUI').on('click', ".voirMarche", function()
{
  $('#modalMessage').css('display', 'none');
  $('#modalMarche #enCharge').css('display', 'block');
  $('#modalMarche').addClass('is-active');
  var id = $(this).attr("data-id");

  $.ajax({
    url: "data/getMarcheJSON.php",
    type : 'POST',
    data : 'id=' + id,
    dataType : 'html',
    success : function(data, statut)
    {
      data = JSON.parse(data);
      // console.log(data);
      $('#m_id').html(data.m_id);
      $('#m_cpv_code').html(data.m_cpv_code);
      $('#m_cpv_libelle').html(data.m_cpv_libelle);
      $('#m_acheteur').html(data.m_acheteur);
      $('#m_acheteur_siret').html(data.m_acheteur_siret);
      // $('#m_acheteur_btn').attr('data-id-acheteur', data.m_acheteur_btn);
      $('#m_titulaire').html(data.m_titulaire);
      // $('#m_titulaire_btn').attr('data-id-titulaire', data.m_titulaire_btn);
      $('#m_titulaire_a').html('<a href="titulaire.php?i='+ data.m_titulaire_btn + '"><i class="fas fa-link"></i>&nbsp;Page du titulaire</a>');
      $('#m_titulaire_siret').html(data.m_titulaire_siret);
      $('#m_procedure').html(data.m_procedure);
      $('#m_nature').html(data.m_nature);
      $('#m_forme_prix').html(data.m_forme_prix);
      $('#m_date_notification').html(data.m_date_notification);
      $('#m_duree').html(data.m_duree);
      $('#m_montant').html(data.m_montant);
      $('#m_lieu').html(data.m_lieu);
      $('#m_objet').html(data.m_objet);

      $('#modalMarche #enCharge').css('display', 'none');
      $('#modalMessage').css('display', 'block');
    },
    error : function(resultat, statut, erreur)
    {
      $('#modalMarche #enCharge').css('display', 'none');
      $('#modalMessage').html("<p>C'est assez génant, mais quelque chose n'a pas fonctionné ...</p>");
    }
  });
});

//// Vider les details du marché lors de la fermeture
var champsDetail = ['#m_id','#m_cpv_libelle','#m_acheteur','#m_acheteur_siret',
'#m_titulaire','#m_titulaire_a','#m_identification','#m_procedure',
'#m_nature','#m_forme_prix','#m_date_notification','#m_duree',
'#m_montant','#m_lieu','#m_objet'];

var viderDetails = function ()
{
  // $('#m_cpv_code').attr("data-cpv", '');
  // $('#m_acheteur_btn').attr('data-id-acheteur', '');
  // $('#m_titulaire_btn').attr('data-id-titulaire', '');
  for (i in champsDetail)
  {
    $(champsDetail[i]).empty();
  }

};

//// Lancer une recherche
$('#modalMarche').on('click', '.plus-acheteur', function ()
{
  $('#modalMarche').removeClass('is-active');
  $('#modalMarche #enCharge').css('display', 'block');
  recherche('acheteur', $(this).attr('data-id-acheteur'));
  viderDetails();
});
$('#modalMarche').on('click', '.plus-titulaire', function ()
{
  $('#modalMarche').removeClass('is-active');
  $('#modalMarche #enCharge').css('display', 'block');
  recherche('titulaire', $(this).attr('data-id-titulaire'));
  viderDetails();
});
$('#modalMarche').on('click', '.plus-cpv', function ()
{
  $('#modalMarche').removeClass('is-active');
  $('#modalMarche #enCharge').css('display', 'block');
  recherche('cpv', $(this).attr('data-cpv'));
  viderDetails();
});

//// Fermer modal
$('.modal-card .delete, .modal-background, #ferme-marche').on('click', function ()
{
  $('#modalMarche').removeClass('is-active');
  $('#modalMarche #enCharge').css('display', 'block');
  viderDetails();
});


/* ---------------------------------------------
| createTimeline                              |
-----------------------------------------------
Récupérer les données de la table et les convertir pour le graphique
*/
var createTimeline = function (t)
{
  var x_serv = [], y_serv = [], text_serv = [];
  var x_trav = [], y_trav = [], text_trav = [];
  var x_four = [], y_four = [], text_four = [];
  var moyenne = 0, moyenne_x = [], moyenne_y = [];
  // var nb_marches = 0, montant_total = 0;
  var marches_sans_date = 0;
  // var montant_max = 0;

  t.data().each( function (d)
  {
    //// Stats

    // marchés qui n'ont pas de date
    // if (d.date_notification === '0000-00-00') marches_sans_date++;
    //
    // // nombre de marchés
    // nb_marches++;
    //
    // // cumul des montants
    // montant_total += parseInt(d.montant);
    //
    // // montant max
    // if (parseInt(d.montant) > montant_max)
    // {
    //   montant_max = parseInt(d.montant);
    // }
    //
    // // moyenne_x stocke les dates. Cela servira au axe et au kpi de la période
    // if (moyenne_x.indexOf(d.date_notification) === -1)
    // {
    //   moyenne_x.push(d.date_notification);
    // }


    switch (d.categorie)
    {
      case 'Fournitures' :
      x_four.push(d.date_notification);
      y_four.push(parseInt(d.montant));
      text_four.push(
        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
      );
      break;

      case 'Travaux' :
      x_trav.push(d.date_notification);
      y_trav.push(parseInt(d.montant));
      text_trav.push(
        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
      );
      break;

      case 'Services' :
      x_serv.push(d.date_notification);
      y_serv.push(parseInt(d.montant));
      text_serv.push(
        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
      );
      break;

    }
  }); // each

  // console.log(text_serv);
  //
  // // stats : moyenne
  // moyenne = (montant_total / nb_marches).toFixed(0);
  // if (isNaN(moyenne))
  // {
  //   moyenne = 0;
  // }

  // stats medianne
  // function median(values)
  // {
  //   values.sort(function(a,b) {return a-b;});
  //
  //   if(values.length ===0) return 0
  //
  //   var half = Math.floor(values.length / 2);
  //
  //   if (values.length % 2) return values[half];
  //   else return (values[half - 1] + values[half]) / 2.0;
  // }

  // stats UI
  // $('#kpi-nb-marches').html(new Intl.NumberFormat('fr-FR').format(nb_marches));
  // $('#kpi-montant-total').html(new Intl.NumberFormat('fr-FR').format(montant_total) + " €");
  // $('#kpi-moyenne').html(new Intl.NumberFormat('fr-FR').format(moyenne) + " €");

  if (marches_sans_date === 0)
  {
    $('#marches-sans-date').html(""); // vider des vieux messages si tout va bien !
  }

  if (marches_sans_date === 1)
  {
    $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a un marché dont la date n'a pas été saisie et qui n'est pas affiché dans le graphique.</p>");
  }

  if (marches_sans_date > 1)
  {
    $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a " + marches_sans_date + " marchés dont la date n'a pas été saisie et qui ne sont pas affichés dans le graphique.</p>");
  }


  // stats : array dimmension y
  // for (i in moyenne_x)
  // {
  //   moyenne_y.push(moyenne);
  // }

  // stats : période. Supprimer les dates non remplies
  // var periode_arr = moyenne_x.filter( function (v, i, a)
  // {
  //   return v != '0000-00-00';
  // });
  // periode_arr = periode_arr.sort();
  // var date_min = new Date(periode_arr.shift());
  // var date_max = new Date(periode_arr.pop());
  // var diff = (date_max.getTime() - date_min.getTime()) / 1000; // secondes
  // diff /= (60 * 60 * 24 * 7 * 4); // mois
  // diff = Math.abs(Math.round(diff));
  // var periode = "";
  //
  //
  // if (isNaN(diff))
  // {
  //   periode = "< 1 mois";
  // }
  // else
  // {
  //   periode = diff + " mois";
  // }
  // $('#kpi-periode').html(periode);
  //
  // // montant max
  // $('#kpi-max').html(new Intl.NumberFormat('fr-FR').format(montant_max) + " €");


  // taille des cercles
  var size_bubble = 20;
  if ( t.data().length > 20 ) size_bubble = 14;
  if ( t.data().length > 40 ) size_bubble = 13;
  if ( t.data().length > 60 ) size_bubble = 12;
  if ( t.data().length > 80 ) size_bubble = 11;
  if ( t.data().length > 100 ) size_bubble = 10;

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
      color : 'rgb(44, 160, 101)',
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
      color : 'rgb(93, 164, 214)',
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
      color : 'rgb(255, 144, 14)',
      line: {
        color: 'rgb(255, 255, 255)',
        width: 1
      },
    },
    text: text_four,
    hoverinfo: 'text'  /// ne pas afficher X & Y
  };

  var moyenne_text = "Moyenne: " + new Intl.NumberFormat('fr-FR').format(parseInt(moyenne_y)) + " €";
  var trace_moyenne ={
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

  var data = [trace_serv, trace_trav, trace_four, trace_moyenne  ];

  var layout = {
    showlegend: true,
    legend: { bgcolor: '#fff', bordercolor: '#f5f5f5', borderwidth: "1" },
    xaxis : { title: { text: "DATE", font: { size: 16, color: '#111' }  } },
    yaxis : { hoverformat: "-.2r€", title: { text: "MONTANT (€)", font: { size: 16, color: '#111' }  } },
    autosize: true,
    height: 600,
    /*width: 920,*/
     autosize: true,
    hovermode:'closest', margin: { l: 50, r: 50, b: 60, t: 40, pad: 0}
  };



  Plotly.newPlot('rechercheTempChart', data, layout, optionsPlotly);
};




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








//// toggle aide nature
$('#aideNatureButton').on('click', function ()
{
  $('#aideNature').toggle();
});
}); // document ready
</script>

<?php include('inc/footer.php'); ?>
