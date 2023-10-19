<?php declare(strict_types=1);

namespace Ww\S3MassDownloader;

use Aws\S3\S3Client;
use ZipStream\ZipStream;

class StreamingClient
{
    private const DEFAULT_FILENAME_PREFIX = 'project_';

    private S3Client $s3Client;
    private string $bucketName;
    private ZipStream $zipStream;
    private string $zipFileName;

    public function __construct(S3Client $s3Client, string $bucketName, string $filename = '')
    {
        $this->s3Client = $s3Client;
        $this->s3Client->registerStreamWrapper();
        $this->bucketName = $bucketName;
        $this->zipFileName = $this->getDefaultFilename($filename);

        $this->zipStream = new ZipStream(
            outputName: $this->zipFileName
        );
    }

    /**
     * @param array $filesList
     * @return int
     * @throws Exceptions\EmptyFileListException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function downloadZippedFiles(array $filesList): int
    {
        $this->validateFilesList($filesList);

        foreach ($filesList as $filePath) {
            $s3Object = $this->s3Client->getObject(['Bucket' => $this->bucketName, 'Key' => $filePath]);
            $this->addFileToZipStream(basename($filePath), $s3Object['Body']);
        }

        return $this->zipStream->finish();
    }

    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }

    public function getZipFileName(): string
    {
        return $this->zipFileName;
    }

    private function getDefaultFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        $currentDateAndTime = date('Y-m-d_H-i-s');

        return self::DEFAULT_FILENAME_PREFIX . $currentDateAndTime . '.zip';
    }

    private function validateFilesList(array $filesList)
    {
        if (empty($filesList)) {
            throw new Exceptions\EmptyFileListException('File list cannot be empty');
        }
    }

    private function addFileToZipStream(string $fileName, $stream): void
    {
        $this->zipStream->addFileFromPsr7Stream(fileName: $fileName, stream: $stream);
    }
}
