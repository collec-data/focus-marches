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

    $title ="Qui a réalisé les marchés";

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




            <?php
            if ($iframe == false){


                $url=strtok("$protocol$_SERVER[HTTP_HOST]",'?');
                $iframe_code_gen="<iframe ";
                $iframe_code_gen.= "src=\"$url/widget-acheteur-qui-realise.php?i=";
                $iframe_code_gen.=$id;
                $iframe_code_gen.="&widget=1\" ";
                $iframe_code_gen.= "referrerpolicy=\"strict-origin-when-cross-origin\" ";
                $iframe_code_gen.= "style=\"border: 0;\" ";
                $iframe_code_gen.= "title=\"Widget Localisation et contexte\" width=\"100%\" height=\"600px\">";
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




</script>
