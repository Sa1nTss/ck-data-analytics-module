<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper
{
    public function saveFileFromTmp(UploadedFile $file): ?string
    {
        $tmpFilePath = $file->getPathname();
        $newFilePath = $this->getFilesDocRoot().'files'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.uniqid('excel_', true).'.'.$file->getClientOriginalExtension();

        if (copy($tmpFilePath, $newFilePath)) {
            chmod($newFilePath, 0777);
            return $newFilePath;
        } else {
            return null;
        }
    }

    protected function getFilesDocRoot(): string
    {
        $currDocRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

        if ('cli' == php_sapi_name()) {
            if (!empty($_SERVER['PWD'])) {
                $currDocRoot = $_SERVER['PWD'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_FILENAME'];
                $currDocRoot = realpath($currDocRoot);
                $currDocRoot = substr($currDocRoot, 0, strpos($currDocRoot, '/bin/console'));
            } else {
                // src/Service
                $currDocRoot = __DIR__;
                $currDocRoot = realpath($currDocRoot.DIRECTORY_SEPARATOR.'../..');
            }

            $currDocRoot = rtrim($currDocRoot, '/').'/public';
        }

        if (!empty($_ENV) && !empty($_ENV['DOWNLOAD_PATH'])) {
            $currDocRoot = trim($_ENV['DOWNLOAD_PATH']);
            $currDocRoot = rtrim($currDocRoot, '/').'/public';
        }

        return $currDocRoot;
    }
}
