[{include file="headitem.tpl" title="OXPS_MODULEGENERATOR_ADMIN_TITLE"|oxmultilangassign}]
[{oxscript include="js/libs/jquery.min.js"}]
[{oxscript include="js/libs/jquery-ui.min.js"}]
[{oxscript include=$oViewConf->getModuleUrl('oxps/ModuleGenerator', 'out/src/js/admin_oxpsmodulegenerator.js')}]
[{oxscript add="$.noConflict();" priority=10}]
[{assign var="sModuleNameValidationUrl" value=$oView->generateAjaxResponseUrl('validateModuleName')}]
[{assign var="sExtendClassesNamesValidationUrl" value=$oView->generateAjaxResponseUrl('validateExtendClassNames')}]

[{oxscript add="
            jQuery(document).ready(function () {
                jQuery('#oxpsmodulegenerator').wizard({
                     moduleNameValidationUrl: '`$sModuleNameValidationUrl`',
                     extendClassesValidationUrl: '`$sExtendClassesNamesValidationUrl`',
                });
            });"
           priority=10}]
<script type="application/javascript">
    /**
     * Themes list disabling/enabling event depending on "no themes" checkbox status.
     */
    // TODO: Move is to admin_oxpsmodulegenerator widget as well as any other JS in the tempalte
    function themeListToggle() {
        document.getElementById('theme_list').disabled = document.getElementById('theme_none').checked;
    }
