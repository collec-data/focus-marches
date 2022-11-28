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

                ?>
                <div>
                    <p class="has-text-right">
                        <button class="button has-text-link is-link-bg is-small" data-clipboard-text="http://127.0.0.1:8080/edsa-focus-marches-new/ui/widget-acheteur-qui-realise.php?i=24360023600018&widget=1" ><i class="fa fa-code"></i>&nbsp;intégrer le widget</button></p>
                </div>
                <?php
            }
            ?>

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



</script>
