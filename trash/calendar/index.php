<?php

use Kigkonsult\Icalcreator\Vcalendar;

class Airbnb
{
    public $events = array();
    public $jsonFile = "";
    public $lastFile = "";
    public $checked = array();
    public $objects = array();

    function __construct()
    {
        $this->objects = array();
        $this->objects[] = array("name"=>"Företag 4B","color"=>"#CC4444","check"=>"4b", "cal"=>"https://www.airbnb.se/calendar/ical/1388275551442625740.ics?s=1352f60e021de56c442150fe86d8cea0");
         $this->objects[] = array("name"=>"Villa med pool","color"=>"#6666CC","check"=>"13a", "cal"=>"https://www.airbnb.se/calendar/ical/1388317673647947381.ics?s=d2136811b68dd78e7177acea0b4770c8");
        $this->objects[] = array("name"=>"OLD_Villa med pool","color"=>"#00CC00","check"=>"13a", "cal"=>"https://www.airbnb.se/calendar/ical/902771067139845929.ics?s=4aee301357abb81c6d146743ca87b20c");
        $this->jsonFile = __DIR__ . '/bookings.json';
        $this->lastFile = __DIR__ . '/last.json';
        $this->setPre();
        if (isset($_POST["addnew"])) {
            $this->setJson();
        } else if (isset($_POST["addraw"])) {
            $this->setJson(true);
        }

        foreach($this->objects as $object){
            if ($this->checked[$object["check"]] !== "") {
                $this->parseICal($object["name"], $object["cal"]);
            }

        }
    }

    private function setPre()
    {
        $this->checked["showall"] = isset($_POST["showall"]) ? "checked='checked'" : "";
        $this->checked["4b"] = isset($_POST["4b"]) || empty($_POST["show"]) ? "checked='checked'" : "";
        $this->checked["13a"] = isset($_POST["13a"]) || empty($_POST["show"]) ? "checked='checked'" : "";
    }

