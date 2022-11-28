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

            <?php
            if ($iframe == false){

                ?>
                <div>
                    <p class="has-text-right">
                        <button class="button has-text-link is-link-bg is-small" data-clipboard-text="http://127.0.0.1:8080/edsa-focus-marches-new/ui/widget-acheteur-procedure-suivi.php?i=24360023600018&widget=1" ><i class="fa fa-code"></i>&nbsp;intégrer le widget</button></p>
                </div>
                <?php
            }
            ?>

        </div>

        <?php
}  ?>



<script>
    <?php
    // différents layouts et fonctions pour paramétrer les graphiques
    include('inc/plotly-layouts-fonctions.php');

    ?>


    /* --------------------------------------
  Nature des marchés
  --------------------------------------*/

    /* --------------------------------------
      Procédures utilisées
      --------------------------------------*/

    <?php
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


</script>
