<?php

declare(strict_types = 1);

namespace App\Util;


use App\Entity\Userfile;
use App\Util\FileSizeInfo;

class FileInfo
{
    public static function getFullFilePath(Userfile $userFile) : string
    {
        return $userFile->getPath() . '/' . $userFile->getName() . "-" . $userFile->getId() . '.' . $userFile->getFiletype();
    }

    public static function getFullFileName(Userfile $userfile) : string
    {
        return $userfile->getName() . '.' . $userfile->getFiletype();
    }

    /**
     * changes input (byte) int to a better readable format
     * 666 => 666B
     * 1536 => 1,5KB
     * 1048576 => 1MB
     *
     * @param int $fileSize
     * @return string
     */
    public static function getFormattedFileSize(int $fileSize) : string
    {
        if ($fileSize < 0) return "0" . FileSizeInfo::$Byte;

        // TODO: Input 999999 gets divided by 1000 and then round up to 1000KB, should be 1MB

        switch (true) {
            case $fileSize >= FileSizeInfo::$MegaByteMinSizeInByte:
                $formattedFileSize = round($fileSize / FileSizeInfo::$MegaByteMinSizeInByte, 1) . FileSizeInfo::$MegaByte;
                break;
            case $fileSize >= FileSizeInfo::$KiloByteMinSizeInByte:
                $formattedFileSize = round($fileSize / FileSizeInfo::$KiloByteMinSizeInByte, 1) . FileSizeInfo::$KiloByte;
                break;
            default:
                $formattedFileSize = $fileSize . FileSizeInfo::$Byte;
        }

        return $formattedFileSize;
    }
}