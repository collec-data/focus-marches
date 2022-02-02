
/* -------------------------------
getChart
---------------------------------*/
var getChart1 = function (url, id, type)
{
  d3.json(url).then(function(text)
  {
    var chart = bb.generate(
    {
      bindto: id,
      labels: true,
      data: { type: type, columns: text }
    });
  });
};

/* -------------------------------
getChart
---------------------------------*/
var getChart = function (url, id, type)
{
    var chart = bb.generate(
    {
      bindto: id,
      labels: true,
      data: { type: type, url: url }
    });
};

/* -------------------------------
getLieux
---------------------------------*/
var getLieux1 = function (id)
{
  d3.json("data/lieux.php").then(function(text)
  {
    var chart = bb.generate(
    {
      bindto: id,
      labels: true,
      data: { type: "bar", columns: text },
      bar: { padding: 5 },
      axis: {
        x: { label: "Départements", type: "category", categories: [""] },
        y: { label: "Montant en K€" }
        /*rotated: true*/
      }
    });
  });
};

/* -------------------------------
getLieux
---------------------------------*/
var getLieux = function (id)
{
    return bb.generate(
    {
      data: {
        url: "data/lieuxCSV.php",
        type: "bar",
        labels: true,
        selection: { enabled: true, draggable: true }
      },
      bindto: id,
      bar: { padding: 5 },
      axis: {
        x: { label: "Départements", type: "category", categories: [""] },
        y: { label: "Montant en K€" }
        /*rotated: true*/
      },
      grid: {
        y: {
          show: true,
          lines: [
            {
              value: 200,
              text: 'Moyenne',
              class: "ligneBlue"  } ] } }
    });
};

/* -------------------------------
getLieux subChart
---------------------------------*/
var getLieuxSubChart = function (id)
{
    var chart = bb.generate(
    {
      data: {
        url: "data/lieuxCSV.php",
        type: "bar",
        labels: true,
        selection: { enabled: true, draggable: true }
      },
      bindto: id,
      bar: { padding: 5 },
      axis: {
        x: { label: "Départements", type: "category", categories: [""] },
        y: { label: "Montant en K€" }
        /*rotated: true*/
      },
      grid: { y: { show: true, lines: [ { value: 200, text: 'Moyenne' } ] } },
      subchart: { show: true  }
    });
};

/* -------------------------------
getLieux Zoom
---------------------------------*/
var getLieuxZoom = function (id)
{
    var chart = bb.generate(
    {
      data: {
        url: "data/lieuxCSV.php",
        type: "bar",
        labels: true,
        selection: { enabled: true, draggable: true }
      },
      bindto: id,
      bar: { padding: 5 },
      axis: {
        x: { label: "Départements", type: "category", categories: [""] },
        y: { label: "Montant en K€" }
        /*rotated: true*/
      },
      grid: { y: { show: true, lines: [ { value: 200, text: 'Moyenne' } ] } },
      zoom: { enabled: true }
    });
};

/* -------------------------------
getLieux Zoom Drag
---------------------------------*/
var getLieuxZoomDrag = function (id)
{
    var chart = bb.generate(
    {
      data: {
        columns: [
	["sample", 30, 200, 100, 400, 150, 250, 150, 200, 170, 240, 350, 150, 100, 400, 150, 250, 150, 200, 170, 240, 100, 150, 250, 150, 200, 170, 240, 30, 200, 100, 400, 150, 250, 150, 200, 170, 240, 350, 150, 100, 400, 350, 220, 250, 300, 270, 140, 150, 90, 150, 50, 120, 70, 40]
    ]
      },
      bindto: id,
      // bar: { padding: 5 },
      axis: {
        x: { label: "Départements", type: "category", categories: [""] },
        y: { label: "Montant en K€" }
        /*rotated: true*/
      },
      zoom: { enabled: { type: 'drag' }  }
    });
};
