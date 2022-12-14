<?php

namespace App\Http\Concerns;

trait FixesJson
{
    public function fixJson(string $json): string
    {
        $fixes = [
            'empty objectVersion' => [
                'pattern' => '/\"objectVersion\": ,/',
                'replacement' => '"objectVersion": "",',
            ],
        ];

        foreach ($fixes as $fix) {
            $json = preg_replace($fix['pattern'], $fix['replacement'], $json);
        }

        return $json;
    }
}