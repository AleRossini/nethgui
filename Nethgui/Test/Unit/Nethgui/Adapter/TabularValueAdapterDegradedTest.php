<?php
namespace Nethgui\Test\Unit\Nethgui\Adapter;

/**
 * @covers \Nethgui\Adapter\TabularValueAdapter
 */
class TabularValueAdapterDegradedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Nethgui\Adapter\TabularValueAdapter
     */
    protected $object;

    /**
     *
     * @var \Nethgui\Serializer\SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        $this->serializer = $this->getMockBuilder('\Nethgui\Serializer\KeySerializer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer->expects($this->any())->method('read')
            ->will($this->returnValue('1A,2A,3A'));

        $innerAdapter = new \Nethgui\Adapter\ArrayAdapter(',', $this->serializer);

        $this->object = new \Nethgui\Adapter\TabularValueAdapter($innerAdapter, '/');
    }

    public function testCount()
    {
        $this->assertEquals(3, $this->object->count());
    }

    public function testDelete()
    {
        $this->object->delete();
        $this->assertEquals(0, $this->object->count());
    }

    public function testGet()
    {
        $compareMatrix = array(
            '1A' => array(),
            '2A' => array(),
            '3A' => array(),
        );

        foreach ($this->object->get() as $key => $row) {
            $this->assertEquals($row, $compareMatrix[$key]);
        }
    }

    public function testSet()
    {
        $compareMatrix = array(
            array('1A'),
            array('2A'),
        );

        $this->object->set($compareMatrix);

        $i = 0;

        foreach ($this->object as $row) {
            $this->assertEquals($row, $compareMatrix[$i ++]);
        }
    }

    public function testSaveModified()
    {
        $this->object['3A'] = array('0', '0');

        $this->serializer->expects($this->once())
            ->method('write')
            ->with($this->equalTo('1A,2A,3A/0/0'));

        $this->object->save();
    }

    public function testSaveNotModified()
    {
        $this->serializer->expects($this->never())
            ->method('write');
        $this->object->save();
    }

    public function testGetIterator()
    {
        $it = $this->object->getIterator();
        $this->assertEquals(3, $it->count());
    }

    public function testIsModified1()
    {
        $this->assertFalse($this->object->isModified());
        $this->object[2] = array('0', '0', '0');
        $this->assertTrue($this->object->isModified());
    }

    public function testIsModified2()
    {
        $this->testSaveModified();
        $this->assertFalse($this->object->isModified());
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->object->offsetExists('1A'));
        $this->assertTrue($this->object->offsetExists('2A'));
        $this->assertTrue($this->object->offsetExists('3A'));
        $this->assertFalse($this->object->offsetExists('XX'));
        $this->assertFalse($this->object->offsetExists(-1));
        $this->assertFalse($this->object->offsetExists(''));
    }

    public function testOffsetGet()
    {
        $this->assertTrue(is_array($this->object->offsetGet('1A')));
        $this->assertTrue(is_array($this->object->offsetGet('2A')));
        $this->assertTrue(is_array($this->object->offsetGet('3A')));
    }

    public function testOffsetSet()
    {
        $this->object->offsetSet('4A', array());
        $this->assertEquals(array(), $this->object['4A']);
    }

}
