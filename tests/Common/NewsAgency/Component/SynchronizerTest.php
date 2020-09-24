<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Synchronizer;

use Common\NewsAgency\Component\Synchronizer\Synchronizer;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for Synchronizer class.
 */
class SynchronizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->instance = new Instance([ 'internal_name' => 'qux' ]);

        $this->file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContents', 'isWriteable' ])
            ->getMock();

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'date', 'files', 'in', 'name' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([
                'chgrp', 'dumpFile', 'exists', 'mkdir', 'remove', 'touch'
            ])->getMock();

        $this->logger = $this->getMockBuilder('Monolog')
            ->setMethods([ 'notice' ])
            ->getMock();

        $this->parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'parse' ])
            ->getMock();

        $this->pf = $this->getMockBuilder('Common\NewsAgency\Component\Factory\ParserFactory')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Common\NewsAgency\Component\Repository\LocalRepository')
            ->setMethods([ 'remove', 'setContents', 'write' ])
            ->getMock();

        $this->server = $this->getMockBuilder('Common\NewsAgency\Component\Server\Server')
            ->disableOriginalConstructor()
            ->setMethods([
                'checkConnection', 'checkParameters', 'downloadFiles',
                'getRemoteFiles'
            ])->getMock();

        $this->sf = $this->getMockBuilder('Common\NewsAgency\Component\Factory\ServerFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->container->expects($this->any())->method('getParameter')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->pf->expects($this->any())->method('get')
            ->willReturn($this->parser);

        $this->sf->expects($this->any())->method('get')
            ->willReturn($this->server);

        $this->synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getFile', 'getFinder', 'loadXmlFile' ])
            ->getMock();

        $this->synchronizer->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $property = new \ReflectionProperty($this->synchronizer, 'fs');
        $property->setAccessible(true);
        $property->setValue($this->synchronizer, $this->fs);
    }

    /**
     * Returns the mock basing on the requested service.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'core.paths.cache':
                return '/plugh/corge';

            case 'application.log':
                return $this->logger;

            case 'news_agency.factory.parser':
                return $this->pf;

            case 'news_agency.factory.server':
                return $this->sf;

            default:
                return null;
        }
    }

    /**
     * Tests empty when the environment is not ready.
     */
    public function testEmptyWhenNotReady()
    {
        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->disableOriginalConstructor()
            ->setMethods([
                'emptyServer', 'isSyncEnvironmentReady', 'lockSync', 'unlockSync'
            ])->getMock();

        $synchronizer->expects($this->once())->method('isSyncEnvironmentReady')
            ->willReturn(false);

        $synchronizer->empty([ 'id' => 23118 ]);
    }

    /**
     * Tests empty when the provided parameter is a server.
     */
    public function testEmptyForList()
    {
        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->disableOriginalConstructor()
            ->setMethods([
                'emptyServer', 'isSyncEnvironmentReady', 'lockSync', 'unlockSync'
            ])->getMock();

        $serverA = [ 'id' => 22398 ];
        $serverB = [ 'id' => 21400 ];

        $synchronizer->expects($this->once())->method('isSyncEnvironmentReady')
            ->willReturn(true);
        $synchronizer->expects($this->once())->method('lockSync');
        $synchronizer->expects($this->at(2))->method('emptyServer')
            ->with($serverA);
        $synchronizer->expects($this->at(3))->method('emptyServer')
            ->with($serverB);
        $synchronizer->expects($this->once())->method('unlockSync');

        $synchronizer->empty([ $serverA, $serverB ]);
    }

    /**
     * Tests empty when the provided parameter is a server.
     */
    public function testEmptyForServer()
    {
        $server = [ 'id' => 22398 ];

        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->disableOriginalConstructor()
            ->setMethods([ 'emptyServer', 'isSyncEnvironmentReady', 'lockSync', 'unlockSync' ])
            ->getMock();

        $synchronizer->expects($this->once())->method('isSyncEnvironmentReady')
            ->willReturn(true);
        $synchronizer->expects($this->once())->method('lockSync');
        $synchronizer->expects($this->once())->method('emptyServer')
            ->with($server);
        $synchronizer->expects($this->once())->method('unlockSync');

        $synchronizer->empty($server);
    }

    /**
     * Tests getResourceStats.
     */
    public function testGetResourceStats()
    {
        $this->assertIsArray($this->synchronizer->getResourceStats());
    }

    /**
     * Tests getServerStats when .sync file was found.
     */
    public function testGetServerStatsWhenFileFound()
    {
        $this->fs->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->synchronizer->expects($this->once())->method('getFile')
            ->willReturn($this->file);

        $this->file->expects($this->once())->method('getContents')
            ->willReturn(serialize([ 'last_import' => 6765 ]));

        $params = $this->synchronizer->getServerStats();

        $this->assertIsArray($params);
        $this->assertEquals(6765, $params['last_import']);
    }

    /**
     * Tests getServerStats when .sync file was not found.
     */
    public function testGetServerStatsWhenNoFileFound()
    {
        $this->fs->expects($this->once())->method('exists')
            ->willReturn(false);


        $params = $this->synchronizer->getServerStats();

        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }

    /**
     * Tests isLocked when .lock file is and is not found.
     */
    public function testIsLocked()
    {
        $this->fs->expects($this->at(0))->method('exists')
            ->willReturn(false);

        $this->fs->expects($this->at(1))->method('exists')
            ->willReturn(true);

        $this->assertFalse($this->synchronizer->isLocked());
        $this->assertTrue($this->synchronizer->isLocked());
    }

    /**
     * Tests resetStats.
     */
    public function testResetStats()
    {
        $property = new \ReflectionProperty($this->synchronizer, 'stats');
        $property->setAccessible(true);

        $this->assertEquals($this->synchronizer, $this->synchronizer->resetStats());
        $this->assertEquals([
            'contents'   => 0,
            'deleted'    => 0,
            'downloaded' => 0,
            'parsed'     => 0,
            'invalid'    => 0,
            'valid'      => 0,
        ], $property->getValue($this->synchronizer));
    }

    /**
     * Tests synchronize when the provided parameter is a server.
     */
    public function testSynchronizeForList()
    {
        $serverA = [ 'id' => 19660, 'activated' => 0 ];
        $serverB = [ 'id' => 20601, 'activated' => 1 ];

        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->disableOriginalConstructor()
            ->setMethods([
                'isSyncEnvironmentReady', 'lockSync', 'unlockSync',
                'setupSyncEnvironment', 'updateServer', 'updateSyncFile'
            ])->getMock();

        $synchronizer->expects($this->once())->method('isSyncEnvironmentReady')
            ->willReturn(false);
        $synchronizer->expects($this->once())->method('setupSyncEnvironment');
        $synchronizer->expects($this->once())->method('lockSync');
        $synchronizer->expects($this->once())->method('updateServer')
            ->with($serverB);
        $synchronizer->expects($this->once())->method('updateSyncFile');
        $synchronizer->expects($this->once())->method('unlockSync');

        $this->assertEquals($synchronizer, $synchronizer->synchronize([ $serverA, $serverB ]));
    }

    /**
     * Tests synchronize when the provided parameter is a server.
     */
    public function testSynchronizeForServer()
    {
        $server = [ 'id' => 19660, 'activated' => 1 ];

        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->disableOriginalConstructor()
            ->setMethods([
                'isSyncEnvironmentReady', 'lockSync', 'unlockSync',
                'setupSyncEnvironment', 'updateServer', 'updateSyncFile'
            ])->getMock();

        $synchronizer->expects($this->once())->method('isSyncEnvironmentReady')
            ->willReturn(false);
        $synchronizer->expects($this->once())->method('setupSyncEnvironment');
        $synchronizer->expects($this->once())->method('lockSync');
        $synchronizer->expects($this->once())->method('updateServer')
            ->with($server);
        $synchronizer->expects($this->once())->method('updateSyncFile');
        $synchronizer->expects($this->once())->method('unlockSync');

        $this->assertEquals($synchronizer, $synchronizer->synchronize($server));
    }

    /**
     * Tests cleanServer when the server is configured with limits so the server
     * only has to include files in the limit.
     */
    public function testCleanServerWhenLimits()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'cleanServer');
        $method->setAccessible(true);

        $this->finder->expects($this->at(0))->method('in')
            ->with('/plugh/corge/qux/importers/7748')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(1))->method('date')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(2))->method('files')
            ->willReturn([ 'wubble', 'foobar' ]);

        $this->fs->expects($this->at(0))->method('remove')
            ->with('wubble');
        $this->fs->expects($this->at(1))->method('remove')
            ->with('foobar');

        $this->finder->expects($this->at(3))->method('in')
            ->with('/plugh/corge/qux/importers')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(4))->method('name')
            ->with('/sync.7748.*.php/')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(5))->method('files')
            ->willReturn([ 'gorp' ]);

        $this->fs->expects($this->at(2))->method('remove')
            ->with([ 'gorp' ]);

        $method->invokeArgs($this->synchronizer, [
            [ 'id' => 7748, 'sync_from' => 3600 ]
        ]);
    }

    /**
     * Tests cleanServer when the server is configured with no limits so the
     * server never has to be cleaned.
     */
    public function testCleanServerWhenNoLimits()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'cleanServer');
        $method->setAccessible(true);

        $this->assertEmpty(
            $method->invokeArgs($this->synchronizer, [ [ 'sync_from' => 'no_limits' ] ])
        );
    }

    /**
     * Tests emptyServer.
     */
    public function testEmptyServer()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'emptyServer');
        $method->setAccessible(true);

        $this->fs->expects($this->at(0))->method('remove')
            ->with('/plugh/corge/qux/importers/24474');

        $this->finder->expects($this->at(0))->method('in')
            ->with('/plugh/corge/qux/importers')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(1))->method('name')
            ->with('/sync.24474.*.php/')
            ->willReturn($this->finder);
        $this->finder->expects($this->at(2))->method('files')
            ->willReturn([ 'sync.24474.15714.php' ]);

        $this->fs->expects($this->at(1))->method('remove')
            ->with([ 'sync.24474.15714.php' ]);

        $this->assertEmpty(
            $method->invokeArgs($this->synchronizer, [ [ 'id' => 24474 ] ])
        );
    }

    /**
     * Tests getFile.
     */
    public function testGetFile()
    {
        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $method = new \ReflectionMethod($synchronizer, 'getFile');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\Finder\SplFileInfo',
            $method->invokeArgs($synchronizer, [ '/glork/mumble' ])
        );
    }

    /**
     * Tests getFiles.
     */
    public function testGetFiles()
    {
        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRealPath' ])
            ->getMock();

        $method = new \ReflectionMethod($this->synchronizer, 'getFiles');
        $method->setAccessible(true);

        $this->finder->expects($this->once())->method('in')
            ->with('/plugh/corge/qux/importers/29978')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('name')
            ->with('/.*\.xml/')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('files')
            ->willReturn([ $file ]);

        $file->expects($this->once())->method('getRealPath')
            ->willReturn('/thud/plugh');

        $this->assertEquals(
            [ '/thud/plugh' ],
            $method->invokeArgs($this->synchronizer, [ [ 'id' => 29978 ] ])
        );
    }

    /**
     * Tests getFinder.
     */
    public function testGetFinder()
    {
        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $method = new \ReflectionMethod($synchronizer, 'getFinder');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\Finder\Finder',
            $method->invokeArgs($synchronizer, [])
        );
    }

    /**
     * Tests getMissingFiles.
     */
    public function testGetMissingFiles()
    {
        $contents = [
            json_decode(json_encode([
                'type'      => 'photo',
                'file_name' => 'xyzzy',
                'file_path' => 'http://grault.bar'
            ])),
            json_decode(json_encode([
                'type'      => 'photo',
                'file_name' => 'thud',
                'file_path' => 'http://thud.glork'
            ])),
            json_decode(json_encode([
                'type'      => 'text',
                'file_name' => 'wobble',
                'file_path' => 'http://wobble.baz'
            ])),
        ];

        $method = new \ReflectionMethod($this->synchronizer, 'getMissingFiles');
        $method->setAccessible(true);

        $this->fs->expects($this->at(0))->method('exists')
            ->with('/frog/flob/xyzzy')->willReturn(false);
        $this->fs->expects($this->at(1))->method('exists')
            ->with('/frog/flob/thud')->willReturn(true);

        $this->assertEquals(
            [ [ 'filename' => 'xyzzy', 'url' => 'http://grault.bar' ] ],
            $method->invokeArgs($this->synchronizer, [ $contents, '/frog/flob' ])
        );
    }

    /**
     * Tests getServerPath.
     */
    public function testGetServerPath()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'getServerPath');
        $method->setAccessible(true);

        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers/22698')
            ->willReturn(false);
        $this->fs->expects($this->once())->method('mkdir')
            ->with('/plugh/corge/qux/importers/22698');

        $this->assertEquals(
            '/plugh/corge/qux/importers/22698',
            $method->invokeArgs($this->synchronizer, [ [ 'id' => 22698 ] ])
        );
    }

    /**
     * Tests isSyncEnvironmentReady.
     */
    public function testIsSyncEnvironmentReady()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers')
            ->willReturn(false);

        $method = new \ReflectionMethod($this->synchronizer, 'isSyncEnvironmentReady');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->synchronizer, []));
    }

    /**
     * Tests lockSync.
     */
    public function testLockSync()
    {
        $this->fs->expects($this->once())->method('touch')
            ->with('/plugh/corge/qux/importers/.lock')
            ->willReturn(true);

        $this->fs->expects($this->once())->method('chgrp')
            ->with('/plugh/corge/qux/importers/.lock', 'www-data', true)
            ->willReturn(true);

        $method = new \ReflectionMethod($this->synchronizer, 'lockSync');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests parseFiles when error.
     */
    public function testParseFilesWhenError()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'parseFiles');
        $method->setAccessible(true);

        $this->fs->expects($this->at(0))->method('exists')
            ->with('glork')->willReturn(true);

        $this->synchronizer->expects($this->once())->method('loadXmlFile')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('notice')
            ->with('Cannot parse XML: glork');

        $method->invokeArgs($this->synchronizer, [ [ 'glork' ], 1 ]);
    }

    /**
     * Tests parseFiles when the provided files exist.
     */
    public function testParseFilesWhenFileFound()
    {
        $xml = new \SimpleXMLElement('<foo></foo>');

        $method = new \ReflectionMethod($this->synchronizer, 'parseFiles');
        $method->setAccessible(true);

        $this->fs->expects($this->at(0))->method('exists')
            ->with('glork')->willReturn(true);

        $this->synchronizer->expects($this->once())->method('loadXmlFile')
            ->with('glork')->willReturn($xml);

        $this->parser->expects($this->once())->method('parse')
            ->with($xml)->willReturn(json_decode(json_encode([ 'id' => 15584 ])));

        $contents = $method->invokeArgs($this->synchronizer, [ [ 'glork' ], 1 ]);
        $this->assertIsArray($contents);
        $this->assertEquals(1, $contents[0]->source);
        $this->assertEquals('glork', $contents[0]->filename);
    }

    /**
     * Tests parseFiles when the provided files do not exist..
     */
    public function testParseFilesWhenFileNotFound()
    {
        $method = new \ReflectionMethod($this->synchronizer, 'parseFiles');
        $method->setAccessible(true);

        $this->fs->expects($this->at(0))->method('exists')
            ->with('glork')->willReturn(false);

        $this->assertEmpty($method->invokeArgs($this->synchronizer, [ [ 'glork' ], 1 ]));
    }

    /**
     * Tests setInstance.
     */
    public function testSetInstance()
    {
        $this->assertEquals(
            $this->synchronizer,
            $this->synchronizer->setInstance($this->instance)
        );
    }

    /**
     * Tests setupSyncEnvironment.
     */
    public function testSetupSyncEnvironment()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers')
            ->willReturn(false);
        $this->fs->expects($this->once())->method('mkdir')
            ->with('/plugh/corge/qux/importers');

        $method = new \ReflectionMethod($this->synchronizer, 'setupSyncEnvironment');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests unlockSync.
     */
    public function testUnlockSync()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers/.lock')
            ->willReturn(true);
        $this->fs->expects($this->once())->method('remove')
            ->with('/plugh/corge/qux/importers/.lock');

        $method = new \ReflectionMethod($this->synchronizer, 'unlockSync');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests updateServer.
     */
    public function testUpdateServer()
    {
        $server       = [ 'id' => 14535, 'sync_from' => 3600 ];
        $synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->setConstructorArgs([ $this->container ])
            ->setmethods([
                'cleanServer', 'getFiles', 'getMissingFiles', 'getServerPath',
                'parseFiles', 'removeInvalidContents'
            ])->getMock();

        $property = new \ReflectionProperty($synchronizer, 'fs');
        $property->setAccessible(true);
        $property->setValue($synchronizer, $this->fs);

        $property = new \ReflectionProperty($synchronizer, 'repository');
        $property->setAccessible(true);
        $property->setValue($synchronizer, $this->repository);

        $this->fs->expects($this->at(0))->method('chgrp')
            ->with('/plugh/corge/qux/importers/14535');
        $this->fs->expects($this->at(1))->method('chgrp');

        $this->server->expects($this->at(0))->method('getRemoteFiles')
            ->willReturn($this->server);
        $this->server->expects($this->at(1))->method('downloadFiles')
            ->willReturn($this->server);

        $synchronizer->expects($this->once())->method('getServerPath')
            ->with($server)->willReturn('/plugh/corge/qux/importers/14535');
        $synchronizer->expects($this->once())->method('getFiles')
            ->with($server)->willReturn([ 'quux' ]);
        $synchronizer->expects($this->once())->method('parseFiles')
            ->with([ 'quux' ], 14535)->willReturn([ 'garply' ]);
        $synchronizer->expects($this->once())->method('getMissingFiles')
            ->with([ 'garply' ], '/plugh/corge/qux/importers/14535')
            ->willReturn([ 'frog' ]);
        $synchronizer->expects($this->once())->method('removeInvalidContents')
            ->with([ 'garply' ], '/plugh/corge/qux/importers/14535');

        $method = new \ReflectionMethod($synchronizer, 'updateServer');
        $method->setAccessible(true);

        $method->invokeArgs($synchronizer, [ $server ]);
    }

    /**
     * Tests updateSyncFile.
     */
    public function testUpdateSyncFile()
    {
        $this->fs->expects($this->once())->method('dumpFile')
            ->with('/plugh/corge/qux/importers/.sync');

        $method = new \ReflectionMethod($this->synchronizer, 'updateSyncFile');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }
}
