<?php

require __DIR__ . '/vendor/autoload.php';

use TaskForce\utils\CsvToSqlConverter;

$converter = new CsvToSqlConverter();

$converter->convert(
    __DIR__ . '/data/locations.csv',
    __DIR__ . '/sql/',
    ['name', 'latitude', 'longitude']
);

$converter->convert(
    __DIR__ . '/data/categories.csv',
    __DIR__ . '/sql/',
    ['title', 'symbol_code']
);

echo "Done\n";