    private function setJson($refresh = false)
    {
        $uid = $_POST['uid'];

        $data = file_exists($this->jsonFile) ? json_decode(file_get_contents($this->jsonFile), true) : [];


        $booking = $refresh ? $data[$uid]["raw"] : $_POST['booking'];
        file_put_contents($this->lastFile, json_encode($booking, JSON_PRETTY_PRINT));

        $data[$uid] = [];
        $data[$uid]["updated_at"] = date('Y-m-d H:i:s');
        $data[$uid]["raw"] = $booking;
        if (preg_match('/Bekräftad\r\n([^\r\n]+)/', $booking, $matches)) {
            $data[$uid]['name'] = trim($matches[1]);
        }
        if (preg_match('/(\d+\s*gäster)/u', $booking, $matches)) {
            $data[$uid]['guests'] = trim($matches[1]);
        }

       if (preg_match('/Incheckning\s+.*?(\d+\s+[a-zA-ZåäöÅÄÖ]+\.?\s+\d{4}).*?Utcheckning\s+.*?(\d+\s+[a-zA-ZåäöÅÄÖ]+\.?\s+\d{4})/su', $booking, $matches)) {
            $checkin = DateTime::createFromFormat('j M. Y', trim($matches[1]), new DateTimeZone('Europe/Stockholm'));
            $checkout = DateTime::createFromFormat('j M. Y', trim($matches[2]), new DateTimeZone('Europe/Stockholm'));
            $data[$uid]['dates'] = $checkin->format('j') . '–' . $checkout->format('j M.');
            $data[$uid]['duration'] = $checkin->diff($checkout)->days . ' nätter';
        }
        if (preg_match('/([\d\s,.]+)\s*kr/u', $booking, $matches)) {
            $data[$uid]['price'] = (float)str_replace([',', ' '], ['.', ''], trim($matches[1]));
        }
        if (preg_match('/(\d,\d)\s*i\s*betyg/', $booking, $matches)) {
            $data[$uid]['rating'] = trim($matches[1]);
        } else {
            $data[$uid]['rating'] = "?";
        }

        $data[$uid]['host'] = $data[$uid]['membertime'] = $data[$uid]['living'] = $data[$uid]['language'] = '';

        if (preg_match('/Ocks[åä]\s*en\s*värd/u', $booking, $matches)) {
            $data[$uid]['host'] = trim($matches[0]);
        }
        if (preg_match('/Blev\s*medlem\s*på\s*Airbnb\s*(\d{4})/u', $booking, $matches)) {
            $data[$uid]['membertime'] = trim($matches[1]);
        }
        if (preg_match('/Bor\s*i\s*([^,]+,\s*[^\r\n]+)/u', $booking, $matches)) {
            $data[$uid]['living'] = trim($matches[1]);
        }
        if (preg_match('/Talar\s*([^\r\n]+)/u', $booking, $matches)) {
            $data[$uid]['language'] = trim($matches[1]);
        }


        file_put_contents($this->jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function parseICal($object, $url)
    {

        $icalString = file_get_contents($url);
        if ($icalString === false) {
            throw new Exception("Kunde inte hämta iCal-data från URL");
        }
        $icalString = str_replace(["\r\n", "\r"], "\n", $icalString);
        $icalString = preg_replace("/\n\s+/", "\n", $icalString);
        $icalString = preg_replace('/^[\x{FEFF}\x{200B}]+/u', '', $icalString);
        $icalString = preg_replace('/(DESCRIPTION:[^\n]*?)\\n([^\n]*)/', '$1$2', $icalString);

        $json = json_decode(file_get_contents($this->jsonFile), 1);
        $vcalendar = Sabre\VObject\Reader::read($icalString);
        foreach ($vcalendar->VEVENT as $vevent) {
            if ($vevent->SUMMARY == "Reserved" || $this->checked["showall"] !== "") {
                $startDate = $vevent->DTSTART ? $vevent->DTSTART->getDateTime() : null;
                $endDate = $vevent->DTEND ? $vevent->DTEND->getDateTime() : null;
                $description = isset($vevent->DESCRIPTION) ? (string)$vevent->DESCRIPTION : '';
                $reservationUrl = '';
                if (preg_match('/Reservation URL: (https:\/\/www\.airbnb\.com\/hosting\/reservations\/de\s*tails\/[^\s]+)/', $description, $matches)) {
                    $reservationUrl = str_replace("\n", "", trim($matches[1])); // Ta bort eventuella radbrytningar och trimma
                }
                $uid = isset($vevent->UID) ? (string)$vevent->UID : '';

                $jsonFields = [];
                foreach ($json[$uid] ?? [] as $key => $value) {
                    $jsonFields[$key] = $value ?? '?';
                }

                $this->events[] = ['object' => $object, 'start' => $startDate ? $startDate->format('Y-m-d') : '', 'end' => $endDate ? $endDate->format('Y-m-d') : '', 'summary' => isset($vevent->SUMMARY) ? (string)$vevent->SUMMARY : '', 'description' => $description, 'url' => "<a href='{$reservationUrl}' target='_blank'>Details AirBnB</a>", 'uid' => isset($vevent->UID) ? (string)$vevent->UID : '', 'name' => isset($json[$uid]['name']) ? $json[$uid]['name'] : '?', 'guests' => isset($json[$uid]['guests']) ? $json[$uid]['guests'] : '?', 'dates' => isset($json[$uid]['dates']) ? $json[$uid]['dates'] : '?', 'duration' => isset($json[$uid]['duration']) ? $json[$uid]['duration'] : '?', 'price' => isset($json[$uid]['price']) ? $json[$uid]['price'] : '?', 'rating' => isset($json[$uid]['rating']) ? $json[$uid]['rating'] : '?', 'host' => isset($json[$uid]['host']) ? $json[$uid]['host'] : '', 'membertime' => isset($json[$uid]['membertime']) ? $json[$uid]['membertime'] : '?', 'living' => isset($json[$uid]['living']) ? $json[$uid]['living'] : '', 'language' => isset($json[$uid]['language']) ? $json[$uid]['language'] : '', 'raw' => isset($json[$uid]['raw']) ? true : false,];
            }
        }

    }

    public function matchDateRange(string $textRange, string $startDate, string $endDate): bool
    {
        if (!preg_match('/(\d+)\s*([a-zA-ZåäöÅÄÖ]*)\.?\s*(?:–|−)\s*(\d+)\s*([a-zA-ZåäöÅÄÖ]*)\.?/u', $textRange, $matches)) {
            return false;
        }

        $startDay = $matches[1];
        $startMonth = $matches[2] ?: date('M', strtotime($startDate));
        $endDay = $matches[3];
        $endMonth = $matches[4] ?: $startMonth;

        $year = date('Y', strtotime($startDate));
        $textStart = strtotime("$startDay $startMonth $year");
        $textEnd = strtotime("$endDay $endMonth $year");

        if ($textEnd < $textStart) {
            $textEnd = strtotime("$endDay $endMonth " . ($year + 1));
        }

        return date('Y-m-d', $textStart) === $startDate && date('Y-m-d', $textEnd) === $endDate;
    }
}

$airbnb = new Airbnb();
$rowHeight = 20;
$cellWidth = 40;
$padding = 5;

$start_date = new DateTime('first day of this month');
$end_date = new DateTime('last day of this month +2 months');

$timeline = '<div style="padding: 10px;">';

for ($month = 0; $month < 4; $month++) {
    $month_start = (clone $start_date)->modify("+$month months");
    $month_end = (clone $month_start)->modify('last day of this month');
    $daterange = new DatePeriod($month_start, new DateInterval('P1D'), $month_end->modify('+1 day'));

    $timeline .= '<div style="overflow-x: auto; white-space: nowrap; margin-bottom: 20px;">';
    $timeline .= '<b>' . $month_start->format('F Y') . '</b>';
    $timeline .= '<div style="position: relative; height: '.(1+ ($rowHeight+20)* (sizeof($airbnb->objects) + 1)).'px;">';

    $timeline .= '<div style="position: absolute; top: 0; height: '.$rowHeight.'px;">';
    foreach ($daterange as $date) {
        $style = 'display: inline-block; width: '.$cellWidth.'px; height: '.$rowHeight.'px; border: 1px solid #FFF; text-align: center; line-height: '.$rowHeight.'px;';
        $style .=  in_array($date->format("N"), array(6,7)) ? "background:#CC6666;":"background:#CCCCCC";
        $timeline .= '<div style="' . $style . '" title="'.$date->format("l Y-m-d ").'">' . $date->format('d') . '</div>';
    }
    $timeline .= '</div>';

    $top = $rowHeight;
    foreach ($airbnb->objects as $object) {
        $timeline .= '<div style="position: absolute; top: '.$top.'px; height: '.$rowHeight.'px;">';
        foreach ($airbnb->events as $event) {
            if ($event['object'] === $object["name"]) {
                $event_start = new DateTime($event['start']);
                $event_end = new DateTime($event['end']);

                $month_start_ts = strtotime($month_start->format('Y-m-d'));
                $month_end_ts = strtotime($month_end->format('Y-m-d'));

                $event_start_ts = strtotime($event['start']);
                $event_end_ts = strtotime($event['end']);

                if ($event_end_ts >= $month_start_ts && $event_start_ts < $month_end_ts) {
                    $display_start = max($event_start_ts, $month_start_ts);
                    $display_end = min($event_end_ts, $month_end_ts);

//                    if ($event_start->format('H:i') !== '00:00' && $event_start_ts == $display_start) {
//                        $start_pos += 0.5;
//                        $days_diff -= 0.5;
//                    }
//                    if ($event_end->format('H:i') !== '00:00' && $event_end_ts == $display_end) {
//                        $days_diff -= 0.5;
//                    }
                    $left = ($display_start - $month_start_ts) / (86400) * $cellWidth;
                    $width = ($display_end - $display_start) / (86400) * $cellWidth;
                    // Start mid
                    $mid = 0;
                    $border = "";
                    if($event_end_ts >= $month_end_ts){
                        $mid = 1;
                        $border .= "border-left:2px solid #000;";
                        $left += 2 * $cellWidth / 3;
                        $width -= 2* $cellWidth / 3;
                    }
                    else if($event_start_ts <= $month_start_ts){
                        $mid = 3;
                        $border .= "border-right:2px solid #000;";
                        $width += $cellWidth / 3;
                    }
                    else{
                        $mid = 2;
                        $border .= "border-left:2px solid #000;";
                        $border .= "border-right:2px solid #000;";
                        $left += 2 * $cellWidth / 3;
                        $width -=  $cellWidth / 3;
                    }

                   // echo date("Y-m-d H:i:s",$event_end_ts)." <br> ".date("Y-m-d H:i:s",$month_end_ts)."<hr>";


                    $style = 'padding:'.$padding.'px;'.$border.';position: absolute; left: ' . $left . 'px; width: ' . $width . 'px; background-color: '.$object["color"].'; color: white; text-align: center; line-height: '.$rowHeight.'px;';
                    $timeline .= '<div style="' . $style . '" title="'. $event['name'] . '&#10;' . $event['start'] . ' - ' . $event['end'] . '">' . $event['name'] . '</div>';
                }
            }
        }
        $timeline .= '</div>';
        $top += $rowHeight+($padding * 2);
    }

    $timeline .= '</div>';
    $timeline .= '</div>';
}

$timeline .= '</div>';

echo $timeline;


echo <<<HTML
<form action="/calendar/" method="post" style="background:#c8e5bc;border:1px solid #000;padding:5px">
<input type="checkbox" value="1" {$airbnb->checked["showall"]} name="showall"> Show all
<input type="checkbox" value="1" {$airbnb->checked["4b"]} name="4b"> 4 B
<input type="checkbox" value="1" {$airbnb->checked["13a"]} name="13a"> 13 A
<input type="submit" name="show" value="Show">
<span id="total" style="font-weight:bold;margin-left:100px"></span>
</form>

<table class="table table-striped">
    <tr>
        
        <th>Period</th>
        <th>Objekt</th>
        <th>Price</th>
        <th>Bokning</th>
        <th>Info</th>
        <th>URL</th>
    </tr>
HTML;
usort($airbnb->events, function ($a, $b) {
    return strtotime($a['start']) <=> strtotime($b['start']);
});
$tot = 0;
$totalDays = 0;
foreach ($airbnb->events as $event) {
    $cleaned =

    $tot += (float)preg_replace('/[^0-9.]/', '', $event['price']);
    $duration = filter_var($event['duration'], FILTER_SANITIZE_NUMBER_INT);
    $totalDays += is_numeric($duration) ? $duration : 0;
    $perday = $duration > 0 ? number_format($event['price'] / $duration, 2, ".") : "?";
    $info = "Betyg: {$event['rating']}";
    $info .= "<br>Medlem sedan: {$event['membertime']}";
    if (!empty($event['host'])) {
        $info .= "<br>Värd:{$event['host']}";
    }
    if (!empty($event['living'])) {
        $info .= "<br>Bor: {$event['living']}";
    }
    if (!empty($event['language'])) {
        $info .= "<br>Språk: {$event['language']}";
    }
    $raw = empty($event["raw"]) ? "" : "<input type=\"submit\" name=\"addraw\" value=\"Refresh\" style=\"vertical-align:top\">";

    $match = $airbnb->matchDateRange($event['dates'], $event['start'], $event['end']);
    $color = $match ? "background:#EEFFEE" : "background:red;";
    echo <<<HTML
        <tr>
            <td style="{$color}">{$event['start']} to {$event['end']} <br>{$event['dates']}</td>   
            <td>{$event['object']}<br>{$event['summary']}</td>   
            <td>{$event['price']} kr <br>({$perday} kr)</td>
            <td>{$event['name']}
                <br>{$event['guests']}
                <br>{$event['duration']}
            </td>
            <td>
            {$info}
            </td>
            <td>{$event['url']}
            <form action="/calendar/" method="post">
                <input type="hidden" name="uid" value="{$event["uid"]}">
                <textarea name="booking" style="height:30px"></textarea>
                <br><input type="submit" name="addnew" value="+" style="vertical-align:top">
                {$raw}
                
            </form>
        </td>           
        </tr>   
        HTML;
}

echo "</table>";
$avg = number_format($tot / $totalDays, 2, ".", " ");
$totals = "Totalt " . number_format($tot, "2", ".", " ") . " kr";
$totals .= ", {$totalDays} dagar ($avg kr/dag)";
?>
<script>
    document.getElementById("total").innerHTML = '<?=$totals?>';
</script>

