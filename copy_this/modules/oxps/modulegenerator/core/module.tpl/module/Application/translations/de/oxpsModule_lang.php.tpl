<?php
[{$oModule->renderFileComment()}]

$sLangName = 'Deutsch';

$aLang = array(
    'charset' => 'UTF-8',
[{if $oModule->renderTasks()}]

    // TODO: Follow this pattern to add more translation. Delete this comment.
[{/if}]
[{if $oModule->renderSamples()}]
    //'[{$oModule->getVendorPrefix(true)}]_[{$oModule->getModuleFolderName(true)}]_[KEY]' => '[TR - Value]',
[{/if}]
);
