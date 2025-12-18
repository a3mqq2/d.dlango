<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class UsbLabelPrinterService
{
    public function printLabel(string $code, float $price, string $currency = 'LYD', string $name = ''): void
    {
        $connector = new WindowsPrintConnector("EML-200L");
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        if ($name !== '') {
            $printer->setTextSize(1, 1);
            $printer->text($this->limit($name, 22) . "\n");
        }

        $printer->feed(1);

        $printer->barcode($code, Printer::BARCODE_CODE128);

        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $printer->text($code . "\n");

        $printer->setEmphasis(true);
        $printer->text(number_format($price, 2) . " " . $currency . "\n");
        $printer->setEmphasis(false);

        $printer->feed(2);
        $printer->cut();
        $printer->close();
    }

    private function limit(string $text, int $max): string
    {
        return mb_strimwidth($text, 0, $max, '');
    }
}