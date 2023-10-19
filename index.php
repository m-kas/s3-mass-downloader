<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

// configuration and credentials
// take them from the .env file, or some other config, depending on your setup or app
const AWS_DEFAULT_REGION = '__REGION__';
const AWS_BUCKET = '__BUCKET_NAME__';
const AWS_ACCESS_KEY_ID = '__ACCESS_KEY_ID__';
const AWS_SECRET_ACCESS_KEY = '__SECRET_ACCESS_KEY__';

$s3Client = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => AWS_DEFAULT_REGION,
    'credentials' => [
        'key' => AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY,
    ],
    'use_path_style_endpoint' => true,
]);

$files = [];

// just an example: take first 10 files from the bucket
try {
    // List objects in a bucket
    $result = $s3Client->listObjectsV2([
        'Bucket' => AWS_BUCKET,
    ]);

    $count = 0;

    foreach ($result['Contents'] as $object) {
        $files[] = $object['Key'];

        if (++$count === 10) {
            break;
        }
    }
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $streamingClient = new \Ww\S3MassDownloader\StreamingClient($s3Client, AWS_BUCKET);
    $streamingClient->getZippedFiles($files);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
