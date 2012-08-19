{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:50%;
    }
    </style>
{/block}

{block name="content"}
<form action="{url name=admin_categories_config}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Category manager{/t} :: {t}Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_categories}" class="admin_add" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="c-{$category}">

            <table class="adminform">
                <tbody>
                    <tr>
                        <td>
                            <div class="form-wrapper">
                                <div>
                                    <label for="section_settings[allowLogo]">{t}Show change headers and color in frontpages:{/t}</label>
                                     <select name="section_settings[allowLogo]" id="section_settings[allowLogo]" class="required">
                                        <option value="0">{t}No{/t}</option>
                                        <option value="1" {if $configs['section_settings']['allowLogo'] eq "1"} selected {/if}>{t}Yes{/t}</option>
                                    </select>
                                </div>
                                <br />

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
                </div><!-- / -->
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
        </div>
   </form>

{/block}
