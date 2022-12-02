<title>Localisation et contexte</title>

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
    $title ="Distribution temporelle des marchés";

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

        <title>Distribution temporelle des marchés</title>

        <div class="container">

            <h3>Distribution temporelle des marchés</h3>
            <div id="marches-sans-date"></div>
            <div id="rechercheTempChart"></div>
            <div id="datesMontantsLieuData"></div>
            <?php include ('inc/aideRecherche.php');?>


            <?php
            if ($iframe == false){

                $url=strtok("$protocol$_SERVER[HTTP_HOST]",'?');
                $iframe_code_gen="<iframe ";
                $iframe_code_gen.= "src=\"$url/widget-acheteur-distrib-temporelle.php?i=";
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
    <?php
    $date_min = date("Y-m", strtotime("-$nb_mois months"));
    ?>



    var createTimeline = function (t)
    {
        var x_serv = [], y_serv = [], text_serv = [];
        var x_trav = [], y_trav = [], text_trav = [];
        var x_four = [], y_four = [], text_four = [];
        var moyenne = 0, moyenne_x = [], moyenne_y = [];
        // var nb_marches = 0, montant_total = 0;
        var marches_sans_date = 0;
        // var montant_max = 0;

        for (const d of t.data) {

            switch (d.categorie)
            {
                case 'Fournitures' :
                    x_four.push(d.date_notification);
                    y_four.push(parseInt(d.montant));
                    text_four.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

                case 'Travaux' :
                    x_trav.push(d.date_notification);
                    y_trav.push(parseInt(d.montant));
                    text_trav.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

                case 'Services' :
                    x_serv.push(d.date_notification);
                    y_serv.push(parseInt(d.montant));
                    text_serv.push(
                        d.acheteur + "<br>" + d.libelle_cpv + "<br>"
                        + "<b>" + new Intl.NumberFormat('fr-FR').format(d.montant) + " €</b><br>"
                    );
                    break;

            }
        }


        // console.log(text_serv);
        //
        // // stats : moyenne
        // moyenne = (montant_total / nb_marches).toFixed(0);
        // if (isNaN(moyenne))
        // {
        //   moyenne = 0;
        // }

        // stats medianne
        // function median(values)
        // {
        //   values.sort(function(a,b) {return a-b;});
        //
        //   if(values.length ===0) return 0
        //
        //   var half = Math.floor(values.length / 2);
        //
        //   if (values.length % 2) return values[half];
        //   else return (values[half - 1] + values[half]) / 2.0;
        // }

        // stats UI
        // $('#kpi-nb-marches').html(new Intl.NumberFormat('fr-FR').format(nb_marches));
        // $('#kpi-montant-total').html(new Intl.NumberFormat('fr-FR').format(montant_total) + " €");
        // $('#kpi-moyenne').html(new Intl.NumberFormat('fr-FR').format(moyenne) + " €");

        if (marches_sans_date === 0)
        {
            $('#marches-sans-date').html(""); // vider des vieux messages si tout va bien !
        }

        if (marches_sans_date === 1)
        {
            $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a un marché dont la date n'a pas été saisie et qui n'est pas affiché dans le graphique.</p>");
        }

        if (marches_sans_date > 1)
        {
            $('#marches-sans-date').html("<p class='is-size-9'><i class='fas fa-exclamation-circle has-text-danger'></i> Il y a " + marches_sans_date + " marchés dont la date n'a pas été saisie et qui ne sont pas affichés dans le graphique.</p>");
        }


        // stats : array dimmension y
        // for (i in moyenne_x)
        // {
        //   moyenne_y.push(moyenne);
        // }

        // stats : période. Supprimer les dates non remplies
        // var periode_arr = moyenne_x.filter( function (v, i, a)
        // {
        //   return v != '0000-00-00';
        // });
        // periode_arr = periode_arr.sort();
        // var date_min = new Date(periode_arr.shift());
        // var date_max = new Date(periode_arr.pop());
        // var diff = (date_max.getTime() - date_min.getTime()) / 1000; // secondes
        // diff /= (60 * 60 * 24 * 7 * 4); // mois
        // diff = Math.abs(Math.round(diff));
        // var periode = "";
        //
        //
        // if (isNaN(diff))
        // {
        //   periode = "< 1 mois";
        // }
        // else
        // {
        //   periode = diff + " mois";
        // }
        // $('#kpi-periode').html(periode);
        //
        // // montant max
        // $('#kpi-max').html(new Intl.NumberFormat('fr-FR').format(montant_max) + " €");


        // taille des cercles
        var size_bubble = 20;
        if ( t.data.length > 20 ) size_bubble = 14;
        if ( t.data.length > 40 ) size_bubble = 13;
        if ( t.data.length > 60 ) size_bubble = 12;
        if ( t.data.length > 80 ) size_bubble = 11;
        if ( t.data.length > 100 ) size_bubble = 10;

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
                color : 'rgb(44, 160, 101)',
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
                color : 'rgb(93, 164, 214)',
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
                color : 'rgb(255, 144, 14)',
                line: {
                    color: 'rgb(255, 255, 255)',
                    width: 1
                },
            },
            text: text_four,
            hoverinfo: 'text'  /// ne pas afficher X & Y
        };

        var moyenne_text = "Moyenne: " + new Intl.NumberFormat('fr-FR').format(parseInt(moyenne_y)) + " €";
        var trace_moyenne ={
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

        var data = [trace_serv, trace_trav, trace_four, trace_moyenne  ];

        var layout = {
            showlegend: true,
            legend: { bgcolor: '#fff', bordercolor: '#f5f5f5', borderwidth: "1" },
            xaxis : { title: { text: "DATE", font: { size: 16, color: '#111' }  } },
            yaxis : { hoverformat: "-.2r€", title: { text: "MONTANT (€)", font: { size: 16, color: '#111' }  } },
            autosize: true,
            height: 600,
            /*width: 920,*/
            autosize: true,
            hovermode:'closest', margin: { l: 50, r: 50, b: 60, t: 40, pad: 0}
        };



        Plotly.newPlot('rechercheTempChart', data, layout, optionsPlotly);
    };



    function ajaxCall() {
        $.ajax({
            // Our sample url to make request
            url : "data/getRecherche.php?libelle_cpv=&titulaire=0&acheteur=" + <?php echo $id;?> + "&lieu=0&objet=&montant_min=0&montant_max=0&duree_min=0&duree_max=0&date_min=" + <?php echo '"' . $date_min . '"';?> + "&date_max=0&forme_prix=0&nature=0&procedure=0&code_cpv=",

            // Type of Request
            type: "GET",

            // Function to call when to
            // request is ok
            success: function (data) {
                createTimeline(data)
            },

            // Error handling
            error: function (error) {
                console.log(`Error ${error}`);
            }
        });
    }
    ajaxCall();

    /* --------------------------------------
      Table de marchés
      --------------------------------------*/
    //// format € https://datatables.net/manual/data/renderers#Number-helper


    //// toggle aide charte
    $('#rechercheTempAide').on('click', function ()
    {
        $('#rechercheTempContenu').toggle();
    });

</script>