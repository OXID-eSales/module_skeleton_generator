<?php
[{$oModule->renderFileComment()}]
[{assign var='sVendorPrefix' value=$oModule->getVendorPrefix()|ucfirst}]
[{assign var='sModuleName' value=$oModule->getModuleFolderName()}]
[{assign var='sNamespaceSuffix' value=$oModule->getNamespaceSuffixFromPath($sFilePath)}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$sNamespaceSuffix}];

use \OxidEsales\Eshop\Core\Controller\BaseController;
use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\Request;
[{if $oModule->renderTasks()}]
// TODO: Define other classes to use in the controller, and delete not used.
[{/if}]

/**
 * Class [{$sClassName}].
 */
class [{$sClassName}] extends BaseController
{

    /**
     * Controller template name.
     *
     * @var string
     */
    protected $_sThisTemplate = '[{$oModule->getModuleId()}][{$sClassName}].tpl';


    [{if $oModule->renderSamples()}]// This is an example of overridden init method which fires before any action and render.
    /* public function init()
    {
[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here or delete this sample
[{/if}]
        //$this->_[{$sClassName}]_init_parent();
    } */
[{/if}]

    /**
     * Overridden parent method.
[{if $oModule->renderTasks()}]
     * TODO: Write a comment on what You will implement here.
[{/if}]
     *
     * @return mixed
     */
    public function render()
    {
        $mReturn = $this->_[{$sClassName}]_render_parent();

[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here
[{/if}]
[{if $oModule->renderSamples()}]
        // $this->setViewData(array('[mMyParam]' => ...)); // To set view data
        // $this->addTplParam($sParam, $sValue); // To add a parameter to view data

        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        // $oRequest = Registry::get(Request::class);
        // ... = $oRequest->getRequestParameter('[request_parameter]'); // To get GET/POST parameters

        // $oModule = \OxidEsales\Eshop\Core\Registry::get(\[{$sVendorPrefix}]\[{$sModuleName}]\Core\[{$sModuleName}]Module::class); // Get the module instance

        // /** @var myObject|myObjectParent $oObject */
        // $oObject = oxNew(myObject::class); // To create new object

        // Registry::getUtils()->redirect(...); // For redirect

        // $this->_oaComponents['oxcmp_..']->...; // To access components

        // $this->getUser(); // To get active shop user

        // Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->addErrorToDisplay(...); // To set error
[{/if}]

        return $mReturn;
    }

    [{if $oModule->renderSamples()}]// This is an example of an action (called with '...?...&fnc=[myAction]'. It is triggered before render.
    /* public function [myAction]()
    {
[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here or delete this sample
[{/if}]
    } */
[{/if}]

[{if $oModule->renderSamples()}]
    // An example of getter that could be used in template as $oView->[getMyData]()
    /* public function [getMyData]()
    {
[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here or delete this sample
        //return ...;
[{/if}]
    } */
[{/if}]

    /**
     * Parent `render` call.
[{if $oModule->renderTasks()}]
     * Method required for mocking.
[{/if}]
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _[{$sClassName}]_render_parent()
    {
        return parent::render();
    }
}
