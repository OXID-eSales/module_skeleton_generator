==Title==
[{$oModule->getTitle()}]

==Author==
[{$oModule->getAuthorData('name')}]

==Prefix==
[{$oModule->getVendorPrefix()}]

==Shop Version==
6.x

==Version==
[{$oModule->getInitialVersion()}]

==Link==
[{$oModule->getAuthorData('link')}]

==Mail==
[{$oModule->getAuthorData('mail')}]

==Description==
[{$oModule->getDescription()}]

==Installation==
Activate the module in administration area.

==Extend==
[{assign var="aExtendClasses" value=$oModule->getClassesToExtend()}]
[{if $aExtendClasses}]
[{foreach from=$aExtendClasses key="sExtendClass" item="mApplicationPath"}]
 * [{$sExtendClass}]
[{/foreach}]
[{else}]

[{/if}]

==Modules==

==Modified original templates==

==Uninstall==
Disable the module in administration area and delete module folder.
