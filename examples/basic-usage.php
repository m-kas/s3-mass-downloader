<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// configuration and credentials
// take the variables from the .env file, or some other config, depending on your setup or app
const AWS_DEFAULT_REGION = '__REGION__';
const AWS_BUCKET = '__BUCKET_NAME__';
const AWS_ACCESS_KEY_ID = '__ACCESS_KEY_ID__';
const AWS_SECRET_ACCESS_KEY = '__SECRET_ACCESS_KEY__';

// create new S3 client instance
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

// just for an example, take the first 10 files from the bucket
try {
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

if (empty($files)) {
    echo 'No files found' . PHP_EOL;
    exit(1);
}

try {
    // create new StreamingClient instance
    // first argument is the S3 client instance
    // second argument is the bucket name
    // third argument is optional, and is the name of the resulting zip file
    // if third argument is not provided, the default filename of resulting zip is `project_{datetime}.zip`
    $streamingClient = new \S3MassDownloader\S3MassDownloader\StreamingClient($s3Client, AWS_BUCKET, 'result.zip');

    // download the files
    // as a parameter please provide an array with the list of absolute files path that you want to be downloaded
    $streamingClient->downloadZippedFiles($files);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
