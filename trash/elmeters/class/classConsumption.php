<?php

use Altahr\Database;


class Consumption
{
    private $db;
    public $usedMeters = [];
    public $allMeters = [];
    public $clientRooms = [];
    public $AllclientRooms = [];
    public $dead = [];

    function __construct()
    {
        $this->db = new Database();
        $this->dead=[1,75];
        $this->allMeters = $this->db->selectArray("el_meters", "meterid", "1=1");
        $clients = $this->db->selectArray("sf_hyreslista", "clientid", "1=1");
        foreach ($clients as $clientid => $client) {
            foreach (explode(",", $client["rooms"]) as $room) {
                $this->clientRooms[$room][] = $client["name"];
                $this->AllclientRooms[$room][] = $client;
            }
        }

    }

    function setMeasured($meterid)
    {
        $measures = $this->db->selectArray("el_measured", "id", "meterid={$meterid} ORDER BY datetime");
        return $measures;
    }

    function lastInvoiced($clientid)
    {
        $invoice = $this->db->selectOne("el_invoiced", "clientid={$clientid} ORDER BY date DESC");
        if ($invoice) {
            return $invoice["date"];
        }
        return "";
    }
    function lastFortnox($fortnoxid)
    {
        $invoice = $this->db->selectOne("elFortnox", "CustomerNumber={$fortnoxid} ORDER BY InvoiceDate DESC");
        if ($invoice) {
            return $invoice["InvoiceDate"];
        }
        return "";
    }
    function setMeters($parent)
    {
        if ($parent == 0) {
            $meters = $this->db->selectArray("el_meters", "meterid", "meterid <> 100 AND (parent=0 || parent not IN(SELECT meterid FROM el_meters)) ORDER BY name_internal");
            return $meters;
        }
        $meters = $this->db->selectArray("el_meters", "meterid", "parent={$parent} ORDER BY name_internal");
        return $meters;
    }

    function ConsumptionMultiple($meters)
    {
        $total = 0;
        foreach ($meters as $meter) {
            //$children = $this->setMeters($meter["meterid"]);
            $total += $this->Consumption($meter["meterid"])[0];
        }
        return $total;
    }

    function ConsumptionKraftringen()
    {
        $kraftringen = $this->db->selectArray("el_kraftringen", "date", "1=1 ORDER BY date");
        $to = end($kraftringen);
        $sum = 0;
        $from = false;
        foreach ($kraftringen as $row) {
            if (strtotime($row["date"]) > strtotime($to["date"]) - 365 * 86400) {
                if (!$from) {
                    $from = $row;
                }
                $sum += $row["value"];
            }
        }
        $to = $to["date"];
        $from = $from["date"];
        return [$sum, $from, $to];
    }

    function Consumption($meterid)
    {
        if ($meterid == 0) {
            return $this->ConsumptionKraftringen();
        } else if ($meterid == 100) {
            return [10858, "2024-01-01", "2024-12-31"];
        } else {
            $measures = $this->setMeasured($meterid);
        }
        if (count($measures) > 1) {
            $to = end($measures);
            $targetDate = strtotime($to["datetime"]) - 365 * 24 * 3600;
            $from = null;
            $closestMeasure = null;
            $closestDiff = PHP_INT_MAX;
            foreach (array_reverse($measures) as $measure) {
                $measureDate = strtotime($measure["datetime"]);
                $diff = abs($measureDate - $targetDate);
                if ($measureDate <= $targetDate) {
                    $from = $measure;
                    break;
                }
                if ($diff < $closestDiff) {
                    $closestMeasure = $measure;
                    $closestDiff = $diff;
                }
            }
            if (!$from) {
                $from = $closestMeasure;
            }
            $f = date("d/m-y", strtotime($from["datetime"])) . " " . round($from["value"]);
            $t = date("d/m-y", strtotime($to["datetime"])) . " " . round($to["value"]);
            // Beräkna årsförbrukning
            $consumption = $from ? round(365 * 24 * 3600 * ($to["value"] - $from["value"]) / (strtotime($to["datetime"]) - strtotime($from["datetime"]))) : 0;
            if (in_array( $meterid,$this->dead)) {
                $consumption = 0;
            }
        else
            return [$consumption, $f, $t];
        }
        return [0, "", ""];
    }

