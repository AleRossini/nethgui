<?php
namespace Nethgui\Test\Unit\Nethgui\View;

/**
 * @covers \Nethgui\View\View
 */
class ViewGenericTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Nethgui\View\View
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $parentModule = $this->getMockBuilder('Nethgui\Controller\ListComposite')
            ->setConstructorArgs(array('Parent'))
            ->getMock();
               
        $module = $this->getMockBuilder('Nethgui\Controller\AbstractController')
            ->setConstructorArgs(array('TestingModule'))
            ->getMock();

        $parentModule->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue($module));        
        
        $module->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parentModule));

        $translator = $this->getMockBuilder('Nethgui\View\TranslatorInterface')
            ->getMock();
        
        $translator->expects($this->any())
            ->method('translate')->will($this->returnArgument(0));

        $translator->expects($this->any())
            ->method('getLanguageCode')->will($this->returnValue('en'));

        $this->object = new \Nethgui\View\View(0, $module, $translator);
    }

    /**
     * @todo Implement testCopyFrom().
     */
    public function testCopyFrom()
    {
        $data = array('a'=>1, 'b'=>2);
        $this->object->copyFrom($data);
        
        $this->assertEquals($data['a'], $this->object['a']);
        $this->assertEquals($data['b'], $this->object['b']);
    }

    /**
     * @todo Implement testSetTemplate().
     */
    public function testSetGetTemplate()
    {
        $this->object->setTemplate('T');        
        $this->assertEquals($this->object->getTemplate(), 'T');
    }


    public function testSpawnView()
    {
        $module = $this->getMockBuilder('Nethgui\Controller\AbstractController')
            ->setConstructorArgs(array('InnerModule'))
            ->setMethods(array())
            ->getMock();
        
        $module->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('InnerModule'));
        
        $innerView1 = $this->object->spawnView($module, TRUE);
        $innerView2 = $this->object->spawnView($module, 'View2');
        
        $this->assertInstanceOf('Nethgui\View\ViewInterface', $innerView1);
        $this->assertInstanceOf('Nethgui\View\ViewInterface', $innerView2);
        $this->assertEquals($innerView1, $this->object['InnerModule']);
        $this->assertEquals($innerView2, $this->object['View2']);
    }

    /**
     * @todo Implement testGetIterator().
     */
    public function testGetIterator()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetExists().
     */
    public function testOffsetExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetGet().
     */
    public function testOffsetGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetSet().
     */
    public function testOffsetSet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetUnset().
     */
    public function testOffsetUnset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRender().
     */
    public function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testToJson().
     */
    public function testToJson()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testTranslate().
     */
    public function testTranslate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetModule().
     */
    public function testGetModule()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetModulePath().
     */
    public function testGetModulePath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

