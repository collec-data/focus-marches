<?php
/*
IE ne connaît pas fetch : utiliser un polyfill
https://stackoverflow.com/questions/44242051/script5009-fetch-is-undefined

You can use https://github.com/github/fetch instead.
CDN: https://cdnjs.com/libraries/fetch

<script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/2.0.3/fetch.js"></script>

If you need Promise polyfill you can use http://bluebirdjs.com/docs/install.html

<script src="//cdn.jsdelivr.net/bluebird/3.5.0/bluebird.min.js"></script>

Load Bluebird before fetch:

<script src="//cdn.jsdelivr.net/bluebird/3.5.0/bluebird.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/2.0.3/fetch.js"></script>

*/
?>
<!-- <script src="assets/polyfill/bluebird.min.js"></script>
<script src="assets/polyfill/fetch.js"></script> -->

<script src="assets/jquery/jquery-3.3.1.min.js"></script>
<script src="assets/d3js/d3.v5.min.js"></script>
<script src="assets/plotly/plotly-1.43.1.min.js"></script>
<!-- <script src="https://cdn.plot.ly/plotly-locale-fr-latest.js"></script> -->
<script>
// var config = {locale: 'fr'};
//// locale en FR

// Plotly.register(
//   {
//     moduleType:"locale",
//     name:"fr-CH",
//     dictionary:{},
//     format:{
//       days:["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"],
//       shortDays:["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"],
//       months:["Janvier","F\xe9vrier","Mars","Avril","Mai","Juin","Juillet","Ao\xfbt","Septembre","Octobre","Novembre","D\xe9cembre"],
//       shortMonths:["Jan","F\xe9v","Mar","Avr","Mai","Jun","Jul","Ao\xfb","Sep","Oct","Nov","D\xe9c"],
//       date:"%d.%m.%Y"}
//     });
//
// Plotly.setPlotConfig({locale: 'fr-CH'});
Plotly.register({moduleType:"locale",name:"fr",dictionary:{Autoscale:"\xc9chelle automatique","Box Select":"S\xe9lection rectangulaire","Click to enter Colorscale titre":"Ajouter un titre \xe0 l'\xe9chelle de couleurs","Click to enter Component A titre":"Ajouter un titre \xe0 la composante A","Click to enter Component B titre":"Ajouter un titre \xe0 la composante B","Click to enter Component C titre":"Ajouter un titre \xe0 la composante C","Click to enter Plot titre":"Ajouter un titre au graphique","Click to enter X axis titre":"Ajouter un titre \xe0 l'axe des x","Click to enter Y axis titre":"Ajouter un titre \xe0 l'axe des y","Click to enter radial axis title":"Ajouter un titre \xe0 l'axe radial","Compare data on hover":"Comparaison entre donn\xe9es en survol","Double-click on legend to isolate one trace":"Double-cliquer sur la l\xe9gende pour isoler une s\xe9rie","Double-click to zoom back out":"Double-cliquer pour d\xe9zoomer","Download plot as a png":"T\xe9l\xe9charger le graphique en fichier PNG","Edit in Chart Studio":"\xc9diter le graphique sur plot.ly","IE only supports svg.  Changing format to svg.":"IE ne permet que les conversions en SVG. Conversion en SVG en cours.","Lasso Select":"S\xe9lection lasso","Orbital rotation":"Rotation orbitale",Pan:"Translation","Produced with Plotly":"G\xe9n\xe9r\xe9 avec Plotly",Reset:"R\xe9initialiser","Reset axes":"R\xe9initialiser les axes","Reset camera to default":"R\xe9gler la cam\xe9ra \xe0 sa valeur d\xe9faut","Reset camera to last save":"R\xe9gler la cam\xe9ra \xe0 sa valeur sauvegard\xe9e","Reset view":"R\xe9initialiser","Reset views":"R\xe9initialiser","Show closest data on hover":"Donn\xe9es les plus proches en survol","Snapshot succeeded":"Conversion r\xe9ussie","Sorry, there was a problem downloading your snapshot!":"D\xe9sol\xe9, un probl\xe8me est survenu lors du t\xe9l\xe9chargement de votre graphique","Taking snapshot - this may take a few seconds":"Conversion en cours, ceci peut prendre quelques secondes",Zoom:"Zoom","Zoom in":"Zoom int\xe9rieur","Zoom out":"Zoom ext\xe9rieur","close:":"fermeture :",trace:"s\xe9rie","lat:":"lat. :","lon:":"lon. :","q1:":"q1 :","q3:":"q3 :","source:":"source :","target:":"destination :","lower fence:":"cl\xf4ture sup\xe9rieure :","upper fence:":"cl\xf4ture inf\xe9rieure :","max:":"max. :","mean \xb1 \u03c3:":"moyenne \xb1 \u03c3 :","mean:":"moyenne :","median:":"m\xe9diane :","min:":"min. :","new text":"nouveau texte","Turntable rotation":"Rotation planaire","Toggle Spike Lines":"Activer/d\xe9sactiver les pics","open:":"ouverture :","high:":"haut :","low:":"bas :","Toggle show closest data on hover":"Activer/d\xe9sactiver le survol","incoming flow count:":"entrées :","outgoing flow count:":"sorties :","kde:":"est. par noyau :"},
format:{days:["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"],shortDays:["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"],months:["Janvier","F\xe9vrier","Mars","Avril","Mai","Juin","Juillet","Ao\xfbt","Septembre","Octobre","Novembre","D\xe9cembre"],shortMonths:["Jan","F\xe9v","Mar","Avr","Mai","Jun","Jul","Ao\xfb","Sep","Oct","Nov","D\xe9c"],date:"%d/%m/%Y",decimal:",",thousands:" ",year:"%Y",month:"%b %Y",dayMonth:"%-d %b",dayMonthYear:"%-d %b %Y"}});

