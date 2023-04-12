<?php

$iframe = false;
if(isset($_GET['widget'])) {
$iframe = true;
if (is_numeric($_GET['widget'])) {
$id_iframe = $_GET['widget'];
}
///// Sécurisation
$secured = false;
if (is_numeric($_GET['i'])) $secured = true;
}

if ($iframe == true){

    $title ="Tous les marchés";

    include('inc/head.php');
    include('inc/config.php');
    include('inc/localization.php');
    ?>
    <!-- entre heads : ajouter extra css , ... -->
    <link rel="stylesheet" href="assets/leaflet/leaflet.css" />

 <?php
//    include('inc/nav.php');
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
    // L"affichage de certains éléments dépend de si on est face à une collectivité ou pas
    $colter = false;
    if ($sirene['categorieJuridiqueUniteLegale'] === '7210')
    {
        $colter = true;
    }

}

    if (isset($sirene['siren']))
    {
    ?>

        <?php include('js/common-js.php');?>

        <script src="assets/leaflet/leaflet.js"></script>
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





        <?php
        //// Qui achète ?
        $titulairesTotal       = getTitulairesList($connect, 12, null, $id, $nb_mois);
        $titulairesServices    = getTitulairesList($connect, 12, 'services', $id, $nb_mois);
        $titulairesTravaux     = getTitulairesList($connect, 12, 'travaux', $id, $nb_mois);
        $titulairesFournitures = getTitulairesList($connect, 12, 'fournitures', $id, $nb_mois);
        ?>

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



            <?php
            if ($iframe == false){

                $url=strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?');

                $iframe_code_gen="<iframe ";
                $iframe_code_gen.= "src=\"$url/../widget-acheteur-tous-marches.php?i=";
                $iframe_code_gen.=$id;
                $iframe_code_gen.="&widget=1\" ";
                $iframe_code_gen.= "referrerpolicy=\"strict-origin-when-cross-origin\" ";
                $iframe_code_gen.= "style=\"border: 0;\" ";
                $iframe_code_gen.= "width=\"100%\" height=\"600px\">";
                $iframe_code_gen.= "</iframe>";
                $iframe_code_1 = $iframe_code_gen

                ?>
                <div>
                    <p class="has-text-right">
                        <button class="btnCopy button has-text-link is-link-bg is-small" data-clipboard-text='<?php echo $iframe_code_1;?>' ><i class="fa fa-code"></i>&nbsp;intégrer le widget</button></p>
                </div>
                <?php
            }
            ?>

        </div>


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


        <?php
}   ?>

<script>
    // différents layouts et fonctions pour paramétrer les graphiques


    /* --------------------------------------
      Acheteurs et titulaires
      --------------------------------------
    */

    <?php

    include('inc/plotly-layouts-fonctions.php');
    ?>



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
</script>
