<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\Models\Repository;

/**
 * Defines test cases for ContentViewsManagerTest class.
 */
class ContentViewsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->dbConn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'fetchAll', 'executeUpdate' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Onm\Cache\Redis')
            ->setMethods([ 'get', 'set' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'error' ])
            ->getMock();
    }

    /**
     * Tests the getViews method with a single id
     */
    public function testgetViewsSingleId()
    {
        $id          = 1;
        $returnValue = 300;
        $dbValue     = [['views' => 300, 'pk_fk_content' => $id ]];
        $this->dbConn->expects($this->any())->method('fetchAll')
            ->will($this->returnValue($dbValue));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->getViews($id));
    }

    /**
     * Tests the getViews method with a single id and db failing to respond
     */
    public function testgetViewsFailedtoConectToDB()
    {
        $id          = 1;
        $returnValue = 0;
        $dbValue     = false;
        $this->dbConn->expects($this->any())->method('fetchAll')
            ->will($this->returnValue($dbValue));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->getViews($id));
    }

    /**
     * Tests the getViews method with multiple ids that are empty
     */
    public function testgetViewsMultipleIDsEmpty()
    {
        $id          = [];
        $returnValue = [];
        $dbValue     = [['views' => 300, 'pk_fk_content' => $id ]];
        $this->dbConn->expects($this->never())->method($this->anything());

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->getViews($id));
    }

    /**
     * Tests the getViews method with multiple ids
     */
    public function testgetViewsMultipleIds()
    {
        $ids          = [ 1, 2, 3 ];
        $returnValues = [
            1 => 100,
            2 => 200,
            3 => 4000
        ];
        $dbValues     = [
            [ 'pk_fk_content' => 1, 'views' => 100],
            [ 'pk_fk_content' => 2, 'views' => 200],
            [ 'pk_fk_content' => 3, 'views' => 4000],
        ];

        $this->dbConn->expects($this->any())->method('fetchAll')
            ->will($this->returnValue($dbValues));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValues, $this->contentViewManager->getViews($ids));
    }

    /**
     * Tests the getViews method with multiple ids that doesnt exists on the db
     */
    public function testgetViewsMultipleIdsNotExistent()
    {
        $id          = [ 1, 2, 3 ];
        $returnValue = [];
        $dbValue     = [];

        $this->dbConn->expects($this->any())->method('fetchAll')
            ->will($this->returnValue($dbValue));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->getViews($id));
    }

    /**
     * Tests the setViews method with a single id
     */
    public function testsetViews()
    {
        $id          = 1;
        $returnValue = true;
        $dbValue     = [];
        $sql         = 'UPDATE `content_views` SET views = views + 1'
            . ' WHERE pk_fk_content = ?';

        $this->dbConn->expects($this->once())->method('executeUpdate')
            ->with($this->equalTo($sql), $this->equalTo([$id]))
            ->will($this->returnValue(null));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->setViews($id));
    }

    /**
     * Tests the setViews method with a single id and a value
     */
    public function testsetViewsWithValue()
    {
        $id      = 1;
        $value   = 3;
        $dbValue = null;
        $sql     = 'UPDATE `content_views` SET views = ?'
            . ' WHERE pk_fk_content = ?';

        $this->dbConn->expects($this->once())->method('executeUpdate')
            ->with($this->equalTo($sql), $this->equalTo([$value, $id]))
            ->will($this->returnValue(null));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals(true, $this->contentViewManager->setViews($id, $value));
    }

    /**
     * Tests the setViews method with multiple ids
     */
    public function testsetViewsMultipleIds()
    {
        $id          = [ 1, 2, 3 ];
        $returnValue = true;
        $dbValue     = [];

        $this->dbConn->expects($this->once())->method('executeUpdate')
            ->will($this->returnValue(null));

        $this->contentViewManager = new \Repository\ContentViewsManager(
            $this->dbConn,
            $this->cache,
            $this->logger,
            'prefix'
        );

        $this->assertEquals($returnValue, $this->contentViewManager->setViews($id));
    }
}
