<?php
[{$oModule->renderFileComment()}]

[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var='sVendorPrefix' value=$oModule->getVendorPrefix()|ucfirst}]
[{assign var='sModuleName' value=$oModule->getModuleFolderName()}]
[{assign var='sNamespaceSuffix' value=$oModule->getNamespaceSuffixFromPath($sFilePath)}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$sNamespaceSuffix}];

/**
 * Class [{$sClassName}]Test
 * Tests for core class [{$sClassName}].
 *
 * @see [{$sClassName}]
 */
class [{$sClassName}]Test extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Subject under the test.
     *
     * @var [{$sClassName}]
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
        $this->SUT = $this->getMock('[{$sClassName}]', array('__call'));
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
        //  - Write as many tests as needed to test all conditions

        // TODO: For mocking use techniques like this:
        // TODO: All generated code below is a set of examples. Use it and/or delete!
        // Create mock object and define its expected behavior
        $myMockObject = $this->getMock('[someClass]', array('[someMethod]', '[otherMethod]'));
        $myMockObject->expects($this->once()/exactly($i)/never()/at($i)/atLeastOnce())
                ->method('[otherClassMethod]')
                ->with('[Val1]', '[Val2]' / $this->equalTo($val])/anything()/isTrue/False/Null/Json/Type/InstanceOf...()/contains/arrayHasKey/...)
                ->will($this->returnValue($val)/returnArgument($i)/throwException($exception)/returnValueMap($array])/returnSelf/...);

        // You can set mock inside mock
        $myMockObject->expects($this->any())->method('myMethod')->with($this->equalTo('argument'))->will(
            $this->returnValue($this->getMock(…))
        );

        // Also create mocks for protected methods access
        $this->getProxyClass('MyClass');
        $this->getMock($this->getProxyClassName('MyClass'), […]);

        // Mock request, config and session data
        $this->setRequestParameter('param', 'value');
        $this->setConfigParam('setting', 'value');
        $this->setSessionParam('key', 'value');

        // Set mocks instead of real objects
        \OxidEsales\Eshop\Core\Registry::set('MyClass', $myMockObject);

        // Use all the variety of PHPUnit assertions
        $this->assert...

        // Use other helpers of OXID Test Case
        $this->setTime/setShopId/setAdminMode/cleanTmpDir/etc..

        // Create tearDown() method to clear state, reset test data, etc.
    [{/if}]

        $this->assetSame('...', $this->SUT->myMethod());
    } */

    // An example of data provider used for testing
    /* public function myDataProvider()
    {
        return [
            ['val1', 'val2'],
            ['valA', 'valB'],
            // ...
        ];
    } */

    /**
     * @dataProvider myDataProvider
     */
    /* public function testSomeMethod($arg, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->SUT->someMethod($arg));
    } */
[{/if}]
}