Plotly.setPlotConfig({locale: 'fr'});


    // https://community.plot.ly/t/remove-options-from-the-hover-toolbar/130/2
    var optionsPlotly =
    {
      showSendToCloud: false,
      modeBarButtonsToRemove: ['pan2d', 'lasso2d', 'hoverClosestCartesian', 'hoverCompareCartesian', 'toggleSpikelines'],
      displaylogo: false,
      responsive: true
    };

var okabe_ito = [
  "rgba(0, 110, 30, 0.3)",
  "rgba(0, 158, 115, 0.3)",
  "rgba(204, 121, 167, 0.3)",
  "rgba(0, 114, 178, 0.3)",
  "rgba(86, 180, 233, 0.3)",
  "rgba(213, 94, 0, 0.3)",
  "rgba(230, 159, 0, 0.3)",
  "rgba(240, 228, 66, 0.3)"
];


var okabe_ito_reverse = [
  "rgba(240, 228, 66, 0.3)",
  "rgba(230, 159, 0, 0.3)",
  "rgba(213, 94, 0, 0.3)",
  "rgba(244, 67, 54, 0.4)",
  "rgba(86, 180, 233, 0.3)",
  "rgba(33, 150, 243, 0.3)",
  "rgba(0, 114, 178, 0.3)",
  "rgba(204, 121, 167, 0.3)",
  "rgba(139, 195, 74, 0.3)",
  "rgba(0, 158, 115, 0.3)",
  "rgba(0, 110, 30, 0.3)",
  "rgba(121, 85, 72, 0.4)"
];

var okabe_ito_reverse_border = [
  "rgba(240, 228, 66, 0.9)",
  "rgba(230, 159, 0, 0.6)",
  "rgba(213, 94, 0, 0.6)",
  "rgba(244, 67, 54, 0.6)",
  "rgba(86, 180, 233, 0.6)",
  "rgba(33, 150, 243, 0.6)",
  "rgba(0, 114, 178, 0.6)",
  "rgba(204, 121, 167, 0.6)",
  "rgba(139, 195, 74, 0.6)",
  "rgba(0, 158, 115, 0.6)",
  "rgba(0, 110, 30, 0.6)",
  "rgba(121, 85, 72, 0.6)"
];

/* Nodes services & départements */
var okabe_ito_sankey = [ "rgba(44, 160, 101, 0.6)", "rgba(93, 164, 214, 0.6)", "rgba(253, 177, 88, 0.6)", "#666", "#666", "#666", "#666", "#666", "#666", "#666","#666",];

