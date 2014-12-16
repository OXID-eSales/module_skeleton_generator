<?php
[{$oModule->renderFileComment()}]

[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var="sClassFullName" value=$sClassNamePrefix|cat:$sClassName}]

/**
 * Class [{$sClassFullName}]Test
 * Tests for core class [{$sClassFullName}].
 *
 * @see [{$sClassFullName}]
 */
class [{$sClassFullName}]Test extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var [{$sClassFullName}]
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

[{if $oModule->renderTasks()}]
        //TODO: Add more methods to mock.
[{/if}]
        $this->SUT = $this->getMock('[{$sClassFullName}]', array('__call'));
    }


[{if $oModule->renderTasks()}]
    // TODO: Implement tests here...
    // TODO: Test method naming is: "test[method_name]_[condition_answering_questions_when]_[expected_action_following_word_should]"

[{/if}]
[{if $oModule->renderSamples()}]
    /* public function testMyMethod_thisAndThatConditionMet_doThisAndThatAction()
    {
[{if $oModule->renderTasks()}]
        // General Good Practices:
        //  - Keep test method as small and as simple as possible
        //  - Test one condition per one test
        //  - Write as many tests as neded to test all conditions

        // TODO: For mocking use techniques like this:
        // Create mock object and define itsx expected behavior
        $oMyMock = $this->getMock('[someClass]', array('[someMethod]', '[otherClassMethod]'));
        $oMyMock->expects($this->once()/exactly($i[x])/never()/at($i[x])/atLeastOnce())
                ->method('[otherClassMethod]')
                ->with('[Val1]', '[Val2]' / $this->equalTo($m[Val])/anything()/isTrue/False/Null/Json/Type/InstanceOf...()/contains/arrayHasKey/...)
                ->will($this->returnValue($m[Val])/returnArgument($i[x])/throwException($o[Exc])/returnValueMap($a[Val])/returnSelf/...);

        // You can set mock inside mock
        $oMyClassMock->expects($this->any())->method('myMethod')->with($this->equalTo('argument'))->will(
            $this->returnValue($this->getMock(…))
        );

        // Also create mocks for protected methods access
        $this->getProxyClass('MyClass');
        $this->getMock($this->getProxyClassName('MyClass'), array(…));

        // Mock request, cnfig and session data
        modConfig::setParameter('param', 'value');
        modConfig::getInstance()->setConfigParam('setting', 'value');
        modSession::getInstance()->setVar('key', 'value');

        // Set mocks instead of real objects
        oxTestModules::addModuleObject('MyClass', $oMyClassMock);
        oxRegistry::set('MyClass', $oMyClassMock);

        // Use all the variety of PHPUnit assertions
        $this->assert...

        // Create tearDown() method to clear state, reset test data, etc.
    [{/if}]
        $this->asset[...](..., $this->SUT->myMethod());
    } */

    // An example of data provider used for testing
    /* public function myDataProvider()
    {
        return array(
            array([val1], [val2]),
            array([valA], [valB]),
            ...
        );
    } */

    /**
     * @dataProvider myDataProvider
     */
    /* public function testSomeMethod($mArg, $mExpectedResult)
    {
        $this->assertSame($mExpectedResult, $this->SUT->someMethod($mArg));
    } */
[{/if}]
}
