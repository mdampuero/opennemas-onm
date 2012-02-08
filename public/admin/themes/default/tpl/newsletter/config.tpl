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
        width:400px;
    }
    .form-wrapper {
        margin:10px auto;
        width:70%;
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
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="cat-{$category}">

            <table class="adminheading">
                 <tr>
                     <th>{t}Information about newsletter receipt{/t}</th>
                 </tr>
            </table>

            <table class="adminform">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label>{t}Maillist name:{/t}</label>
                                <input type="text" class="required" name="newsletter_maillist[name]" value="{$configs['newsletter_maillist']['name']|default:""}" />
                            </div>
                            <br />
                             <div>
                                <label>{t}Maillist email:{/t}</label>
                                <input type="text" class="required" name="newsletter_maillist[email]" value="{$configs['newsletter_maillist']['email']|default:""}" />
                            </div>
                            <br />
                             <div>
                                <label>{t}Maillist link:{/t}</label>
                                <select name="newsletter_maillist[link]" id="newsletter_maillist[link]" class="required">
                                  <option value="inner" {if $configs['newsletter_maillist']['link'] eq 'inner'} selected {/if}>{t}Point to inner{/t}</option>
                                  <option value="front" {if $configs['newsletter_maillist']['link'] eq 'front'} selected {/if}>{t}Point to frontpage{/t}</option>
                                </select>
                            </div>
                            <br />    
                            <div>
                                <label>{t}Newsletter subscription type{/t}</label>
                                <input type="radio" class="required" name="newsletter_subscriptionType" value="submit" {if $configs['newsletter_subscriptionType'] eq 'submit'}checked="checked"{/if} />
                                {t}Manage newsletter by e-mail{/t}
                                <input type="radio" class="required" name="newsletter_subscriptionType" value="create_subscriptor" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'}checked="checked"{/if} />
                                {t}Manage newsletter by subscriptors table{/t}
                            </div>
                            <br />   
                            <div>
                                <label>{t}Mail address in form subscription{/t}</label>
                                <input type="text" class="required" name="newsletter_maillist[subscription]" value="{$configs['newsletter_maillist']['subscription']|default:""}" />
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
   </div>
</form>
{/block}
