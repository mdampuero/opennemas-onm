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
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletter :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="{$category}">

            <table class="adminheading">
                 <tr>
                     <th align="left">{t}Information about newsletter receipt{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border=0>
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="newsletter_maillist[name]">{t}Maillist name:{/t}</label>
                                <input type="text" class="required" id="name" name="newsletter_maillist[name]" value="{$configs['newsletter_maillist']['name']|default:""}" />
                            </div>
                            <br />
                             <div>
                                <label for="newsletter_maillist[email]">{t}Maillist email:{/t}</label>
                                <input type="text" class="required" name="newsletter_maillist[email]" value="{$configs['newsletter_maillist']['email']|default:""}" />
                            </div>
                            <br />
                             <div>
                                <label for="newsletter_maillist[link]">{t}Maillist link:{/t}</label>
                                <select name="newsletter_maillist[link]" id="newsletter_maillist[link]" class="required">
                                  <option name="newsletter_maillist[link]" value="inner" {if $configs['newsletter_maillist']['link'] eq 'inner'} selected {/if}>{t}Point to inner{/t}</option>
                                  <option name="newsletter_maillist[link]" value="front" {if $configs['newsletter_maillist']['link'] eq 'front'} selected {/if}>{t}Point to frontpage{/t}</option>
                                </select>
                            </div>
                            <br />    
                            <div>
                                <label for="newsletter_maillist[subscription]">{t}Mail address in form subscription{/t}</label>
                                <input type="text" class="required" id="name" name="newsletter_maillist[subscription]" value="{$configs['newsletter_maillist']['subscription']|default:""}" />
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

        <input type="hidden" id="action" name="action" value="save_config" />
   </form>
</div>
{/block}
