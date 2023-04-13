<?php
include('inc/localization.php');

$page = "recherche";
$title = "Recherche | Marchés publics en " . gettext("NOM_REGION");
$desc = "Recherche | Marchés publics en " . gettext("NOM_REGION");

include('inc/head.php');
include('inc/config.php');
?>
<!-- entre heads : ajouter extra css , ... -->

<?php include('inc/nav.php'); ?>

<div id="main">
  <div class="container">
    <h1 class="title">Explorez les marchés publics</h1>
    <p>Quel est l'historique d'achat d'une collectivité ? Ai-j'ai obtenu un bon prix pour mes marchés ?</p>
    <div class="columns">
      <div class="column is-four-fifths">
        <p class="is-clearfix">Découvrez les réponses à ces questions et à bien d'autres avec ces filtres de recherche.
        </p>
      </div>
      <div class="column has-text-right"><button id="aideRechercheButton"
          class="button has-text-link is-link-bg is-small"><i class="far fa-lightbulb"></i>&nbsp;Idées de recherches
        </button></div>
    </div>

    <div id="aideRecherche" class="aide">

      <div class="columns">
        <div class="column">
          <article class="message is-link">
            <div class="message-body has-text-justified">
              <p class="has-text-centered"><i class="fas fa-history fa-3x"></i></p>
              <p class="message-title">Quel est l'historique d'achat d'une collectivité ? </p>
              <p>Commencez à écrire le nom de la collectivité dans la boîte "acheteur". Au fur et mesure, des
                propositions vont appraître.</p>
              <p>Dès que vous allez répérer la collectivité qui vous intéresse, cliquez sur celle-ci, puis sur le bouton
                bleu "Chercher". L'historique de ses achat s'affichera sur le tableau en bas de page.</p>
            </div>
          </article>

        </div>
        <div class="column">
          <article class="message is-link">
            <div class="message-body has-text-justified">
              <p class="has-text-centered"><i class="fab fa-angellist fa-3x"></i></p>
              <p class="message-title">Comment savoir si j'ai obtenu un bon prix ? </p>
              <p>Les conditions des achats ne sont jamais identiques, mais vous pouvez lister tous les marchés ayant un
                code CPV proche du votre.</p>
              <p>La recherche sur le code CPV fonctionne de gauche à droite : si vous tapez "45" vous aurez tous les
                marchés de la catégorie "Travaux", tandis que si vous tapez "45261410" vous limiterez la recherche aux
                "Travaux d'isolation de toiture".</p>
            </div>
          </article>

        </div>
        <div class="column">
          <article class="message is-link">
            <div class="message-body has-text-justified">
              <p class="has-text-centered"><i class="fas fa-microscope fa-3x"></i></p>
              <p class="message-title">Quels marchés ont été passés par l'ancienne équipe&nbsp;?</p>
              <p>Renseignez d'abord la case "acheteur" avec le nom de la commune. Ensuite, cliquez sur "avancé". Vous
                verrez alors deux cases vous permettant de delimiter la période de recherche.</p>
            </div>
          </article>
        </div>
      </div>
    </div>

    <div id="rechercheForm">

      <div id="rechercheOptionsWrapper" class="is-clearfix">
        <div class="tags has-addons is-pulled-right">
          <span id="rechercheSimple" class="tag is-info" title="Recherche simple avec options limitées">Simple</span>
          <span id="rechercheAvancee" class="tag is-white"
            title="Recherche avancée avec toutes les options">Avancée</span>
        </div>
      </div>

      <div class="columns">

        <div class="ajaxContainer column">
          <input id="in_id_acheteur" type="hidden" value="">
          <p><label>Acheteur <i class="fas fa-question-circle has-text-grey-light"
                title="Commencez à saissir le nom de l'acheteur et sélectionnez-le dans la liste"></i></label>
            <input id="in_nom_acheteur" type="text" value="" placeholder="Organisme qui a créé la consultation">
            <span id="acheteur_select"></span>
          </p>
        </div>

        <div class="ajaxContainer column">
          <input id="in_id_titulaire" type="hidden" value="">
          <p>
            <label>Fournisseur <i class="fas fa-question-circle has-text-grey-light"
                title="Commencez à saissir le nom du fournisseur et sélectionnez-le dans la liste"></i></label>
            <input id="in_denomination_sociale" type="text" value="" placeholder=<?php echo gettext("Entité ayant gagné le marché") ?>>
            <span id="denomination_select"></span>
          </p>
        </div>

        <div class="column">
          <p>
            <label>Objet <i class="fas fa-question-circle has-text-grey-light"
                title="Saisissez 1 ou plusieurs mots-clés devant apparaître dans l'objet du marché, séparés par des virgules"></i></label>
            <input id="in_objet" type="text" value="" placeholder="Ex: ordinateur, tablette">
          </p>
        </div>
      </div> <!-- ./ columns -->

      <div id="rechercheOptions" class="is-clearfix">

        <div class="columns">
          <div class="column">
            <p>
              <label>Code CPV </label>
              <input id="in_code_cpv" type="text" value="" placeholder="Ex: 45, ou 4511, ou 451125 ...">
            </p>
          </div>

          <div class="column">
            <p>
              <label>Libellé CPV <i class="fas fa-question-circle has-text-grey-light"
                  title="Saisissez 1 ou plusieurs mots-clés séparés par des virgules"></i></label>
              <input id="in_libelle_cpv" type="text" value="" placeholder="Ex: ordinateur, tablette">
            </p>
          </div>

          <div class="column">
            <p><label>Lieu</label>
              <select id="in_lieu">
                <option value="0">Tous les départements</option>
                <option value="21">21 - Côte d'Or</option>
                <option value="25">25 - Doubs</option>
                <option value="39">39 - Jura</option>
                <option value="58">58 - Nièvre</option>
                <option value="70">70 - Haute-Saône</option>
                <option value="71">71 - Saône-et-Loire</option>
                <option value="89">89 - Yonne</option>
                <option value="90">90 - Territoire de Belfort</option>
              </select>
            </p>
          </div>
        </div> <!-- ./ columns -->

        <div class="columns">
          <div class="column">
            <p><label>Forme de prix</label>
              <select id="in_forme_prix">
                <option value="0">Toutes</option>
                <option value="1">Ferme</option>
                <option value="2">Ferme et actualisable</option>
                <option value="3">Révisable</option>
              </select>
            </p>
          </div>

          <div class="column">
            <p><label>Type de marché</label>
              <select id="in_nature">
                <option value="0">Toutes</option>
                <option value="1">Marché</option>
                <option value="2">Marché de partenariat</option>
                <option value="3">Accord-cadre</option>
                <option value="4">Marché subséquent</option>
              </select>
            </p>
          </div>

          <div class="column">
            <p><label>Procédure</label>
              <select id="in_procedure">
                <option value="0">Toutes</option>
                <option value="1">Procédure adaptée</option>
                <option value="2">Appel d'offres ouvert</option>
                <option value="3">Appel d'offres restreint</option>
                <option value="4">Procédure concurrentielle avec négociation</option>
                <option value="5">Procédure négociée avec mise en concurrence préalable</option>
                <option value="6">Marché négocié sans publicité ni mise en concurrence préalable</option>
                <option value="7">Procédure concurrentielle avec négociation</option>
              </select>
            </p>
          </div>
        </div> <!-- ./ columns -->

        <div class="columns">

          <div class="column">
            <div class="columns">
              <div class="column">
                <p>
                  <label>Montant min.</label>
                  <input id="in_montant_min" type="text" value="" placeholder="€">
                </p>
              </div>
              <div class="column">
                <p>
                  <label>Montant max.</label>
                  <input id="in_montant_max" type="text" value="" placeholder="€">
                </p>
              </div>
            </div>
          </div>

          <div class="column">
            <div class="columns">
              <div class="column">
                <p>
                  <label>Durée min.</label>
                  <input id="in_duree_min" type="text" value="" type="number" placeholder="mois">
                </p>
              </div>
              <div class="column">
                <p>
                  <label>Durée max.</label>
                  <input id="in_duree_max" type="text" value="" type="number" placeholder="mois">
                </p>
              </div>
            </div>
          </div>

          <div class="column">
            <div class="columns">
              <div class="column">
                <p>
                  <label>Date min.</label>
                  <input id="in_date_min" type="text" value="" type="number" placeholder="aaaa-mm-jj">
                </p>
              </div>
              <div class="column">
                <p>
                  <label>Date max.</label>
                  <input id="in_date_max" type="text" value="" type="number" placeholder="aaaa-mm-jj">
                </p>
              </div>
            </div>
          </div>
        </div> <!-- ./ columns -->
      </div><!--rechercheOptions -->

      <div class="column">
        <button id="rechercheBouton" class="button is-info is-fullwidth" type="button" role="button"
          aria-label="search">Chercher</button>
      </div>

      <p class="has-text-centered is-size-9 has-text-grey">Cliquez sur "Chercher" pour afficher tous les marchés.
        Limitez la recherche en utilisant les différents filtres</p>

    </div><!-- ./rechercheForm -->

    <div id="rechercheSansResultats">
      <h3>Il n'y a pas de résultats pour ces critères</h3>
      <p>Nous sommes désolés mais aucun des marchés semble correspondre à ce que vous cherchez.</p>
      <p>Essayez à élargir vos critères et relancez la recherche.</p>
    </div>
  </div>

  <div id="rechercheResultats">

    <div class="container">
      <h3>Indicateurs clés de votre recherche</h3>
      <div class="kpis columns">
        <div class="column kpi">
          <i class="far fa-calendar-alt fa-3x has-text-blue-light pb-1"></i>
          <span
            title="Différence exprimée en mois entre la date de consultation la plus recente et la date de consultation la plus ancienne.">Période</span>
          <b id="kpi-periode"></b>
        </div>
        <div class="column kpi">
          <i class="far fa-handshake fa-3x has-text-blue-light pb5"></i>
          <span title="Nombre de contrats">Nb de contrats</span>
          <b id="kpi-nb-marches"></b>
        </div>
        <div class="column kpi">
          <i class="fas fa-calculator fa-3x has-text-blue-light"></i>
          <span>Montant total</span>
          <b id="kpi-montant-total"></b>
        </div>
        <div class="column kpi">
          <!-- <i class="far fas fa-thermometer-half fa-3x has-text-blue-light"></i> -->
          <i class="fas fa-divide fa-3x has-text-blue-light "></i>
          <span>Montant moyen</span>
          <b id="kpi-moyenne"></b>
        </div>
        <div class="column kpi">
          <!-- <i class="far fas fa-thermometer-full fa-3x has-text-blue-light"></i> -->
          <i class="fas fa-mountain fa-3x has-text-blue-light "></i>
          <span>Montant max.</span>
          <b id="kpi-max"></b>
        </div>
      </div>
    </div>

    <div class="container">
      <h3>Distribution temporelle des marchés</h3>

      <div id="marches-sans-date">
      </div>

      <div id="rechercheTempChart"></div>
      <?php include('inc/aideRecherche.php'); ?>
    </div>


    <div class="container">
      <h3 id="titreTableUI">Toutes les données de votre recherche</h3>
      <p class="pb-40 is-size-9">Ce tableau affiche les principales informations des marchés de votre sélection. Cliquez
        sur "Voir" pour accéder au détail de chaque marché. </p>
      <table class="display table table-striped table-bordered table-hover dataTable no-footer" id="tableUI"
        style="width:100%">
        <thead>
          <tr>
            <th width="7%">Détails</th>
            <th width="38%">CPV <i class="fas fa-question-circle has-text-grey-light"
                title="Le vocabulaire commun des marchés publics ou CPV (Common Procurement Vocabulary) est composé de codes normalisés, utilisés pour décrire l’objet des contrats à l’aide d’un système unique de classification pour les marchés publics."></i>
            </th>
            <th width="20%">Acheteur</th>
            <th width="20%">Fournisseur</th>
            <th width="10%">Date</th>
            <th width="10%">Montant</th>
          </tr>
        </thead>
      </table>
    </div>

  </div> <!-- recherche Resultats -->
