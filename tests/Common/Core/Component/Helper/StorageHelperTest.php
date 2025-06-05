<?php

namespace Tests\Common\Core\Component\Helper;

use PHPUnit\Framework\TestCase;
use Common\Core\Component\Helper\StorageHelper;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

class StorageHelperTest extends TestCase
{
    /**
     * @var Filesystem&MockObject
     */
    private $filesystemMock;
    private $loggerMock;
    private $storageHelper;

    protected function setUp(): void
    {
        $this->filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['put', 'delete', 'fileExists', 'has', 'read', 'listContents'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->storageHelper = new StorageHelper(
            $this->filesystemMock,
            $this->loggerMock,
            $this->loggerMock
        );
    }

    /**
     * Tests successful file upload.
     */
    public function testUploadSuccess()
    {
        $this->filesystemMock->expects($this->once())
            ->method('put')
            ->with('file.txt', 'content')
            ->willReturn(true);

        $this->assertTrue($this->storageHelper->upload('file.txt', 'content'));
    }

    /**
     * Tests successful file deletion.
     */
    public function testDeleteSuccess()
    {
        $this->filesystemMock->expects($this->once())
            ->method('delete')
            ->with('file.txt')
            ->willReturn(true);

        $this->assertTrue($this->storageHelper->delete('file.txt'));
    }

    /**
     * Tests checking file existence returns true.
     */
    public function testExistsReturnsTrue()
    {
        $this->filesystemMock->method('fileExists')->willReturn(true);
        $this->assertTrue($this->storageHelper->exists('file.txt'));
    }

    /**
     * Tests reading file content returns expected string.
     */
    public function testReadReturnsContent()
    {
        $this->filesystemMock->method('read')->willReturn('content');
        $this->assertEquals('content', $this->storageHelper->read('file.txt'));
    }

    /**
     * Tests replacing a file by deleting and uploading new content.
     */
    public function testReplaceReplacesFile()
    {
        $this->filesystemMock->method('fileExists')->willReturn(true);

        $this->filesystemMock->expects($this->once())
            ->method('delete')
            ->with('file.txt');

        $this->filesystemMock->expects($this->once())
            ->method('put')
            ->with('file.txt', 'new content')
            ->willReturn(true);

        $this->assertTrue($this->storageHelper->replace('file.txt', 'new content'));
    }

    /**
     * Tests listing directory contents returns expected array.
     */
    public function testListContentsReturnsArray()
    {
        $expected = [
            ['type' => 'file', 'path' => 'folder/file1.txt'],
            ['type' => 'file', 'path' => 'folder/file2.txt'],
        ];

        $this->filesystemMock->method('listContents')->willReturn(new \ArrayIterator($expected));

        $this->assertEquals($expected, $this->storageHelper->listContents('folder'));
    }

    /**
     * Full integration test for upload, exists, read, delete.
     */
    public function testFullFileLifecycleIntegration()
    {
        $testConfig = [
            'key'        => 'DO801FCQYMJ92HJ3YXEV',
            'secret'     => '16u0BZ/XOIw+6yfH5BoCBnWqlluIL6v7f0IwAXemiBo',
            'region'     => 'ams3',
            'bucket'     => 'onm-test',
            'endpoint'   => 'https://ams3.digitaloceanspaces.com',
            'path_style' => true
        ];

        $storageHelper = \Common\Core\Component\Helper\StorageHelperFactory::create(
            $testConfig,
            $this->loggerMock,
            $this->loggerMock
        );

        $path    = 'test/file.txt';
        $content = 'test content';

        // Upload the file
        $this->assertTrue($storageHelper->upload($path, $content, ['visibility' => 'public']));

        // Verify it exists
        $this->assertTrue($storageHelper->exists($path));

        // Read and verify content
        $readContent = $storageHelper->read($path);
        $this->assertEquals($content, $readContent);

        // Delete it
        $this->assertTrue($storageHelper->delete($path));

        // Confirm it's gone
        $this->assertFalse($storageHelper->exists($path));
    }
}
