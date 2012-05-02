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
    .top-action-bar .title > * {
        display: inline-block;
        padding: 0;
    }
    </style>
{/block}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2 class="disqus">{t}Comment manager{/t}:: {t}Configuration{/t}</h2>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="POST">
        <br>

        {render_messages}

        {if (!empty($error))}
        <div class="error">
             {render_error}
        </div>
        {/if}

        <div>

             <table class="adminheading">
                 <tr>
                     <th align="left">{t}Auth credentials{/t}</th>
                 </tr>
             </table>

             <table class="adminform" border=0>

                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="server">{t}Short name:{/t}</label>
                                <input type="text" class="required" name="shortname" value="{$shortname|default:""}" />
                            </div>
                        </div>
                    </td>
                </tr>

            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div>
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="config" />
   </form>
</div>
{/block}
