<?php declare(strict_types=1);

namespace Ww\S3MassDownloader;

use Aws\S3\S3Client;
use ZipStream\ZipStream;

class StreamingClient
{
    private const DEFAULT_FILENAME_PREFIX = 'project_';

    private S3Client $s3Client;
    private ZipStream $zipStream;


    public function __construct(S3Client $s3Client, string $filename = '')
    {
        $this->s3Client = $s3Client;
        $this->s3Client->registerStreamWrapper();

        $this->zipStream = new ZipStream(
            outputName: $this->getFilename($filename)
        );
    }

    /**
     * @param array $filesList
     * @return int
     * @throws Exceptions\EmptyFileListException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function getZippedFiles(array $filesList)
    {
        if (empty($filesList)) {
            throw new Exceptions\EmptyFileListException('File list cannot be empty');
        }

        foreach ($filesList as $filePath) {
            $this->zipStream->addFileFromPsr7Stream(
                fileName: basename($filePath),
                stream: $this->s3Client->getObject(
                    [
                        'Bucket' => 'files.belle-ai.com',
                        'Key' => $filePath,
                    ]
                )['Body']
            );
        }

        return $this->zipStream->finish();
    }

    protected function getFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        $currentDateAndTime = date('Y-m-d_H-i-s');

        return self::DEFAULT_FILENAME_PREFIX . $currentDateAndTime . '.zip';
    }
}
