<?php

/** @var yii\web\View $this */

$this->title = "NSU weather"; ?>

      <div class="information-block">
          <p class="weather-parameter"><span class="weather-data"><?php echo $data['dayOfTheWeek']; ?></span></p>
          <p class="weather-parameter degree"><?php echo $data['lastRecordData']['temperature']; ?> °C</p>
          
          <p class="weather-parameter">Влажность <span class="weather-data"><?php echo $data['lastRecordData']['humidity']; ?></span> %</p>
          <p class="weather-parameter">Давление <span class="weather-data"><?php echo $data['lastRecordData']['pressure']; ?></span> мм рт. ст.</p>
          <p class="weather-parameter">Частицы pm2 <span class="weather-data"><?php echo $data['lastRecordData']['pm2']; ?></span> мкг/м³ <?php echo $data['pm2level']; ?></p>
          <p class="weather-parameter">Частицы pm10 <span class="weather-data"><?php echo $data['lastRecordData']['pm10']; ?></span> мкг/м³ <?php echo $data['pm10level']; ?></p>
          
      </div>
      
      <div class="information-block">
          <p class="weather-parameter">Средняя температура составила <?php echo round($data['average'], 1); ?>°C</p>
          
          <div class="ct-chart ct-perfect-fourth ct-series-a ct-line"></div>
      </div>
     
      
        <div class="button-block">
            <form>
              <button class="choose-period" id="days3">За 3 дня</button>
              <button class="choose-period" id="days10">За 10 дней</button>
              <button class="choose-period" id="daysMonth">За месяц</button>
            </form>
        </div>

<script>
    var dateChart3 = <?php echo json_encode($data['dateChart3']); ?>;
    var tempChart3 = <?php echo json_encode($data['tempChart3']); ?>;
    var dateChart10 = <?php echo json_encode($data['dateChart10']); ?>;
    var tempChart10 = <?php echo json_encode($data['tempChart10']); ?>;
    var dateChart = <?php echo json_encode($data['dateChartMonth']); ?>;
    var tempChart = <?php echo json_encode($data['tempChartMonth']); ?>;
    
    var data = {
        labels: dateChart3,
        series: [tempChart3]
    };
    
    var isMonth = false;
</script>
<script src="js/weather.js"></script>
