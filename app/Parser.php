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

            $tPos = strpos($rest, 'T');
            if ($tPos === false) {
                continue;
            }

            $date = substr($rest, 0, $tPos);

            if (strncmp($url, $host, $hostLen) === 0) {
                $url = substr($url, $hostLen);
            } else {
                $url = $url;
            }

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
