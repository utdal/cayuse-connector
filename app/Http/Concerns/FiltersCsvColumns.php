<?php

namespace App\Http\Concerns;

use App\Files\CsvReader;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FiltersCsvColumns
{
    public function filterCsvFile(UploadedFile $file): string
    {
        $reader = new CsvReader($file);

        $filtered = [];
        $filtered_headers = $this->default_columns ?? $reader->reader->getHeader();
        $filtered[] = $filtered_headers;

        foreach ($reader->rows as $row) {
            $filtered_row = [];
            foreach ($filtered_headers as $filtered_header) {
                $filtered_row[$filtered_header] = $row[$filtered_header] ?? '';
            }
            $filtered[] = $filtered_row;
        }

        $csv = Writer::createFromString('');
        $csv->insertAll($filtered);

        return $csv->toString();
    }
}