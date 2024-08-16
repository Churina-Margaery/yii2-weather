<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace app\models;
use yii\db\ActiveRecord;

/**
 * Description of Records
 *
 * @author user
 */
class Records extends ActiveRecord
{
    public static function tableName(): string
    {
        return "records";
    }
    
            // Функция для выполнения GET-запросов
    private static function httpGetting($url, $headers)
        {
            $options = [
                "http" => [
                    "header" => implode("\r\n", $headers),
                    "method" => "GET",
                    "ignore_errors" => true,
                ],
            ];
            $context = stream_context_create($options);
            return file_get_contents($url, false, $context);
        }

    public static function getAndUpdateWeatherData()
    {
        // Получаем текущую дату и время
        $currentDateTime = new \DateTime();

        $daysOfWeek = [
            "Воскресенье",
            "Понедельник",
            "Вторник",
            "Среда",
            "Четверг",
            "Пятница",
            "Суббота",
        ];

        $months = [
            1 => "января",
            2 => "февраля",
            3 => "марта",
            4 => "апреля",
            5 => "мая",
            6 => "июня",
            7 => "июля",
            8 => "августа",
            9 => "сентября",
            10 => "октября",
            11 => "ноября",
            12 => "декабря",
        ];

        // Получаем день недели и месяц из объекта DateTime
        $dayOfWeek = $daysOfWeek[$currentDateTime->format("w")];
        $dayOfMonth = $currentDateTime->format("j");
        $month = $months[(int) $currentDateTime->format("n")];

        // Формируем итоговую строку
        $formattedDate = "$dayOfWeek, $dayOfMonth $month";

        // Запрос для получения последней записи
        $lastRecord = Records::find()
            ->orderBy(["date" => SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();

        // Если база данных пуста, можем сразу обновить данные
        if (empty($lastRecord)) {
            Records::fillWeatherData();

            $lastRecord = Records::find()
                ->orderBy(["date" => SORT_DESC])
                ->limit(1)
                ->asArray()
                ->one();
        }

        $lastRecordTime = new \DateTime($lastRecord["date"]);
        
        // Если данных за последний час нет,обновляем бд
        if ($lastRecordTime <= $currentDateTime->modify("-1 hour")) {
            Records::updateWeatherData();
            $lastRecord = Records::find()
                ->orderBy(["date" => SORT_DESC])
                ->limit(1)
                ->asArray()
                ->one();
        }
        
        // Получаем данные для графика  
   
        $currentDateTimeM = new \DateTime();
        $monthAgo = $currentDateTimeM
            ->modify("-31 days")
            ->format("Y-m-d H:i:s");
        $dataChartM = Records::find()
            ->select(["DATE(date) as date", "temperature"])
            ->where([">=", "date", $monthAgo])
            ->asArray()
            ->all();

        $dateChartM = [];
        $tempChartM = [];

        // Заполняем массивы
        foreach ($dataChartM as $record) {
            $dateChartM[] = $record["date"];
            $tempChartM[] = $record["temperature"];
        }
        
        $dateChart3 = array_slice($dateChartM, 654);
        $tempChart3 = array_slice($tempChartM, 654);
        
        $dateChart10 = array_slice($dateChartM, 504);
        $tempChart10 = array_slice($tempChartM, 504);
        
        // Получаем остальные данные
        $average = array_sum($tempChart3) / count($tempChart3);

        $pollutionAssessmentPm2 = "";
        $pollutionAssessmentPm10 = "";

        if ($lastRecord["pm2"] < 12) {
            $pollutionAssessmentPm2 = "(хорошее качество воздуха)";
        } elseif ($lastRecord["pm2"] < 35.4) {
            $pollutionAssessmentPm2 = "(нормальное качество воздуха)";
        } elseif ($lastRecord["pm2"] < 55.4) {
            $pollutionAssessmentPm2 = "(неприемлемое качество воздуха)";
        } elseif ($lastRecord["pm2"] < 150.4) {
            $pollutionAssessmentPm2 = "(плохое качество воздуха)";
        } else {
            $pollutionAssessmentPm2 = "(очень плохое качество воздуха)";
        }

        if ($lastRecord["pm10"] < 54) {
            $pollutionAssessmentPm10 = "(хорошее качество воздуха)";
        } elseif ($lastRecord["pm10"] < 154) {
            $pollutionAssessmentPm10 = "(нормальное качество воздуха)";
        } elseif ($lastRecord["pm10"] < 254) {
            $pollutionAssessmentPm10 = "(неприемлемое качество воздуха)";
        } else {
            $pollutionAssessmentPm10 = "(очень плохое качество воздуха)";
        }

        $lastRecord["pm2"] = round($lastRecord["pm2"], 2);
        $lastRecord["pm10"] = round($lastRecord["pm10"], 2);
        

        return [
            "lastRecordData" => $lastRecord,
            "pm2level" => $pollutionAssessmentPm2,
            "pm10level" => $pollutionAssessmentPm10,
            "dayOfTheWeek" => $formattedDate,
            "average" => $average,
            "dateChartMonth" => $dateChartM,
            "tempChartMonth" => $tempChartM,
            "dateChart3" => $dateChart3,
            "tempChart3" => $tempChart3,
            "dateChart10" => $dateChart10,
            "tempChart10" => $tempChart10,
        ];
    }

    private static function fillWeatherData()
    {
        // Обновление данных о погоде через API
        // Параметры API
        $postListUrl = "https://mycityair.ru/harvester/v2/Posts";
        $apiKey = "";  // Прописать ключ

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey,
        ];

        // Получаем список постов
        $response = httpGetting($postListUrl, $headers);
        if ($response === false) {
            die("Error occurred while fetching posts.");
        }

        $posts = json_decode($response, true);
        if (count($posts) == 0) {
            throw new Exception("At least one post is required");
        }

        $post = $posts[0];
        $interval = "1h";

        $currentDateTime = new \DateTime();
        $currentDateTime->setTimezone(new \DateTimeZone("UTC"));

        // Если база пуста, заполняем ее данными за последний месяц
        $tenDaysAgo = $currentDateTime
            ->modify("-31 days")
            ->format("Y-m-d\TH:00:00\Z");
        $postMeasurementsUrl = sprintf(
            "https://mycityair.ru/harvester/v2/Posts/%s/measurements?interval=%s&date__gt=%s",
            $post["id"],
            $interval,
            $tenDaysAgo
        );

        // Получаем измерения
        $response = httpGetting($postMeasurementsUrl, $headers);
        if ($response === false) {
            die("Error occurred while fetching measurements.");
        }

        $measurementsResponse = json_decode($response, true);
        if ($measurementsResponse === null) {
            throw new Exception("Failed to decode the response.");
        }

        // Сохраняем новые записи в БД
        foreach ($measurementsResponse["data"] as $measurement) {
            $record = new Records();
            $dateTmp = new \DateTime($measurement["date"]);
            $formattedDate = $dateTmp->format("Y-m-d H:i:s");
            $record->date = $formattedDate;
            //$record->time = $measurement['date'];
            $record->temperature = $measurement["temperature"];
            $record->humidity = $measurement["humidity"];
            $record->pressure = $measurement["pressure"];
            $record->pm2 = $measurement["pm2"];
            $record->pm10 = $measurement["pm10"];
            $record->save(); // Сохраняем запись
        }
    }

    private static function updateWeatherData()
    {
        // Обновление данных о погоде через API
        // Параметры API
        $postListUrl = "https://mycityair.ru/harvester/v2/Posts";
        $apiKey = "";  // Прописать ключ

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey,
        ];

        // Получаем список постов
        $response = Records::httpGetting($postListUrl, $headers);
        if ($response === false) {
            die("Error occurred while fetching posts.");
        }

        $posts = json_decode($response, true);
        if (count($posts) == 0) {
            throw new Exception("At least one post is required");
        }

        $post = $posts[0];
        $interval = "1h";

        $currentDateTime = new \DateTime();
        $currentDateTime->setTimezone(new \DateTimeZone("UTC"));

        // Если база не пуста, проверяем время последней записи
        $lastRecord = Records::find()
            ->orderBy(["date" => SORT_DESC])
            ->one();
        $lastRecordDateTime = new \DateTime($lastRecord->date);
        $timeDiff =
            $currentDateTime->getTimestamp() -
            $lastRecordDateTime->getTimestamp();

        // Если прошло больше часа, добавляем недостающие записи
        if ($timeDiff >= 3600) {
            // Получаем данные с последнего времени до текущего
            $lastMeasurementTime = $lastRecordDateTime->format(
                "Y-m-d\TH:i:s\Z"
            );
            $postMeasurementsUrl = sprintf(
                "https://mycityair.ru/harvester/v2/Posts/%s/measurements?interval=%s&date__gt=%s",
                $post["id"],
                $interval,
                $lastMeasurementTime
            );

            // Получаем новые измерения
            $response = Records::httpGetting($postMeasurementsUrl, $headers);
            if ($response === false) {
                die("Error occurred while fetching new measurements.");
            }

            $measurementsResponse = json_decode($response, true);
            if ($measurementsResponse === null) {
                throw new Exception("Failed to decode the response.");
            }

            // Сохраняем новые записи в БД
            foreach ($measurementsResponse["data"] as $measurement) {
                $record = new Records();
                $dateTmp = new \DateTime($measurement["date"]);
                $formattedDate = $dateTmp->format("Y-m-d H:i:s");
                $record->date = $formattedDate;
                $record->temperature = $measurement["temperature"];
                $record->humidity = $measurement["humidity"];
                $record->pressure = $measurement["pressure"];
                $record->pm2 = $measurement["pm2"];
                $record->pm10 = $measurement["pm10"];
                $record->save();
            }
        }

        // Удаляем записи старше месяца
        $tenDaysAgo = $currentDateTime
            ->modify("-31 days")
            ->format("Y-m-d H:i:s");
        Records::deleteAll(["<", "date", $tenDaysAgo]);

        // Находим все записи с дубликатами
        $duplicates = Records::find()
            ->select(["date", "COUNT(*) AS count"])
            ->groupBy("date")
            ->having("count > 1")
            ->all();

        foreach ($duplicates as $duplicate) {
            $records = Records::find()
                ->where(["date" => $duplicate->date])
                ->orderBy("id")
                ->all();

            // Удаляем все, кроме первой записи
            for ($i = 1; $i < count($records); $i++) {
                $records[$i]->delete();
            }
        }
    }

    public static function getDataForPeriod($startDate, $endDate, $data)
    {
        $currentTime = new \DateTime("now", new \DateTimeZone("UTC"));
        if ($endDate > $currentTime) {
            $endDate = $currentTime->format("Y-m-d\TH:i:s\Z");
        }
        $result = [];
        if (isset($data["data"])) {
            foreach ($data["data"] as $entry) {
                if (
                    isset($entry["date"], $entry["temperature"]) &&
                    $startDate < $data["data"] &&
                    $endDate > $data["data"]
                ) {
                    $result[] = [
                        "date" => $entry["date"],
                        "temperature" => $entry["temperature"],
                    ];
                }
            }
        }
        return $result;
    }
}
