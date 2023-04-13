
/* --------------------------------------
Graphiques plotly
--------------------------------------*/

var layout = { showlegend: false, autosize: true, hovermode:'closest', yaxis: { automargin: true },
  margin: { l: 50, r: 50, b: 100, t: 100, pad: 4 }
};

var layoutProcedures = { showlegend: false, autosize: true, hovermode:'closest',
margin: { l: 200, r: 20, b: 40, t: 30, pad: 5 }
};

var layoutQui = { showlegend: false, autosize: true, hovermode:'closest', margin: { l: 50, r: 50, b: 120, t: 20, pad: 0 }};

var layoutCols = { showlegend: false, autosize: true, hovermode:'closest',
margin: { l: 20, r: 20, b: 0, t: 0, pad: 5 }, height: 150,
xaxis: { autorange: true, showgrid: false, zeroline: false, showline: false, autotick: true, ticks: '', showticklabels: false },
yaxis: { autorange: true, showgrid: false, zeroline: false, showline: false, autotick: true, ticks: '', showticklabels: false },
/*paper_bgcolor: "#4fb2db ",plot_bgcolor: "#4fb2db"*/
};

var layoutCategories = { showlegend: false, autosize: true, hovermode:'closest', height: 350, margin: { l: 50, r: 50, b: 60, t: 20, pad: 0}, showticklabels: false };


var linePlot = function (id, x, y)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'scatter'}], layoutCols, optionsPlotly );
};

var splinePlot = function (id, x, y)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'scatter',mode: 'lines+markers',line: {shape: 'spline', color: "#4fb2db" }}], layoutCols, {displayModeBar: false} );
};

var barPlot = function (id, x, y, couleur, border)
{
  Plotly.newPlot( id, [{  x: x,  y: y, type: 'bar', marker: {color: couleur,line: { color: border, width: 1 } }}], layoutCols, {displayModeBar: false} );
};

/*
setQui
Fonction pour afficher avec des indicateurs en HTML + barres Plotlyjs
qui achete ou qui réalise les marchés
*/
var setQui = function (id, x, y)
{
  if (x.length == 0)
  {
    console.log('#' + id, ' Pas de données pour ce graphique');
    $('#' + id).html("<span class='pasData'>Il n'y a pas eu de contrat pour cette catégorie</span>");
    return;
  }
var chartData = [{ type: 'bar', y: y, x: x, marker:{ color: okabe_ito_reverse, line: { color: okabe_ito_reverse, width: 1 } }, orientation: 'v'}];
Plotly.newPlot( id, chartData, layoutQui, optionsPlotly);
};
