<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="index, follow">
        <meta name="keywords" content="votaieti, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
        <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:title" content="Votaieti">
        <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:image" content="../imgs/votaietilogo.png">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="author" content="Arnau Mestre, Claudia Moyano i Henry Doudo">
        <title>Votaieti</title>
        <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
        <link rel="stylesheet" href="styles.css">
        <script src="../styles + scripts/script.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    </head>

    <!--Load the AJAX API-->
    
  <body>
  <div class="contenedorHeader">
        <?php include 'header.php'; ?>
    </div>          
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Move', 'Percentage'],
          ["King's pawn (e4)", 44],
          ["Queen's pawn (d4)", 31],
          ["Knight to King 3 (Nf3)", 12],
          ["Queen's bishop pawn (c4)", 10],
          ['Other', 3]
        ]);

        var options = {
          width: 800,
          legend: { position: 'none' },
          chart: {
            title: 'Chess opening moves',
            subtitle: 'popularity by percentage' },
          axes: {
            x: {
              0: { side: 'top', label: 'White to move'} // Top x-axis.
            }
          },
          bar: { groupWidth: "90%" }
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        // Convert the Classic options to Material options.
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };
    </script>
     <div id="chart_div"></div>
  <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>
  </body>
</html>