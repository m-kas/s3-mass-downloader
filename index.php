<?php

require_once __DIR__ . '/vendor/autoload.php';

$streamingClient = new \Ww\S3MassDownloader\StreamingClient();

$files = [
    '/Users/marcin/Downloads/assets/img/brand/brand1.png',
    '/Users/marcin/Downloads/assets/img/brand/brand2.png',
    '/Users/marcin/Downloads/assets/img/brand/brand3.png',
    '/Users/marcin/Downloads/assets/img/brand/brand4.png',
    '/Users/marcin/Desktop/random.rand',
];

$streamingClient->getZippedFiles($files);
