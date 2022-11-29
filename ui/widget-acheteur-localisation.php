
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

    $title ="Localisation et contexte";
    $desc =" Widget Localisation et contexte";
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
<!--                        --><?php //if ($colter) {?>
<!--                            <li>-->
<!--                                <a id="tab2"><i class="fas fa-users"></i>&nbsp; La population</a>-->
<!--                            </li>-->
<!--                        --><?php //} ?>
<!--                    </ul>-->
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
<!--                <div class="tabContainer" id="tab2C">-->
<!--                    <table class="table is-striped is-fullwidth">-->
<!--                        <tr>-->
<!--                            <td>Population (2015)</td><td colspan='3'>--><?php //echo $sirene['pop_2015'];?><!-- habitants</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>Ménages</td><td>--><?php //echo $sirene['menages'];?><!-- ménages</td>-->
<!--                            <td title="Population de 15 ans ou plus Agriculteurs exploitants en 2015">Agriculteurs</td><td>--><?php //echo $sirene['agriculteurs'] . pf($sirene['pop_2015'], $sirene['agriculteurs']);?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td title="Population de 15 ans ou plus Artisans, Commerçants, Chefs entreprise en 2015">Artisans, chefs</td><td>--><?php //echo $sirene['artisans_chefs'] . pf($sirene['pop_2015'], $sirene['artisans_chefs']);?><!--</td>-->
<!--                            <td title="Population de 15 ans ou plus Cadres ou professions intellectuelles supérieures en 2015">Cadres</td><td>--><?php //echo $sirene['cadres'] . pf($sirene['pop_2015'], $sirene['cadres']);?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td title="Population de 15 ans ou plus professions Intermediaires en 2015">Intermediaires</td><td>--><?php //echo $sirene['intermediaires'] . pf($sirene['pop_2015'], $sirene['intermediaires']);?><!--</td>-->
<!--                            <td title="Population de 15 ans ou plus Employés en 2015">Employés</td><td>--><?php //echo $sirene['employes'] . pf($sirene['pop_2015'], $sirene['employes']);?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td title="Population de 15 ans ou plus Retraités en 2015">Retraités</td><td>--><?php //echo $sirene['retraites'] . pf($sirene['pop_2015'], $sirene['retraites']);?><!--</td>-->
<!--                            <td title="Population de 15 ans ou plus Autres sans activité professionnelle en 2015">Autres</td><td>--><?php //echo $sirene['autres'] . pf($sirene['pop_2015'], $sirene['autres']);?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td title="Le niveau de vie est égal au revenu disponible du ménage divisé par le nombre d'unités de consommation (uc). Le niveau de vie est donc le même pour tous les individus d'un même ménage. Les unités de consommation sont généralement calculées selon l'échelle d'équivalence dite de l'OCDE modifiée qui attribue 1 uc au premier adulte du ménage, 0,5 uc aux autres personnes de 14 ans ou plus et 0,3 uc aux enfants de moins de 14 ans. (Source: INSEE)">Médiane niveau vie</td><td colspan='3'>-->
<!--                                --><?php
//                                if (is_numeric($sirene['mediane_niveau_vie']))
//                                {
//                                    echo nf( $sirene['mediane_niveau_vie'] ) . " € (" . nf( $revenuMoyenNational ) . " €) <span title='moyenne des mediannes de toutes les communes'>à niveau nationnal</span>";
//                                }
//                                else
//                                {
//                                    echo 'non disponible';
//                                }
//                                ?><!--</td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                    <p>Survolez les rubriques pour voir leur définiton</p>-->
<!--                </div>-->
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
        if ($iframe == false){

            $url=strtok("$protocol$_SERVER[HTTP_HOST]",'?');
            $iframe_code_gen="<iframe ";
            $iframe_code_gen.= "src=\"$url/$path_prefix/ui/widget-acheteur-localisation.php?i=";
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

<?php
}
