<?php

namespace App\tests\Util;

use App\Util\FileInfo;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    public function testGetFormattedFileSize()
    {
        $this->assertEquals("0B", FileInfo::getFormattedFileSize(PHP_INT_MIN));
        $this->assertEquals("0B", FileInfo::getFormattedFileSize(0));
        $this->assertEquals("0B", FileInfo::getFormattedFileSize(-1));
        $this->assertEquals("666B", FileInfo::getFormattedFileSize(666));
        $this->assertEquals("999B", FileInfo::getFormattedFileSize(999));
        $this->assertEquals("1KB", FileInfo::getFormattedFileSize(1000));
        $this->assertEquals("1.1KB", FileInfo::getFormattedFileSize(1149));
        $this->assertEquals("1.5KB", FileInfo::getFormattedFileSize(1536));
        $this->assertEquals("1MB", FileInfo::getFormattedFileSize(1048576));
        $this->assertEquals("1.5MB", FileInfo::getFormattedFileSize(1549999));
        $this->assertEquals("1.5MB", FileInfo::getFormattedFileSize(1500000));
        $this->assertEquals("1.6MB", FileInfo::getFormattedFileSize(1550001));
        $this->assertEquals("0B", FileInfo::getFormattedFileSize(0));
        $this->assertEquals("9223372036854.8MB", FileInfo::getFormattedFileSize(PHP_INT_MAX));


        // This should return 1MB
        $this->assertEquals("1000KB", FileInfo::getFormattedFileSize(999999));
    }
}