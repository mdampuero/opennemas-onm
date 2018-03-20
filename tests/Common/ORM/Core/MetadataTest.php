<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->metadata = new Metadata([
            'name' => 'Foo',
            'converters' => [
                'frog' => [
                    'class'     => 'Foo',
                    'arguments' => [ '@orm.metadata.norf' ]
                ]
            ],
            'datasets' => [
                'wibble' => [
                    'class'     => 'Glorp',
                    'arguments' => [ '@orm.metadata.norf' ]
                ]
            ],
            'persisters' => [
                'grault' => [
                    'class'     => 'Fred',
                    'arguments' => [ '@orm.metadata.norf' ]
                ]
            ],
            'repositories' => [
                'garply' => [
                    'class'     => 'Garply',
                    'arguments' => [ '@orm.metadata.norf' ]
                ]
            ],
            'mapping' => [
                'database' => [
                    'index' => [ [ 'columns' => [ 'id' ], 'primary' => true ] ]
                ]
            ]
        ]);
    }

    /**
     * Tests getClassName.
     */
    public function testGetClassName()
    {
        $this->assertEquals('Metadata', $this->metadata->getClassName());
    }

    /**
     * Tests getConverter with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidConverterException
     */
    public function testGetConverterWhenEmpty()
    {
        $metadata = new Metadata([]);
        $metadata->getConverter();
    }

    /**
     * Tests getConverter with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidConverterException
     */
    public function testGetConverterInvalidName()
    {
        $this->metadata->getConverter('flob');
    }

    /**
     * Tests getConverter with invalid name.
     */
    public function testGetConverterValidName()
    {
        $this->metadata->getConverter();
        $this->metadata->getConverter('frog');
    }

    /**
     * Tests getDataSetKey with valid and empty values in metadata.
     */
    public function testGetDataSetKey()
    {
        $this->metadata->mapping['database'] = [];
        $this->assertEquals('name', $this->metadata->getDataSetKey());

        $this->metadata->mapping['database'] = [ 'dataset' => [] ];
        $this->assertEquals('name', $this->metadata->getDataSetKey());

        $this->metadata->mapping['database'] = [
            'dataset' => [ 'key' => 'qux', 'value' => 'wobble' ]
        ];
        $this->assertEquals('qux', $this->metadata->getDataSetKey());
    }

    /**
     * Tests getDataSetValue with valid and empty values in metadata.
     */
    public function testGetDataSetValue()
    {
        $this->metadata->mapping['database'] = [];
        $this->assertEquals('value', $this->metadata->getDataSetValue());

        $this->metadata->mapping['database'] = [ 'dataset' => [] ];
        $this->assertEquals('value', $this->metadata->getDataSetValue());

        $this->metadata->mapping['database'] = [
            'dataset' => [ 'key' => 'qux', 'value' => 'wobble' ]
        ];
        $this->assertEquals('wobble', $this->metadata->getDataSetValue());
    }

    /**
     * Tests getDataSet with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidDataSetException
     */
    public function testGetDataSetWhenEmpty()
    {
        $metadata = new Metadata([]);
        $metadata->getDataSet();
    }

    /**
     * Tests getDataSet with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidDataSetException
     */
    public function testGetDataSetInvalidName()
    {
        $this->metadata->getDataSet('flob');
    }

    /**
     * Tests getDataSet with invalid name.
     */
    public function testGetDataSetValidName()
    {
        $this->metadata->getDataSet();
        $this->metadata->getDataSet('wibble');
    }

    /**
     * Tests getId with valid and invalid entities.
     */
    public function testGetId()
    {
        $entity = new Entity([ 'id' => 1, 'foo' => 'bar' ]);
        $this->assertEquals([ 'id' => 1 ], $this->metadata->getId($entity));
        $this->assertEmpty($this->metadata->getId(new Entity()));
    }

    /**
     * Tests getIdKeys with valid and empty values in metadata.
     */
    public function testGetIdKeys()
    {
        $this->metadata->mapping['database'] = [ 'index' => [] ];
        $this->assertEmpty($this->metadata->getIdKeys());

        $this->metadata->mapping['database'] = [ 'index' => [ [ 'name' => 'id' ] ] ];
        $this->assertEmpty($this->metadata->getIdKeys());

        $this->metadata->mapping['database'] = [
            'index' => [
                [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
            ]
        ];

        $this->assertEquals([ 'id' ], $this->metadata->getIdKeys());
    }

    /**
     * Tests getMetaKeyName with empty and non-empty meta definition in Metadata.
     */
    public function testGetMetaKeyName()
    {
        $this->metadata->mapping['database'] = [
            'index' => [
                [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
            ]
        ];

        $this->assertEquals('meta_key', $this->metadata->getMetaKeyName());

        $this->metadata->mapping['database'] = [
            'metas' => ['ids' => [ 'id' => 'bar' ], 'key' => 'baz' ]
        ];

        $this->assertEquals('baz', $this->metadata->getMetaKeyName());
    }

    /**
     * Tests getMetaKeys with empty and non-empty meta definition in Metadata.
     */
    public function testGetMetaKeys()
    {
        $this->metadata->mapping['database'] = [
            'index' => [
                [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
            ]
        ];

        $this->assertEquals([ 'id' => 'foo_id' ], $this->metadata->getMetaKeys());

        $this->metadata->mapping['database'] = [
            'metas' => ['ids' => [ 'id' => 'bar' ] ]
        ];

        $this->assertEquals([ 'id' => 'bar' ], $this->metadata->getMetaKeys());
    }

    /**
     * Tests getMetaTable with empty and non-empty meta definition in Metadata.
     */
    public function testGetMetaTable()
    {
        $this->assertEquals('foo_meta', $this->metadata->getMetaTable());

        $this->metadata->mapping['database'] = [
            'metas' => ['table' => 'foo_table_meta' ]
        ];
        $this->assertEquals('foo_table_meta', $this->metadata->getMetaTable());
    }

    /**
     * Tests getMetaValueName with empty and non-empty meta definition in Metadata.
     */
    public function testGetMetaValueName()
    {
        $this->metadata->mapping['database'] = [
            'index' => [
                [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
            ]
        ];

        $this->assertEquals('meta_value', $this->metadata->getMetaValueName());

        $this->metadata->mapping['database'] = [
            'metas' => ['ids' => [ 'id' => 'bar' ], 'value' => 'baz' ]
        ];

        $this->assertEquals('baz', $this->metadata->getMetaValueName());
    }

    /**
     * Tests getPersister with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterWhenEmpty()
    {
        $metadata = new Metadata([]);
        $metadata->getPersister();
    }

    /**
     * Tests getPersister with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalidName()
    {
        $this->metadata->getPersister('flob');
    }

    /**
     * Tests getPersister with invalid name.
     */
    public function testGetPersisterValidName()
    {
        $this->metadata->getPersister();
        $this->metadata->getPersister('grault');
    }

    /**
     * Tests getPrefix when prefix is missing and present in Metadata.
     */
    public function testGetPrefix()
    {
        $this->assertEquals('foo-', $this->metadata->getPrefix());

        $this->metadata->prefix = 'bar';
        $this->assertEquals('bar-', $this->metadata->getPrefix());
    }

    /**
     * Tests getPrefixedId.
     */
    public function testGetPrefixedId()
    {
        $entity = new Entity([ 'id' => 1 ]);
        $this->assertEquals('foo-1', $this->metadata->getPrefixedId($entity));
    }

    /**
     * Tests getSeparator.
     */
    public function testGetSeparator()
    {
        $this->assertEquals('-', $this->metadata->getSeparator());

        $this->metadata->separator = '_';
        $this->assertEquals('_', $this->metadata->getSeparator());
    }

    /**
     * Tests getRelationColumns when relations are missing and present in
     * Metadata.
     */
    public function testGetRelationColumns()
    {
        $this->assertEmpty($this->metadata->getRelationColumns());

        $this->metadata->mapping['database'] = [
            'relations' => [
                'glorp' => [
                    'table'   => 'glorp_table',
                    'ids'     => [ 'id' => 'foo_id' ],
                    'columns' => [
                        'flob' => [ 'type' => 'integer' ],
                        'qux'  => [ 'type' => 'string' ]
                    ]
                ],
                'wubble' => [
                    'table'   => 'wubble_table',
                    'ids'     => [ 'id' => 'foo_id' ],
                    'columns' => [
                        'grault' => [ 'type' => 'datetimez' ]
                    ]
                ]
            ]
        ];

        $this->assertEquals(
            [ 'flob', 'qux', 'grault' ],
            $this->metadata->getRelationColumns()
        );
    }

    /**
     * Tests getRelations when relations are missing and present in Metadata.
     */
    public function testGetRelations()
    {
        $this->assertEmpty($this->metadata->getRelations());

        $this->metadata->mapping['database'] = [
            'relations' => [
                [ 'table' => 'glorp_table', 'ids' => [ 'id' => 'foo_id' ] ]
            ]
        ];

        $this->assertEquals([
            [
                'table' => 'glorp_table',
                'ids' => [ 'id' => 'foo_id' ]
            ]
        ], $this->metadata->getRelations());
    }

    /**
     * Tests getRepository with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryWhenEmpty()
    {
        $metadata = new Metadata([]);
        $metadata->getRepository();
    }

    /**
     * Tests getRepository with invalid name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalidName()
    {
        $this->metadata->getRepository('flob');
    }

    /**
     * Tests getRepository with invalid name.
     */
    public function testGetRepositoryValidName()
    {
        $this->metadata->getRepository();
        $this->metadata->getRepository('garply');
    }

    /**
     * Tests getTable when table is missing and present in Metadata.
     */
    public function testGetTable()
    {
        $this->assertEquals('foo', $this->metadata->getTable());

        $this->metadata->mapping['database'] = [ 'table' => 'foo_table' ];
        $this->assertEquals('foo_table', $this->metadata->getTable());
    }

    /**
     * Tests hasMetas when meta is missing and present in Metadata.
     */
    public function testHasMetas()
    {
        $this->assertFalse($this->metadata->hasMetas());

        $this->metadata->mapping['database'] = [ 'metas' => ['table' => 'foo_table_meta' ] ];
        $this->assertTrue($this->metadata->hasMetas());
    }

    /**
     * Tests hasRelations when relations are missing and present in Metadata.
     */
    public function testHasRelations()
    {
        $this->assertFalse($this->metadata->hasRelations());

        $this->metadata->mapping['database'] = [
            'relations' => [
                [ 'table' => 'glorp_table', 'ids' => [ 'id' => 'foo_id' ] ]
            ]
        ];

        $this->assertTrue($this->metadata->hasRelations());
    }

    /**
     * Tests normalizeId for scalar and array values.
     */
    public function testNormalizeId()
    {
        $this->assertEquals([ 'id' => 1 ], $this->metadata->normalizeId(1));
        $this->assertEquals([ 'id' => 1 ], $this->metadata->normalizeId([ 'id' => 1 ]));
    }
}
