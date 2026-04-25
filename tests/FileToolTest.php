<?php

use PHPUnit\Framework\TestCase;
use PHPFileTool\FileTool\FileTool;

class FileToolTest extends TestCase
{
    public function testFacadeCreateAndRead(): void
    {
        $file = TEST_DIR . '/facade.txt';

        FileTool::createFile(TEST_DIR, 'facade.txt');

        FileTool::writeFile($file, 'Facade Test');

        $content = FileTool::readFile($file);

        $this->assertEquals('Facade Test', $content);

        unlink($file);
    }
}