    function renderTree($meters, $level = 1)
    {
        $columns = 10;
        $html = "";
        if (count($meters) > 0) {
            foreach ($meters as $meterid => $meter) {
                $this->usedMeters[$meterid][] = $meterid;
                $children = $this->setMeters($meter["meterid"]);
                $childResult = $this->renderTree($children, $level + 1);
                list($consumption, $from, $to) = $this->Consumption($meterid);
                $consumption_children = $this->ConsumptionMultiple($children);
                $style = $level > 2 ? "display:none" : "";
                $html .= "<tr class=\"children children-{$meter["parent"]}\" style=\"{$style}\">";
                $dead = in_array($meterid,$this->dead) ? "font-style:italic;color:#F00 !important;" : "";
                for ($i = 1; $i < $level; $i++) {
                    $html .= "<td style='background-color:#FFF'>&nbsp;</td>";
                }
                $html .= "<td class='level{$level}' style='$dead'>";
                $html .= "<b>" . number_format($consumption, 0, "", " ") . " kWh</b>";
                $html .= "<br>{$from}<br>{$to}";
                $html .= "</td>";
                if (count($children) > 0) {
                    $html .= "<td class='level{$level}'>";
                    $html .= "<Button class='btn' onclick='toggleChildren(\"children-{$meter["meterid"]}\")'>";
                    $html .= number_format($consumption_children, 0, "", " ") . " kWh <br>";
                    $html .= "</Button>";
                    $html .= "</td>";
                } else {
                    $html .= "<td class='level{$level}'><button class='btn btn-secondary' style='background-color: lightgray !important;'>No child</button></td>";
                }
                $html .= "<td class='level{$level}' style='background:#FFF'>";
                $html .= "{$meter["name_internal"]} [{$meter["meterid"]}]<br>";
                foreach (explode(",", $meter["rooms"]) as $room) {
                    $html .= "{$room} - ";
                    if (isset($this->clientRooms[$room])) {
                        $html .= implode(",", $this->clientRooms[$room]);
                    }
                    $html .= "<br>";
                }
                $html .= "<i>{$meter["comment"]}</i>";
                $html .= "</td>";
                for ($i = $level + 3; $i <= $columns; $i++) {
                    $html .= "<td style='background-color:#FFF'>&nbsp;</td>";
                }
                $html .= "</tr>";
                $html .= $childResult;
            }
            return $html;
        }
        return "";
    }

    function colorDate($date, $maxMonths = 48)
    {
        $time = strtotime($date);
        $now = time();
        $months = floor(($now - $time) / (30 * 24 * 60 * 60));
        $months = min($months, $maxMonths);
        $redValue = floor(($months / $maxMonths) * 255);
        $fontWeight = floor(400 + ($months / $maxMonths) * 500);
        $style = "color: rgb($redValue, 0, 0); font-weight: {$fontWeight};";
        return "<span style='{$style}'>{$date}</span>";
    }

    function num($num, $kWh = false)
    {
        if(!is_numeric($num)){
            return $num;
        }
        $return = number_format($num,0,""," ");
        $return.= $kWh ? " kWh" : "";
        return $return;
    }

    function AllMeters($meters, $level = 1)
    {
        $columns = 10;
        $html = "";
        if (count($meters) > 0) {
            foreach ($meters as $meterid => $meter) {
                list($consumption, $from, $to) = $this->Consumption($meterid);
                $this->usedMeters[$meterid][] = $meterid;
                $children = $this->setMeters($meter["meterid"]);
                $consumption_children = "";
                if (count($children) > 0) {
                    $consumption_children = "<i style='color:#666'>({$this->num($this->ConsumptionMultiple($children),1)})</i>";
                }
                $childResult = $this->AllMeters($children, $level + 1);
                $style = $level == 2 ? "background-color:#CCC !important;" :"";
                $style .= in_array($meterid,$this->dead) ? "font-style:italic;color:#F00 !important;" : "";
                $html .= "<tr>";
                $paddingLeft = 0;
                $fontweight = 900;
                for ($i = 1; $i < $level; $i++) {
                    $paddingLeft += 40;
                    $fontweight = max(200, $fontweight - 300);
                }
                $html .= "<td style='padding-left:{$paddingLeft}px;font-weight: {$fontweight};{$style}'>";
                $html .= $meter["name_internal"];
                $html .= "</td>";
                $html .= "<td style='{$style}' title='{$from} kWh till {$to} kWH'>{$this->num($consumption, true)} {$this->num($consumption_children, true)}</td>";
                $measures = $this->setMeasured($meterid);
                $lastDate = "";
                if (count($measures) > 0) {
                    $last = end($measures);
                    $lastDate = date("Y-m-d", strtotime($last["datetime"]));
                }
                $html .= "<td style='{$style}'>{$meter["rooms"]}</td>";
                $clients = [];
                $meterInvoice[$meterid] = [];
                $fortnoxInvoice[$meterid] = [];

                foreach (explode(",", $meter["rooms"]) as $room) {
                    if (!empty($room) && isset($this->AllclientRooms[$room])) {
                        foreach ($this->AllclientRooms[$room] as $na => $client) {
                            $clients[$client["name"]] = $client["name"];
                            $fortnoxInvoice[$meterid][$client["fortnox"]] = $this->colorDate($this->lastFortnox($client["fortnox"]));
                            $meterInvoice[$meterid][$client["clientid"]] = $this->colorDate($this->lastInvoiced($client["clientid"]));
                        }
                    }
                }
                $html .= "<td style='{$style}'>" . implode("<br>", $clients) . "</td>";
                $html .= "<td style='{$style}'>" . $this->colorDate($lastDate) . "</td>";
                $html .= "<td style='{$style}'>" . implode("<br>", $meterInvoice[$meterid]) . "</td>";
                $html .= "<td style='{$style}'>" . implode("<br>", $fortnoxInvoice[$meterid]) . "</td>";
                $html .= "</tr>";
                $html .= $childResult;
            }
            return $html;
        }
        return "";
    }
}

?>