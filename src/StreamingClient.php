<?php

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

    public function getZippedFiles(array $filesList)
    {
        if (empty($filesList)) {
            return;
        }

        foreach ($filesList as $filePath) {
            $this->zipStream->addFileFromPath(
                fileName: basename($filePath),
                path: $filePath
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
