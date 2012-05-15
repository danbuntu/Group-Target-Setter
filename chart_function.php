<?php

function chart_data($values) {

// Port of JavaScript from http://code.google.com/apis/chart/
// http://james.cridland.net/code
// First, find the maximum value from the values given

        $maxValue = max($values);

// A list of encoding characters to help later, as per Google's example
        $simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $chartData = "s:";
        for ($i = 0; $i < count($values); $i++) {
            $currentValue = $values[$i];

            if ($currentValue > -1) {
                $chartData.=substr($simpleEncoding, 61 * ($currentValue / $maxValue), 1);
            } else {
                $chartData.='_';
            }
        }

// Return the chart data - and let the Y axis to show the maximum value
        return $chartData . "&chxt=y&chxl=0:|0|" . $maxValue;
    }

    ?>