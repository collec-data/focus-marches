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

    $title ="Indicateurs clés";
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
        $date_min = isset($date_min) ? $date_min_param : null;
        $date_max = isset($date_max) ? $date_max_param : null;
        $nom = getNom($connect, $id_acheteur_param);
        $kpi = getKPI($connect, $id_acheteur_param, $nb_mois, 0, $date_min, $date_max);
        $marches = getDatesMontantsLieu($connect, $id_acheteur_param, $nb_mois, $date_min, $date_max);
        $sirene = getDataSiretAcheteur($connect, $id_acheteur_param);
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
            <h3>Indicateurs clés</h3>
            <p>Principaux indicateurs <?php echo texte_html_selon_periode($nb_mois,$date_min,$date_max)?> </p>
            <div class="kpis columns">
                <div class="column kpi">
                    <i class="far fa-calendar-alt fa-3x has-text-blue-light pb-1"></i>
                    <span>Période</span>
                    <b id="kpi-periode"><?php echo ceil( $kpi['periode'] ) > 0 ? ceil( $kpi['periode'] ) : "<1" ?> mois</b>
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


            <?php
            if ($iframe == false){

                $url=strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?');
                $iframe_code_gen="<iframe ";
                $iframe_code_gen.= "src=\"$url/../widget-acheteur-indicateurs.php?i=";
                $iframe_code_gen.=$id_acheteur_param;
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
                        <button class="btnCopy button has-text-link is-link-bg is-small " data-clipboard-text='<?php echo $iframe_code_1;?>' ><i class="fa fa-code"></i>&nbsp;intégrer le widget</button></p>
                </div>
                <?php
            }
            ?>
        </div>










<?php
}



