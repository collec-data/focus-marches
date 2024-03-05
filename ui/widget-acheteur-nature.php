<?php

$iframe = false;
$wiget_param = filter_input(INPUT_GET, 'widget',FILTER_VALIDATE_INT);
if (isset($wiget_param)) {
    $iframe = true;
    if (is_numeric($wiget_param)) {
        $id_iframe = $wiget_param;
    }
}

if ($iframe == true){
    $title ="Nature des marchés";

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
    require_once('data/validateurs.php');
    require_once('common/widget/view-utils.php');
    require_once('common/widget/common-functions.php');

    $connect->set_charset("utf8");

///// Sécurisation
    $secured = false;
    $id_acheteur_param = filter_input(INPUT_GET, 'i',FILTER_VALIDATE_INT);
    $date_min_param = filter_input(INPUT_GET,'date_min');
    $date_max_param = filter_input(INPUT_GET,'date_max');

    if ($id_acheteur_param && isset($id_acheteur_param) && is_numeric($id_acheteur_param))
        $secured = true;

    if (isset($date_min_param) && is_date($date_min_param) && $secured == true) {
        $date_min = $date_min_param;
        $secured = true;
    }
        
        if (isset($date_max_param) && is_date($date_max_param) && $secured == true) {
        $date_max = $date_max_param;
        $secured = true;
    }

    if ($secured == true)
    {
        $id = $_GET['i'];
        $date_min = isset($date_min) ? $date_min_param : null;
        $date_max = isset($date_max) ? $date_max_param : null;
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
        //override nb_mois
        $nb_mois = nb_mois_calcul($date_min, $date_max, $config);
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
            <h3>Nature des marchés</h3>
            <p>Répartition des contrats par nature du marché public, en montant et en nombre. Ci-dessous les marchés sont groupés par mois et <?php echo texte_html_selon_periode($nb_mois,$date_min,$date_max)?></p>
            <div class="columns sequence">
                <?php
                $cats = getCategoriesPrincipales ($connect, $nb_mois, $id, "acheteur", $date_min, $date_max);
                $natures = getNaturesAcheteurs($connect, $id, $nb_mois, $date_min, $date_max);
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

            <?php
            if ($iframe == false){

                $url=strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?');

                $iframe_code_gen="<iframe ";
                $iframe_code_gen.= "src=\"$url/../widget-acheteur-nature.php?i=";
                $iframe_code_gen.=$id;
                $iframe_code_gen .= isset($date_min) ? "&date_min=" . $date_min : "";
                $iframe_code_gen .= isset($date_max) ? "&date_max=" . $date_max : "";
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
        <?php
}  ?>



<script>

    //// toggle aide nature
    $('#aideNatureButton').on('click', function ()
    {
        $('#aideNature').toggle();
    });

    <?php
    // différents layouts et fonctions pour paramétrer les graphiques
    include('inc/plotly-layouts-fonctions.php');

    ?>


/* --------------------------------------
  Nature des marchés
  --------------------------------------*/

    <?php
    for ($i=1; $i<5; $i++)
    {
        $data = getNatureByDate($connect, $id, $i, $nb_mois, $date_min, $date_max);
        echo "barPlot('nature_$i', ["
            . $data['date'] . "], ["
            . $data['total'] . "], "
            . " couleursNatures[" . ($i-1) . "],"
            . " couleursNaturesBorders[" . ($i-1) . "]);\n";
    }

    ?>


</script>