</div>

<!-- fenêtre modal pour afficher les marchés sans perdre sa recherche -->

<div id="modalMarche" class="modal">
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
            <!-- <p id="modalMessageLogo" class="has-text-centered"></p>
            <p class="is-size-9 clair mt-15">n°: <span id="m_id"></span></p> -->
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
                  <span id="m_acheteur"></span> <span id="m_acheteur_siret" class="siret"></span>
                </p>
                <p>
                  <span id="m_acheteur_btn" class="tag is-btn-a plus-acheteur "
                    title="Chercher tous les marchés de cet acheteur" data-id-acheteur=""><i
                      class="fas fa-table"></i>&nbsp;Tous ses marchés</span>
                  <span id="m_acheteur_a" class="link"></span>
                </p>
              </div>

              <!-- Colonne 2 B -->
              <div class="column">
                <h4>Fournisseur</h4>
                <p>
                  <span id="m_titulaire"></span> <span id="m_titulaire_siret" class="siret"></span>
                </p>
                <p>
                  <span id="m_titulaire_btn" class="tag is-btn-a plus-titulaire "
                    title="Chercher tous les marchés gagnés par ce fournisseur" data-id-titulaire=""><i
                      class="fas fa-table"></i>&nbsp;Tous ses marchés</span>
                  <span id="m_titulaire_a" class="link"></span>
                </p>
              </div>
            </div><!-- ./Colonnes 2A et 2B -->
            <div id="m_wrap_cpv">
              <h4>Code CPV : </h4>
              <p id="m_cpv_libelle"></p>
              <p><span id="m_cpv_code" class="tag is-btn-a plus-cpv"
                  title="Chercher tous les marchés qui correspondent à ce code CPV" data-cpv=""><i
                    class="fas fa-table"></i>&nbsp;Marchés avec ce code</span></p>
            </div>
            <h4>Objet</h4>
            <p id="m_objet"></p>
          </div>
        </div>

        <!-- <button id="ferme-marche" class="has-text-centered button is-btn-marron">Fermer</button> -->
    </section>
  </div><!-- ./ modalMessage -->
</div>
</div> <!-- ./ main -->

<?php include('js/common-js.php'); ?>

<!-- <script src="assets/datatables/datatables.min.js"></script> -->
<script src="assets/datatables/jquery.dataTables.min.js"></script>
<script src="assets/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
<script src="assets/datatables/dataTables.buttons.min.js"></script>
<script src="assets/datatables/buttons.flash.min.js"></script>
<script src="assets/datatables/jszip.min.js"></script>
<script src="assets/datatables/pdfmake.min.js"></script>
<script src="assets/datatables/vfs_fonts.js"></script>
<script src="assets/datatables/buttons.html5.min.js"></script>
<script src="assets/datatables/buttons.print.min.js "></script>

<script src="js/recherche.js"></script>

<?php include('inc/footer.php'); ?>