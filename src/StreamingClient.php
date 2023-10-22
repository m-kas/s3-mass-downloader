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

    /**
     * @param S3Client $s3Client
     * @param string $bucketName
     * @param string $zipFileName
     * @throws Exceptions\EmptyArgumentException
     */
    public function __construct(S3Client $s3Client, string $bucketName, string $zipFileName = '')
    {
        $this->validateNonEmptyArgument($bucketName, 'Bucket name cannot be empty');

        $this->s3Client = $s3Client;
        $this->s3Client->registerStreamWrapper();
        $this->bucketName = $bucketName;
        $this->zipFileName = $this->getDefaultFilename($zipFileName);
        $this->zipStream = new ZipStream(outputName: $this->zipFileName);
    }

    /**
     * @param string[] $filesList
     * @return int Number of bytes
     * @throws Exceptions\EmptyArgumentException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function downloadZippedFiles(array $filesList): int
    {
        $this->validateNonEmptyArgument($filesList, 'File list cannot be empty');

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

    /**
     * @param string $bucketName
     * @return void
     * @throws Exceptions\EmptyArgumentException
     */
    public function setBucketName(string $bucketName): void
    {
        $this->validateNonEmptyArgument($bucketName, 'Bucket name cannot be empty');
        $this->bucketName = $bucketName;
    }

    /**
     * @param S3Client $s3Client
     * @return void
     */
    public function setS3Client(S3Client $s3Client): void
    {
        $this->s3Client = $s3Client;
    }

    /**
     * @param string $zipFileName
     * @return void
     * @throws Exceptions\EmptyArgumentException
     */
    public function setZipFileName(string $zipFileName): void
    {
        $this->validateNonEmptyArgument($zipFileName, 'Zip file name cannot be empty');
        $this->zipFileName = $zipFileName;
    }

    private function getDefaultFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        return self::DEFAULT_FILENAME_PREFIX . date(self::DATE_FORMAT) . '.zip';
    }

    private function validateNonEmptyArgument($argument, string $message): void
    {
        if (empty($argument)) {
            throw new Exceptions\EmptyArgumentException($message);
        }
    }

    private function addFileToZipStream(string $fileName, $stream): void
    {
        $this->zipStream->addFileFromPsr7Stream(fileName: $fileName, stream: $stream);
    }
}
