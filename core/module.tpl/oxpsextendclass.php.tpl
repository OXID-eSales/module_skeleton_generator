<?php
[{$oModule->renderFileComment()}]
[{assign var='sClassNamePrefix' value=$oModule->getModuleClassName()}]
[{if $sClassRealName}]
    [{assign var='sClassName' value=$sClassRealName}]
[{else}]
    [{assign var='sClassName' value=$oModule->getFileNameSuffix($sFilePath)}]
[{/if}]
[{assign var='sReadableClassName' value=$sClassName|capitalize}]

/**
 * Class [{$sClassNamePrefix}][{$sReadableClassName}].
 * Extends [{$sClassName}].
 *
 * @see [{$sClassName}]
 */
class [{$sClassNamePrefix}][{$sReadableClassName}] extends [{$sClassNamePrefix}][{$sReadableClassName}]_parent
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

        return $this->_[{$sClassNamePrefix}][{$sReadableClassName}]_[myMethod]_parent($[sSomeParameter]);
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
    /* protected function _[{$sClassNamePrefix}][{$sReadableClassName}]_[myMethod]_parent($[sSomeParameter] = '')
    {
        return parent::[myMethod]($[sSomeParameter]);
    } */
[{/if}]
}
