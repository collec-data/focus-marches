<?php

$iframe = false;
$wiget_param = filter_input(INPUT_GET, 'widget',FILTER_VALIDATE_INT);
if (isset($wiget_param)) {
    $iframe = true;
    if (is_numeric($wiget_param)) {
        $id_iframe = $wiget_param;
    }
}

if ($iframe == true) {
    $title = "Distribution temporelle des marchés";

    include('inc/localization.php');
    include('inc/head.php');
    include('inc/config.php');
    require_once('common/widget/view-utils.php');
    require_once('common/widget/common-functions.php');
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

    if ($secured == true) {
        $id = $id_acheteur_param;
        $date_min = isset($date_min) ? $date_min_param : null;
        $date_max = isset($date_max) ? $date_max_param : null;
        $sirene = getDataSiretAcheteur($connect, $id);
        $revenuMoyenNational = getMedianeNiveauVie($connect);

    }
    // L"affichage de certains éléments dépend de si on est face à une collectivité ou pas
    $colter = false;
    if ($sirene['categorieJuridiqueUniteLegale'] === '7210') {
        $colter = true;
    }

}

if (isset($sirene['siren'])) {
    //override nb_mois
    $nb_mois = nb_mois_calcul($date_min, $date_max, $config);
    ?>

    <?php include('js/common-js.php'); ?>

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

    <title>Distribution temporelle des marchés</title>

    <div class="container">

        <h3>Distribution temporelle des marchés</h3>
        <div id="marches-sans-date"></div>
        <div id="rechercheTempChart"></div>
        <div id="datesMontantsLieuData"></div>
        <?php include('inc/aideRecherche.php'); ?>


        <?php
        if ($iframe == false) {

            $url = strtok("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');

            $iframe_code_gen = "<iframe ";
            $iframe_code_gen .= "src=\"$url/../widget-acheteur-distrib-temporelle.php?i=";
            $iframe_code_gen .= $id;
            $iframe_code_gen .= isset($date_min) ? "&date_min=" . $date_min : "";
            $iframe_code_gen .= isset($date_max) ? "&date_max=" . $date_max : "";
            $iframe_code_gen .= "&widget=1\" ";
            $iframe_code_gen .= "referrerpolicy=\"strict-origin-when-cross-origin\" ";
            $iframe_code_gen .= "style=\"border: 0;\" ";
            $iframe_code_gen .= "width=\"100%\" height=\"600px\">";
            $iframe_code_gen .= "</iframe>";
            $iframe_code_1 = $iframe_code_gen

                ?>
            <div>
                <p class="has-text-right">
                    <button class="btnCopy button has-text-link is-link-bg is-small"
                        data-clipboard-text='<?php echo $iframe_code_1; ?>'><i class="fa fa-code"></i>&nbsp;intégrer le
                        widget</button>
                </p>
            </div>
            <?php
        }
        ?>
    </div>

    <?php
} ?>

<script>
    // différents layouts et fonctions pour paramétrer les graphiques


    /* --------------------------------------
      Acheteurs et titulaires
      --------------------------------------
    */

    <?php

    include('inc/plotly-layouts-fonctions.php');
    ?>
    /* ---------------------------------------------
    | createTimeline                              |
    -----------------------------------------------
    Récupérer les données de la table et les convertir pour le graphique
    */
    var createTimeline = function (t) {
        var x_serv = [], y_serv = [], text_serv = [];
        var x_trav = [], y_trav = [], text_trav = [];
        var x_four = [], y_four = [], text_four = [];
        var moyenne = 0, moyenne_x = [], moyenne_y = [];
        // var nb_marches = 0, montant_total = 0;
        var marches_sans_date = 0;
        // var montant_max = 0;

        for (const d of t.data) {

            switch (d.categorie) {
                case 'Fournitures':
                    x_four.push(d.date_notification);
                    y_four.push(parseInt(d.montant));
                    text_four.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

                case 'Travaux':
                    x_trav.push(d.date_notification);
                    y_trav.push(parseInt(d.montant));
                    text_trav.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

                case 'Services':
                    x_serv.push(d.date_notification);
                    y_serv.push(parseInt(d.montant));
                    text_serv.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

            }
        }


        if (marches_sans_date === 0) {
            $('#marches-sans-date').html(""); // vider des vieux messages si tout va bien !
        }

        if (marches_sans_date === 1) {
            $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a un marché dont la date n'a pas été saisie et qui n'est pas affiché dans le graphique.</p>");
        }

        if (marches_sans_date > 1) {
            $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a " + marches_sans_date + " marchés dont la date n'a pas été saisie et qui ne sont pas affichés dans le graphique.</p>");
        }


        // taille des cercles
        var size_bubble = 20;
        if (t.data.length > 20) {size_bubble = 14;}
        if (t.data.length > 40) {size_bubble = 13;}
        if (t.data.length > 60) {size_bubble = 12;}
        if (t.data.length > 80)  {size_bubble = 11; }
        if (t.data.length > 100) { size_bubble = 10; }

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
                color: 'rgb(44, 160, 101)',
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
                color: 'rgb(93, 164, 214)',
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
                color: 'rgb(255, 144, 14)',
                line: {
                    color: 'rgb(255, 255, 255)',
                    width: 1
                },
            },
            text: text_four,
            hoverinfo: 'text'  /// ne pas afficher X & Y
        };

        var moyenne_text = "Moyenne: " + new Intl.NumberFormat('fr-FR').format(parseInt(moyenne_y)) + " €";
        var trace_moyenne = {
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

        var data = [trace_serv, trace_trav, trace_four, trace_moyenne];

        var layout = {
            showlegend: true,
            legend: { bgcolor: '#fff', bordercolor: '#f5f5f5', borderwidth: "1" },
            xaxis: { title: { text: "DATE", font: { size: 16, color: '#111' } } },
            yaxis: { hoverformat: "-.2r€", title: { text: "MONTANT (€)", font: { size: 16, color: '#111' } } },
            autosize: true,
            height: 600,
            /*width: 920,*/
            autosize: true,
            hovermode: 'closest', margin: { l: 50, r: 50, b: 60, t: 40, pad: 0 }
        };



        Plotly.newPlot('rechercheTempChart', data, layout, optionsPlotly);
    };

    <?php
    $date_min_tab = isset($date_min) ? $date_min : date("Y-m", strtotime("-$nb_mois months"));
    $date_max_tab = isset($date_max) ? $date_max : 0;
    ?>

    function ajaxCallTemporel() {

        const url_s = "data/getRecherche.php?libelle_cpv=&titulaire=0&acheteur=" + <?php echo $id; ?> + "&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=" + <?php echo '"' . $date_min_tab . '"'; ?> + "&date_max=" + <?php echo '"' . $date_max_tab . '"'; ?> + "&forme_prix=0&nature=0&procedure=0&code_cpv="
        console.log(url_s);
        $.ajax({
            // Our sample url to make request
            url: url_s,

            // Type of Request
            type: "GET",

            // Function to call when to
            // request is ok
            success: function (data) {
                createTimeline(data)
            },

            // Error handling
            error: function (error) {
            }
        });
        
    }
    ajaxCallTemporel();

    /* --------------------------------------
      Table de marchés
      --------------------------------------*/
    //// format € https://datatables.net/manual/data/renderers#Number-helper


    //// toggle aide charte
    $('#rechercheTempAide').on('click', function () {
        $('#rechercheTempContenu').toggle();
    });

</script>