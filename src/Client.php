<?php

namespace Ww\S3MassDownloader;

use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class Client
{
    public function getZippedFiles(array $filesList, string $filename = ''): StreamedResponse
    {
        $filename = $this->getFilename($filename);

        $response = new StreamedResponse(callback: function () use ($filesList) {
            $zip = new ZipArchive();
            $zip->open('php://output', ZipArchive::CREATE);

            foreach ($filesList as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }

            try {
                $zip->close();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        });

        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response->send();
    }

    private function getFilename(string $filename = ''): string
    {
        if (!empty($filename)) {
            return $filename;
        }

        $currentDateAndTime = date('Y-m-d_H-i-s');

        return "project_{$currentDateAndTime}.zip";
    }
}
