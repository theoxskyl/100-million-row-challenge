<?php

namespace App;

use Exception;

final class Parser
{
    public function parse(string $inputPath, string $outputPath): void
    {
        // theoxskyl
        $fp = fopen($inputPath, 'r');

        $host    = 'https://stitcher.io';
        $hostLen = mb_strlen($host);
        $dateLen = 10;

        $output = [];
        while (($line = fgets($fp)) !== false) {
            $line = rtrim($line, "\r\n");

            $pos = strpos($line, ',');
            if ($pos === false) {
                continue;
            }

            $url = substr($line, 0, $pos);
            $rest   = substr($line, $pos + 1);

            if ($rest === '') {
                continue;
            }

            $date = substr($rest, 0, $dateLen);
            $url = substr($url, $hostLen);

            if (!isset($output[$url])) {
                $output[$url] = [];
            }

            if (isset($output[$url][$date])) {
                $output[$url][$date]++;
            } else {
                $output[$url][$date] = 1;
            }
        }

        fclose($fp);

        foreach ($output as $idx => $o) {
            ksort($output[$idx]);
        }

        file_put_contents($outputPath, json_encode($output, JSON_PRETTY_PRINT));
    }
}
