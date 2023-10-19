<?php

require_once __DIR__ . '/vendor/autoload.php';

$streamingClient = new \Ww\S3MassDownloader\StreamingClient();

$files = [
    'https://speed.hetzner.de/1GB.bin',
];

$streamingClient->getZippedFiles($files);
