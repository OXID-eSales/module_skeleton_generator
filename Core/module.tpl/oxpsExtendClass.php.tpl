<?php
[{$oModule->renderFileComment()}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{assign var='sVendorPrefix' value=$oModule->getVendorPrefix()|ucfirst}]
[{assign var='sModuleName' value=$oModule->getModuleFolderName()}]
[{assign var='sNamespaceSuffix' value=$oModule->getNamespaceSuffixFromPath($sFilePath)}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{if $v6Namespace}]
    [{assign var='sv6Namespace' value=$v6Namespace}]
[{/if}]
[{assign var='sReadableClassName' value=$sClassName|capitalize}]

namespace [{$sVendorPrefix}]\[{$sModuleName}]\[{$sNamespaceSuffix}];

/**
 * Class [{$sReadableClassName}].
 * Extends \[{$sv6Namespace}]\[{$sClassName}].
 *
 * @mixin \[{$sv6Namespace}]\[{$sClassName}]
 */
class [{$sReadableClassName}] extends [{$sReadableClassName}]_parent
{
[{if $oModule->renderTasks()}]
    // TODO: Overload parent class methods or implement new methods for the extended class.
[{/if}]

[{if $oModule->renderSamples()}]
    // An example of an overloaded method which already exists in parent class
    /* public function [myMethod]($[sSomeParameter] = '')
    {
[{if $oModule->renderTasks()}]
        // TODO: Implement Your custom logic here or delete this sample
        // NOTE: Overloaded method parameters must be same as in original method.
        // NOTE: Overloaded method must always call parent and do a return similar as in original method
[{/if}]

        return $this->_[{$sReadableClassName}]_[myMethod]_parent($[sSomeParameter]);
    } */


    /**
     * Parent `[myMethod]` call.
[{if $oModule->renderTasks()}]
     * Method required for mocking.
[{/if}]
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    /* protected function _[{$sReadableClassName}]_[myMethod]_parent($[sSomeParameter] = '')
    {
        return parent::[myMethod]($[sSomeParameter]);
    } */
[{/if}]
}
