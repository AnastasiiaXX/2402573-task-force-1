<?php
declare(strict_types=1);

namespace TaskForce\utils;

use SplFileObject;
use Exception;

class CsvToSqlConverter {
    public function convert(string $sourcePath, string $outPath, array $columns): void
    {
        $fileObject = new SplFileObject($sourcePath);
        $tableName = pathinfo($sourcePath, PATHINFO_FILENAME);
        $sql = '';
        $columnsString = (implode(', ', $columns));

        while(!$fileObject->eof()) {
            $csvString = $fileObject->fgetcsv();

           if($fileObject->key() == 0 || $csvString == null || $csvString === [null]) {
                continue;
            }

            $values = array_map(fn($value) => "'" . $value . "'", $csvString);
            $valuesString = implode(', ', $values);
            $sql .= "INSERT INTO {$tableName} ({$columnsString}) VALUES ({$valuesString});" . PHP_EOL;
        }

        if ($sql === '') {
            throw new Exception('CSV does not contain data');
        }

        $outName = $tableName . '.sql';

        if (file_put_contents($outPath . $outName, $sql) === false) {
            throw new Exception('Cannot write SQL file');
        }
    }
}
