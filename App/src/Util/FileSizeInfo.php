<?php

declare(strict_types = 1);

namespace App\Util;

class FileSizeInfo
{
    public static $Byte = 'B';
    public static $KiloByte = 'KB';
    public static $MegaByte = 'MB';

    public static $ByteMinSize = 0;
    public static $KiloByteMinSizeInByte = 1000;
    public static $MegaByteMinSizeInByte = 1000000;
}