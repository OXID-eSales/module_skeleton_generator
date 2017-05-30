<?php
[{$oModule->renderFileComment()}]

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                     => 'UTF-8', // Supports german language specific chars like: ä, ö. ß, etc.

    [{if $oModule->renderTasks()}]// TODO: Follow this pattern to add more translation. Delete this comment.
    [{/if}][{if $oModule->renderSamples()}]//'[{$oModule->getVendorPrefix(true)}]_[{$oModule->getModuleFolderName(true)}]_[KEY]' => '[TR - Value]',
[{/if}]);
