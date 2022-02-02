
/* --------------------------------------
Categorie Principale
--------------------------------------*/
var listTout = {
  type: 'scatter', fill: "tozeroy", fillcolor: '#dadada', mode: 'lines', line: {color:'#ccc'},
  x: [<?php echo implode(',',$cats->tousMarches['dates']); ?>],
  y: [<?php echo implode(',',$cats->tousMarches['montants']); ?>],
};

///// listServices
var listServicesData = [listTout, {
  type: 'scatter', fill: "tozeroy", fillcolor: 'rgba(44, 160, 101,0.7)', mode: 'lines', line: {color:'rgb(44, 160, 101)'},
  x: [<?php echo implode(',',$cats->services['dates']); ?>],
  y: [<?php echo implode(',',$cats->services['montants']); ?>],
}];
Plotly.newPlot( 'listServices', listServicesData, layoutCategories, optionsPlotly);

///// listTravaux
var listTravauxData = [listTout, {
  type: 'scatter', fill: "tozeroy", fillcolor: 'rgba(93, 164, 214,0.7)', mode: 'lines', line: {color:'rgb(93, 164, 214)'},
  x: [<?php echo implode(',',$cats->travaux['dates']); ?>],
  y: [<?php echo implode(',',$cats->travaux['montants']); ?>]
}];
Plotly.newPlot( 'listTravaux', listTravauxData, layoutCategories, optionsPlotly);

///// listFournitures
var listFournituresData = [listTout, {
  type: 'scatter', fill: "tozeroy", fillcolor: 'rgba(255, 144, 14,0.7)', mode: 'lines', line: {color:'rgb(255, 144, 14)'},
  x: [<?php echo implode(',',$cats->fournitures['dates']); ?>],
  y: [<?php echo implode(',',$cats->fournitures['montants']); ?>]
}];
Plotly.newPlot( 'listFournitures', listFournituresData, layoutCategories, optionsPlotly);
