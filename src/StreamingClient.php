<?php declare(strict_types=1);

namespace Ww\S3MassDownloader;

use ZipStream\ZipStream;

class StreamingClient
{
    private const DEFAULT_FILENAME_PREFIX = 'project_';
    private ZipStream $zipStream;

    public function __construct(string $filename = '')
    {
        $this->zipStream = new ZipStream(
            outputName: $this->getFilename($filename)
        );
    }

    /**
     * @param array $filesList
     * @return int
     * @throws Exceptions\EmptyFileListException
     * @throws \ZipStream\Exception\FileNotFoundException
     * @throws \ZipStream\Exception\FileNotReadableException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function getZippedFiles(array $filesList)
    {
        if (empty($filesList)) {
            throw new Exceptions\EmptyFileListException('File list cannot be empty');
        }

        foreach ($filesList as $filePath) {
            $this->zipStream->addFile(
                fileName: basename($filePath),
                data: file_get_contents($filePath)
            );
        }

        return $this->zipStream->finish();
    }

    private function getFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        $currentDateAndTime = date('Y-m-d_H-i-s');

        return self::DEFAULT_FILENAME_PREFIX . $currentDateAndTime . '.zip';
    }
}
