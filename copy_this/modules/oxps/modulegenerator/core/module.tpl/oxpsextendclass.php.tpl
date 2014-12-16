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
    // public function [myMethod]($[sSomeParameter] = '')
    // {
    //    /** @var [{$sClassNamePrefix}][{$sReadableClassName}]|[{$sClassName}] $this */
    //
[{if $oModule->renderTasks()}]
    //    // TODO: Implement Your custom logic here or delete this sample
[{/if}]
    //
    //    return $this->_[{$sClassNamePrefix}][{$sReadableClassName}]_[myMethod]_parent($[sSomeParameter]);
    // }


    /**
     * Parent `[myMethod]` call. Method required for mocking.
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
