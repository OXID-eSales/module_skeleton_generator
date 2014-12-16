<?php
[{$oModule->renderFileComment()}]

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                     => 'ISO-8859-15', // Supports DE chars like: ä, ü, ö, etc.

    [{if $oModule->renderTasks()}]// TODO: Follow this pattern to add more translation. Delete this comment.
    [{/if}][{if $oModule->renderSamples()}]//'[{$oModule->getVendorPrefix(true)}]_[{$oModule->getModuleFolderName(true)}]_[KEY]' => '[TR - Value]',
[{/if}]);
