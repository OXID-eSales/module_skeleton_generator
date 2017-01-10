<?php
[{$oModule->renderFileComment()}]

[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]

use \OxidEsales\Eshop\Core\Controller\BaseController;

/**
 * Class [{$sClassNamePrefix}][{$sClassName}].
 */
class [{$sClassNamePrefix}][{$sClassName}] extends BaseController
{

    /**
     * Controller template name.
     *
     * @var string
     */
    protected $_sThisTemplate = '[{$oModule->getModuleId()}][{$sClassName|lower}].tpl';


    [{if $oModule->renderSamples()}]// This is an example of overridden init method that goes before any action and render.
    /* public function init()
    {
[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here or delete this sample
[{/if}]
        //$this->_[{$sClassNamePrefix}][{$sClassName}]_init_parent();
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
        $mReturn = $this->_[{$sClassNamePrefix}][{$sClassName}]_render_parent();

[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here
[{/if}]
[{if $oModule->renderSamples()}]
        // $this->setViewData(array('[mMyParam]' => ...)); // To set ir modify view data

        // ... = $this->getConfig()->getRequestParameter('[request_parameter]'); // To get GET/POST parameters

        // $oModule = oxRegistry::get('[{$sClassNamePrefix}]Module'); // Get the module instance

        // /** @var myObject|myObjectParent $oObject */
        // $oObject = oxNew('myObject'); // To create new object

        // oxRegistry::getUtils()->redirect(...); // For redirect

        // $this->_oaComponents['oxcmp_..']->...; // To access components

        // $this->getUser(); // To get active shop user

        // oxRegistry::get("oxUtilsView")->addErrorToDisplay(...); // To set error
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
     * Parent `render` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _[{$sClassNamePrefix}][{$sClassName}]_render_parent()
    {
        return parent::render();
    }
}
