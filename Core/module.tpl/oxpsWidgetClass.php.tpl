<?php
[{$oModule->renderFileComment()}]

[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]

use \OxidEsales\Eshop\Application\Component\Widget\WidgetController;

/**
 * Class [{$sClassNamePrefix}][{$sClassName}].
 */
class [{$sClassNamePrefix}][{$sClassName}] extends WidgetController
{

    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
[{if $oModule->renderTasks()}]
     *
     * @TODO: Enter components You need for the widget to be loaded. As example ... array('oxcmp_cur' => 1, ...);
[{/if}]
     *
     * @var array
     */
    protected $_aComponentNames = array([{if $oModule->renderSamples()}]/*'oxcmp_shop' => 1, 'oxcmp_basket' => 1, 'oxcmp_user' => 1*/[{/if}]);

    /**
     * Widget template name.
     *
     * @var string
     */
    protected $_sThisTemplate = '[{$oModule->getModuleId()}][{$sClassName}].tpl';


[{if $oModule->renderTasks()}]
    // TODO: Implement public methods to use in the template to get data. Also You can overload render() method here.
    // NOTE: Widget is very close to a controller and works very similar as a controller.
[{/if}]

[{if $oModule->renderSamples()}]
    /**
     * A sample method that could be called in the widget template as "$oView->getData()"
     *
     * @return array
     */
    /* public function getData()
    {
        return array('mySampleData');
    } */
[{/if}]

    /**
     * Returns if view should be cached.
     *
     * @return bool
     */
    public function isCacheable()
    {
[{if $oModule->renderTasks()}]
        // TODO: You can make it false of implement some logic when to cache it and when not.
[{/if}]
        return false;
    }
}
