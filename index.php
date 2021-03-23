<!DOCTYPE html>
<html>
<head>
<title>Tir</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
<style type="text/css">

#chart-container, .chart-container {
    width: 100%;
    height: auto;
}

body {
	width: 100%;
}

h1 {
	font-size: 30px;
	font-weight: bold;
}

.main-intro-container.box {
	margin-top: 30px;
}

.tile.is-ancestor {
  flex-wrap:wrap;
}

.tile.is-6 {
	padding: 20px;
}

.tile.is-12 {
	margin-bottom: 20px;
	margin-top: 20px;
}

.tile.is-child.notification.is-primary {
	max-width: 100%;
}

.chart-container {
	padding-bottom: 20px;
}

.chart-container p {
	font-size: 20px;
	text-align: center;
	font-weight: bold;
}

</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script>

</head>
<body>
  <script>

    var slideNames = [];



  <?php
  /* Get number of slides and populate display */

  $mysqli = new mysqli("localhost","contpnjf_tir","ravenclaw123","contpnjf_tir");

  if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
  }

  $sqlQuery = "SELECT DISTINCT `SlideName` FROM `TABLE 1` ORDER BY `SlideName` ASC";

  $result = mysqli_query($mysqli,$sqlQuery);

  foreach ($result as $row) {
  	?>
      slideNames.push("<?php echo $row['SlideName']; ?>");
    <?php
  }

  $row2 = mysqli_num_rows($result);

  mysqli_close($mysqli);

  ?>

    var rowNum = <?php echo $row2; ?>;
  </script>

  <div class="container main-display">

    <div class="main-intro-container box">
      <h1>TIR</h1>
      <p>An analysis tool for simultaneously viewing multiple slides of GeoMx DSP data.</p>
      <button class="button" id="downloadPdf">Export PDF</button>
    </div>

    <div class="tile is-ancestor">

      <?php
        for ($i = 1; $i <= $row2; $i++) {
          //echo $i;
          ?>

          <div class="tile is-12">
            <article class="tile is-child notification is-primary">
              <div id="chart-container<?php echo $i; ?>" class="chart-container">
                  <p></p>
                  <canvas id="graphCanvas<?php echo $i; ?>"></canvas>

              </div>

              <nav class="level">
                <div class="level-item has-text-centered">
                  <div class="select is-link">
                    <select id="first-select-<?php echo $i; ?>" class="first-select-box" numbox="<?php echo $i; ?>">
                      <option value="SegmentDisplayName">SegmentDisplayName</option>
                      <option value="SlideName">SlideName</option>
                      <option value="ScanName">ScanName</option>
                      <option value="ROILabel">ROILabel</option>
                      <option value="SegmentLabel">SegmentLabel</option>
                      <option value="Sample_ID">Sample_ID</option>
                    </select>
                  </div>
                </div>
                <div class="level-item has-text-centered">
                  <div class="select is-link">
                    <select id="second-select-<?php echo $i; ?>" class="second-select-box" numbox="<?php echo $i; ?>">
                      <option value="NormalizationFactor">NormalizationFactor</option>
                      <option value="disease_status">disease_status</option>
                      <option value="AOISurfaceArea">AOISurfaceArea</option>
                      <option value="AOINucleiCount">AOINucleiCount</option>
                      <option value="ROICoordinateX">ROICoordinateX</option>
                      <option value="ROICoordinateY">ROICoordinateY</option>
                      <option value="RawReads">RawReads</option>
                      <option value="TrimmedReads">TrimmedReads</option>
                      <option value="StitchedReads">StichedReads</option>
                      <option value="AlignedReads">AlignedReads</option>
                      <option value="DeduplicatedReads">DeduplicatedReads</option>
                      <option value="SequencingSaturation">SequencingSaturation</option>
                      <option value="UMIQ30">UMIQ30</option>
                      <option value="RTSQ30">RTSQ30</option>
                      <option value="LOQ">LOQ</option>
                    </select>
                  </div>
                </div>
              </nav>


            </article>
          </div>

          <?php
        }
      ?>

    </div>
  </div>

    <script>
        $(document).ready(function () {
          var currentContainer = 1;
          var graphPrefix = "graphCanvas";
          slideNames.forEach(function(item, index, array) {
            showGraph(item,'chart-container'+currentContainer,graphPrefix+currentContainer,'bar','SegmentDisplayName','NormalizationFactor');
            $("#"+graphPrefix+currentContainer).prev('p').html(item);
            currentContainer++;
          })

        });

        $( ".first-select-box" ).change(function() {
          let currentGraphID = $(this).parent().parent().parent().parent().find('canvas').attr('id');
          let currentSelection = $(this).val();
          let currentSlide = $("#"+currentGraphID).prev('p').html();
          let numBox = $(this).attr('numbox');

          $("#"+currentGraphID).prev('p').remove();
          $("#"+currentGraphID).remove();
          $('#chart-container'+numBox).append('<p>'+currentSlide+'</p><canvas id="'+currentGraphID+'"><canvas>');

          showGraph(currentSlide,'chart-container',currentGraphID,'bar',currentSelection, $('#second-select-'+numBox).val() );
        });

        $( ".second-select-box" ).change(function() {
          let currentGraphID = $(this).parent().parent().parent().parent().find('canvas').attr('id');
          let currentSelection = $(this).val();
          let currentSlide = $("#"+currentGraphID).prev('p').html();
          let numBox = $(this).attr('numbox');

          $("#"+currentGraphID).prev('p').remove();
          $("#"+currentGraphID).remove();
          $('#chart-container'+numBox).append('<p>'+currentSlide+'</p><canvas id="'+currentGraphID+'"><canvas>');

          showGraph(currentSlide,'chart-container',currentGraphID,'bar', $('#first-select-'+numBox).val() ,currentSelection);

        });

        function showGraph(slideName,containerName,graphName,graphType,value1,value2)
        {
            {
                $.post("data.php?slide="+slideName,
                function (data)
                {
                    console.log(data);
                    var name = [];
                    var marks = [];

                    for (var i in data) {
                        switch(value1){
                          case "SegmentDisplayName":
                            name.push(data[i].SegmentDisplayName);
                          break;

                          case "ROILabel":
                            name.push(data[i].ROILabel);
                          break;

                          case "SegmentLabel":
                            name.push(data[i].SegmentLabel);
                          break;

                          case "Sample_ID":
                            name.push(data[i].Sample_ID);
                          break;


                        }

                        switch(value2){
                          case "NormalizationFactor":
                            marks.push(data[i].NormalizationFactor);
                          break;

                          case "disease_status":
                            marks.push(data[i].disease_status);
                          break;

                          case "AOISurfaceArea":
                            marks.push(data[i].AOISurfaceArea);
                          break;

                          case "AOINucleiCount":
                            marks.push(data[i].AOINucleiCount);
                          break;

                          case "ROICoordinateX":
                            marks.push(data[i].ROICoordinateX);
                          break;

                          case "ROICoordinateY":
                            marks.push(data[i].ROICoordinateY);
                          break;

                          case "RawReads":
                            marks.push(data[i].RawReads);
                          break;

                          case "TrimmedReads":
                            marks.push(data[i].TrimmedReads);
                          break;

                          case "StichedReads":
                            marks.push(data[i].StichedReads);
                          break;

                          case "AlignedReads":
                            marks.push(data[i].AlignedReads);
                          break;

                          case "DeduplicatedReads":
                            marks.push(data[i].DeduplicatedReads);
                          break;

                          case "SequencingSaturation":
                            marks.push(data[i].SequencingSaturation);
                          break;

                          case "UMIQ30":
                            marks.push(data[i].UMIQ30);
                          break;

                          case "RTSQ30":
                            marks.push(data[i].RTSQ30);
                          break;

                          case "LOQ":
                            marks.push(data[i].LOQ);
                          break;

                        }


                    }

                    var chartdata = {
                        labels: name,
                        datasets: [
                            {
                                label: 'data',
                                backgroundColor: '#49e2ff',
                                borderColor: '#46d5f1',
                                hoverBackgroundColor: '#CCCCCC',
                                hoverBorderColor: '#666666',
                                data: marks
                            }
                        ]
                    };

                    var graphTarget = $("#" + graphName);

                    var barGraph = new Chart(graphTarget, {
                        type: graphType,
                        data: chartdata
                    });





                });
            }
        }


        $('#downloadPdf').click(function(event) {

          var reportPageHeight = 1000;
          var reportPageWidth = 1000;

          var pdfCanvas = $('<canvas />').attr({
            id: "canvaspdf",
            width: reportPageWidth,
            height: reportPageHeight
          });

          var pdfctx = $(pdfCanvas)[0].getContext('2d');
          var pdfctxX = 0;
          var pdfctxY = 0;
          var buffer = 100;

          $("canvas").each(function(index) {

            var canvasHeight = $(this).innerHeight();
            var canvasWidth = $(this).innerWidth();

            pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
            pdfctxX += canvasWidth + buffer;

            if (index % 2 === 1) {
              pdfctxX = 0;
              pdfctxY += canvasHeight + buffer;
            }
          });


          var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
          pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 0);

          pdf.save('tir_export.pdf');
        });
        </script>

</body>
</html>