</script>
[{oxstyle include=$oViewConf->getModuleUrl('oxps/ModuleGenerator', 'out/src/css/admin_oxpsmodulegenerator.css')}]
[{oxstyle}]
<div id="oxpsmodulegenerator">
<div class="export">
    <span>[{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_TITLE"}]</span>
</div>
<table class="oxpsmodulegenerator-wrapper" cellspacing="0" cellpadding="0" border="0"
       xmlns="http://www.w3.org/1999/html">
<tr>
    <td valign="top" class="edittext" align="left">
        [{if $sMessage}]
        <div class="[{if $blError}]errorbox[{else}]messagebox[{/if}]">
            <ul>
                <li>[{oxmultilang ident=$sMessage}]</li>
            </ul>
        </div>
        [{/if}]
        <br/>
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="edittext">
                    <form name="modulegenerator_form" id="modulegenerator_form" method="post"
                          action="[{$oViewConf->getSelfLink()}]cl=admin_oxpsmodulegenerator&fnc=generateModule">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME"}]
                                    <span class="req"> *</span>
                                </td>
                                <td class="edittext">
                                    <input type="text" name="modulegenerator_module_name" value="[{$oValues->name}]"
                                           maxlength="20"/>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_NAME_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES"}]
                                </td>
                                <td class="edittext">
                                    <textarea name="modulegenerator_extend_classes" cols="20"
                                              rows="3">[{$oValues->extend}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_OVERRIDE_CLASSES_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS"}]
                                </td>
                                <td class="edittext">
                                    <textarea name="modulegenerator_controllers" cols="20"
                                              rows="1">[{$oValues->controllers}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_CONTROLLERS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS"}]
                                </td>
                                <td class="edittext">
                                    <textarea name="modulegenerator_models" cols="20"
                                              rows="1">[{$oValues->models}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_MODELS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS"}]
                                </td>
                                <td class="edittext">
                                    <textarea name="modulegenerator_lists" cols="20"
                                              rows="1">[{$oValues->lists}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_LISTS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS"}]
                                </td>
                                <td class="edittext">
                                    <textarea name="modulegenerator_widgets" cols="20"
                                              rows="1">[{$oValues->widgets}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_WIDGETS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS"}]
                                </td>
                                <td class="edittext">
                                    <textarea class="wider" name="modulegenerator_blocks" cols="20"
                                              rows="2">[{$oValues->blocks}]</textarea>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_BLOCKS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <td class="edittext edittext-label">
                                [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTINGS"}]
                            </td>
                            <td class="edittext">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>[{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_NAME"}]
                                        </th>
                                        <th>[{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_TYPE"}]
                                        </th>
                                        <th>
                                            [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_SETTING_VALUE"}]
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    [{section name=settings start=0 loop=5}]
                                        [{assign var='i' value=$smarty.section.settings.index}]
                                        [{assign var='aSetting' value=$oValues->settings.$i}]
                                        [{assign var='sType' value=$aSetting.type}]
                                        [{if not $sType}]
                                            [{assign var='sType' value='str'}]
                                        [{/if}]
                                        <tr>
                                            <td>
                                                <input type="text" name="modulegenerator_settings[[{$i}]][name]"
                                                       value="[{$aSetting.name}]" maxlength="12"/>
                                            </td>
                                            <td>
                                                <select name="modulegenerator_settings[[{$i}]][type]">
                                                    [{* todo (nice2have) get possible options as array from view *}]
                                                    <option value="bool"
                                                            [{if $sType eq 'bool'}]selected[{/if}]>Checkbox</option>
                                                    <option value="str"
                                                            [{if $sType eq 'str'}]selected[{/if}]>String</option>
                                                    <option value="num"
                                                            [{if $sType eq 'num'}]selected[{/if}]>Number</option>
                                                    <option value="arr"
                                                            [{if $sType eq 'arr'}]selected[{/if}]>Array</option>
                                                    <option value="aarr"
                                                            [{if $sType eq 'aarr'}]selected[{/if}]>Assoc Array</option>
                                                    <option value="select"
                                                            [{if $sType eq 'select'}]selected[{/if}]>Dropdown</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="modulegenerator_settings[[{$i}]][value]" cols="10"
                                                          rows="1">[{$aSetting.value}]</textarea>
                                            </td>
                                        </tr>
                                        [{/section}]
                                    </tbody>
                                </table>
                                [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CREATE_SETTINGS_HINT"}]
                            </td>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES"}]
                                </td>
                                <td class="edittext">
                                    <input type="checkbox" name="modulegenerator_theme_none" id="theme_none" value="1"
                                           onclick="themeListToggle();"
                                           [{if $oValues->theme_none}]checked="checked"[{/if}]/>&nbsp;
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_NONE"}]<br/>
                                    <br/>
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_OR"}]<br/>
                                    <br/>
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_LIST"}]:<br/>
                                    <textarea name="modulegenerator_theme_list" id="theme_list"
                                              cols="10" rows="4">[{$oValues->theme_list}]</textarea>
                                    <script type="application/javascript">
                                        document.addEventListener('DOMContentLoaded', function(event) {
                                            themeListToggle();
                                        });
                                    </script>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_THEMES_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_INIT_VERSION"}]
                                    <span class="req"> *</span>
                                </td>
                                <td class="edittext">
                                    <input type="text" name="modulegenerator_init_version" value="[{$oValues->version}]"
                                           maxlength="12"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            [{* todo (nice2have) Implement this feature: administration area presets and menu.xml*}]
                            [{*<tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION"}]
                                </td>
                                <td class="edittext">
                                    <input type="checkbox" name="modulegenerator_has_administration" value="1"
                                           disabled="disabled"/>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_HAS_ADMINISTRATION_HINT"}]
                                </td>
                            </tr>*}]
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS"}]
                                </td>
                                <td class="edittext">
                                    <input type="checkbox" name="modulegenerator_fetch_unit_tests" value="1"
                                           [{if $oValues->tests}]checked="checked"[{/if}]/>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_CHECKOUT_UNIT_TESTS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS"}]
                                </td>
                                <td class="edittext">
                                    <input type="checkbox" name="modulegenerator_render_tasks" value="1"
                                           [{if $oValues->tasks}]checked="checked"[{/if}]/>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_RENDER_TASKS_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">
                                    [{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES"}]
                                </td>
                                <td class="edittext">
                                    <input type="checkbox" name="modulegenerator_render_samples" value="1"
                                           [{if $oValues->samples}]checked="checked"[{/if}]/>
                                    [{oxinputhelp ident="OXPS_MODULEGENERATOR_ADMIN_RENDER_SAMPLES_HINT"}]
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="edittext edittext-label">&nbsp;</td>
                                <td class="edittext">
                                    <input type="submit" name="modulegenerator_submit"
                                           value="[{oxmultilang ident="OXPS_MODULEGENERATOR_ADMIN_MODULE_GENERATE"}]"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>
[{include file="bottomitem.tpl"}]