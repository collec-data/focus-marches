<?php

$iframe = false;
if(isset($_GET['widget'])) {
$iframe = true;
if (is_numeric($_GET['widget'])) {
$id_iframe = $_GET['widget'];
}
///// Sécurisation
$secured = false;
if (is_numeric($_GET['i'])) {
     $secured = true;
}

if (isset($_GET['date_min']) && is_date($_GET['date_min']) && $secured == true) {
    $date_min = $_GET['date_min'];
    $secured = true;
}
  
if (isset($_GET['date_max']) && is_date($_GET['date_max']) && $secured == true) {
    $date_min = $_GET['date_min'];
    $secured = true;
}


}


if ($iframe == true){

    $title ="Distribution par catégorie principale d'achat";
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

    $connect->set_charset("utf8");

///// Sécurisation
    $secured = false;
    if (is_numeric($_GET['i'])) $secured = true;

    if (isset($_GET['date_min']) && is_date($_GET['date_min']) && $secured == true) {
        $date_min = $_GET['date_min'];
        $secured = true;
      }
      
      if (isset($_GET['date_max']) && is_date($_GET['date_max']) && $secured == true) {
        $date_max = $_GET['date_max'];
        $secured = true;
      }

    if ($secured == true)
    {
        $id = $_GET['i'];
        $sirene = getDataSiretAcheteur($connect, $id);
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
            <?php
            //// 2. Distribution par catégorie principale d'achat
            $date_min = isset($date_min) ? $date_min : null;
            $date_max = isset($date_max) ? $date_max : null;
            $cats = getCategoriesPrincipales ($connect, $nb_mois, $id, "acheteur", $date_min, $date_max);
            include('inc/categories-principales-html.php');
            ?>
            <?php
             if ($iframe == false){

                $url=strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?');

                 $iframe_code_gen="<iframe ";
                 $iframe_code_gen.= "src=\"$url/../widget-acheteur-distribution.php?i=";
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


<?php
}   ?>

<script>



    <?php
    // différents layouts et fonctions pour paramétrer les graphiques
    include('inc/plotly-layouts-fonctions.php');

    // Code pour générer les 3 graphiques des catégories principales
    include('inc/categories-principales-js.php');
    ?>

</script>
