<?php declare(strict_types=1);

namespace Ww\S3MassDownloader;

use Aws\S3\S3Client;
use ZipStream\ZipStream;

class StreamingClient
{
    private const DATE_FORMAT = 'Y-m-d_H-i-s';

    private const DEFAULT_FILENAME_PREFIX = 'project_';

    private S3Client $s3Client;
    private string $bucketName;
    private ZipStream $zipStream;
    private string $zipFileName;

    public function __construct(S3Client $s3Client, string $bucketName, string $zipFileName = '')
    {
        $this->s3Client = $s3Client;
        $this->s3Client->registerStreamWrapper();
        $this->bucketName = $bucketName;
        $this->zipFileName = $this->getDefaultFilename($zipFileName);
        $this->zipStream = new ZipStream(outputName: $this->zipFileName);
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

    public function setBucketName(string $bucketName): void
    {
        $this->bucketName = $bucketName;
    }

    public function setS3Client(S3Client $s3Client): void
    {
        $this->s3Client = $s3Client;
    }

    public function setZipFileName(string $zipFileName): void
    {
        $this->zipFileName = $zipFileName;
    }

    private function getDefaultFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        return self::DEFAULT_FILENAME_PREFIX . date(self::DATE_FORMAT) . '.zip';
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