var okabe_ito_sankey_border = [ "rgba(44, 160, 101, 0.3)",  "rgba(44, 160, 101, 0.3)", "rgba(44, 160, 101, 0.3)",  "rgba(44, 160, 101, 0.3)", "rgba(44, 160, 101, 0.3)",  "rgba(44, 160, 101, 0.3)", "rgba(44, 160, 101, 0.3)",  "rgba(44, 160, 101, 0.3)", "rgba(93, 164, 214, 0.3)",  "rgba(93, 164, 214, 0.3)", "rgba(93, 164, 214, 0.3)",  "rgba(93, 164, 214, 0.3)", "rgba(93, 164, 214, 0.3)",  "rgba(93, 164, 214, 0.3)", "rgba(93, 164, 214, 0.3)",  "rgba(93, 164, 214, 0.3)", "rgba(253, 177, 88, 0.3)",  "rgba(253, 177, 88, 0.3)", "rgba(253, 177, 88, 0.3)",  "rgba(253, 177, 88, 0.3)", "rgba(253, 177, 88, 0.3)",  "rgba(253, 177, 88, 0.3)", "rgba(253, 177, 88, 0.3)",  "rgba(253, 177, 88, 0.3)"];

var okabe_ito_sankey_border_link = [  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(0, 114, 178, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(230, 159, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",  "rgba(213, 94, 0, 0.3)",];

var stack = [
  "rgba(240, 228, 66, 0.9)",
  "rgba(230, 159, 0, 0.9)",
  "rgba(213, 94, 0, 0.9)",
  "rgb(255, 204, 153)",
  "rgb(255, 102, 102)",
  "rgba(86, 180, 233, 0.9)",
  "rgb(51, 51, 255)",
  "rgb(153, 0, 0)",
  "rgb(0, 0, 102)",
  "rgb(120, 102, 75)",
  "rgb(51, 51, 51)",
  // "rgb(153, 153, 153)",
    // "rgb(204, 204, 255)",
];

var okabe_ito_border = [
  "rgba(0, 110, 30, 0.9)",
  "rgba(0, 158, 115, 0.9)",
  "rgba(204, 121, 167, 0.9)",
  "rgba(0, 114, 178, 0.9)",
  "rgba(86, 180, 233, 0.9)",
  "rgba(213, 94, 0, 0.9)",
  "rgba(230, 159, 0, 0.9)",
  "rgba(240, 228, 66, 1)",
  "rgba(250, 230, 190, 0.9)",
  "rgba(130, 20, 160, 0.9)",
  "rgba(170, 10, 60, 0.9)"
];
// var materialize4 = [
//   "rgba(255, 99, 132, 0.4)",
//   "rgba(255, 159, 64, 0.4)",
//   "rgba(255, 205, 86, 0.4)",
//   "rgba(75, 192, 192, 0.4)",
//   "rgba(153, 102, 255, 0.4)",
//   "rgba(201, 203, 207, 0.4)",
//   "rgba(255,202,40 ,0.4)",
//
//
//   "rgba(255,238,88 ,0.4)", "rgba(255,167,38 ,0.4)", "rgba(255,112,67 ,0.4)",
//   "rgba(239,83,80 ,0.4)", "rgba(236,64,122 ,0.4)",
//   "rgba(171,71,188 ,0.4)", "rgba(126,87,194 ,0.4)", "rgba(92,107,192 ,0.4)", "rgba(66,165,245 ,0.4)", "rgba(41,182,246 ,0.4)",
//   "rgba(38,198,218 ,0.4)", "rgba(38,166,154 ,0.4)", "rgba(102,187,106 ,0.4)", "rgba(156,204,101 ,0.4)", "rgba(212,225,87 ,0.4)",
//   "rgba(141,110,99 ,0.4)", "rgba(189,189,189 ,0.4)", "rgba(120,144,156 ,0.4)"
// ];

var borderColorArray = [
  "rgb(255, 99, 132)",
  "rgb(255, 159, 64)",
  "rgb(255, 205, 86)",
  "rgb(75, 192, 192)",
  "rgb(153, 102, 255)",
  "rgb(201, 203, 207)",
  "rgb(255, 238, 88)",
  "rgb(255,202,40)",
  "rgba(255,167,38)",
  "rgba(255,112,67)", "rgba(239,83,80)", "rgba(236,64,122)",
"rgba(171,71,188)", "rgba(126,87,194)", "rgba(92,107,192)", "rgba(66,165,245)", "rgba(41,182,246)",
"rgba(38,198,218)", "rgba(38,166,154)", "rgba(102,187,106)", "rgba(156,204,101)", "rgba(212,225,87)",
"rgba(141,110,99)", "rgba(189,189,189)", "rgba(120,144,156)"];
var francais =
{
  "sProcessing":     "Traitement en cours...",
  "sSearch":         "Filtrer les résultats&nbsp;",
  "sLengthMenu":     "Afficher _MENU_ marchés",
  "sInfo":           "Affichage du marché _START_ &agrave; _END_ sur _TOTAL_ marchés",
  "sInfoEmpty":      "Affichage du marché 0 &agrave; 0 sur 0 marchés",
  "sInfoFiltered":   "(filtr&eacute; de _MAX_ marchés au total)",
  "sInfoPostFix":    "",
  "sLoadingRecords": "Chargement en cours...",
  "sZeroRecords":    "Aucun marché &agrave; afficher",
  "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
  "oPaginate": {
    "sFirst":      "Premier",
    "sPrevious":   "Pr&eacute;c&eacute;dent",
    "sNext":       "Suivant",
    "sLast":       "Dernier"
  },
  "oAria": {
    "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
    "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
  }
};
var francais_neutre =
{
  "sProcessing":     "Traitement en cours...",
  "sSearch":         "Filtrer les résultats&nbsp;",
  "sLengthMenu":     "Afficher _MENU_ lignes",
  "sInfo":           "Affichage de la ligne _START_ &agrave; _END_ sur _TOTAL_ ",
  "sInfoEmpty":      "Affichage de la ligne 0 &agrave; 0 sur 0 ",
  "sInfoFiltered":   "(filtr&eacute; de _MAX_ lignes au total)",
  "sInfoPostFix":    "",
  "sLoadingRecords": "Chargement en cours...",
  "sZeroRecords":    "Aucune ligne &agrave; afficher",
  "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
  "oPaginate": {
    "sFirst":      "Premier",
    "sPrevious":   "Pr&eacute;c&eacute;dent",
    "sNext":       "Suivant",
    "sLast":       "Dernier"
  },
  "oAria": {
    "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
    "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
  }
};

var couleursNatures = ["#cce9f8", "#fbb4af", "#f7e2b3", "#b3d3bb"];
var couleursNaturesBorders = ["#9cd0f9", "#faa6a0", "#f3d084", "#99c5a6"];
</script>

<script src="js/main.js"></script>
<script>

/*
getMonthDiff
-----------------------------------------
Différence en mois entre 2 dates
*/
var getMonthDiff = function (d1, d2)
{
    var months;
    months = (d2.getFullYear() - d1.getFullYear()) * 12;
    months -= d1.getMonth();
    months += d2.getMonth();
    return months <= 0 ? 0 : months;
}

$(document).ready(function() {

  // Check for click events on the navbar burger icon
  $(".navbar-burger").click(function()
  {
      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      $(".navbar-burger").toggleClass("is-active");
      $(".navbar-menu").toggleClass("is-active");
  });


  /* Tabs
  --------------- */
  $('#acheteurs li').click(function()
  {
    var tab_id = $(this).attr('data-tab');

    $('#acheteurs li').removeClass('current');
    $('#acheteurs .tab-content').removeClass('current');
    $(this).addClass('current');
    $("#"+tab_id).addClass('current');
  });

  $('#titulaires li').click(function()
  {
    var tab_id = $(this).attr('data-tab');

    $('#titulaires li').removeClass('current');
    $('#titulaires .tab-content').removeClass('current');
    $(this).addClass('current');
    $("#"+tab_id).addClass('current');
  });




  // Animation du h1

var h1 = $('#h1Fixe');

$(window).scroll(function()
{
   if($(window).scrollTop() > 100)
   {
       h1.addClass('show');
   }
   else if(h1.hasClass('show') && $(window).scrollTop() < 150)
   {
      h1.removeClass('show');
   }
});

  // Animation du footer

// var footer = $('#footer');
//
// $(window).scroll(function()
// {
//    if($(window).scrollTop() + $(window).height() > $(document).height() - 100)
//    {
//        footer.addClass('show');
//    }
//    else if(footer.hasClass('show') && $(window).scrollTop() + $(window).height() > $(document).height() - 150)
//    {
//       footer.removeClass('show');
//    }
// });

});
</script>
