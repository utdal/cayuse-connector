<?php

namespace App\Files;

use Iterator;
use League\Csv\HTMLConverter;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvReader
{
    public Reader $reader;
    public Iterator $rows;

    public function __construct(public UploadedFile|string $input)
    {
        if ($input instanceof UploadedFile) {
            $this->reader = Reader::createFromPath($input->getPathname());
        } elseif (is_string($input)) {
            $this->reader = Reader::createFromString($input);
        }
        $this->reader->setHeaderOffset(0);
        $this->rows = $this->reader->getRecords();
    }

    public function render($type = 'json')
    {
        if ($type === 'json') {
            header('Content-type: application/json');
            echo json_encode($this->results);
        }
    }

    public function count(): int
    {
        return count($this->reader);
    }

    public function toHtml(): string
    {
        return (new HTMLConverter())
            ->table('table table-striped')
            ->convert($this->rows, $this->reader->getHeader());
    }

    public function toArray(): array
    {
        $rows = [];
        foreach($this->rows as $row_num => $row) {
            $rows[$row_num] = $this->slugifyHeaders($row);
        }

        return $rows;
    }

    public function toString(): string
    {
        return $this->reader?->toString() ?? '';
    }

    public function slugifyHeaders(array $row): array
    {
        return array_combine(
            array_map(fn(string $key): string => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $key))), array_keys($row)),
            array_values($row)
        );
    }
}