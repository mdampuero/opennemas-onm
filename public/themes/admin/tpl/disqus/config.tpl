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
<form action="{url name=admin_comments_disqus_config}" method="POST">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title">
            <h2 class="disqus">{t}Settings{/t}</h2>
        </div>
        <ul class="old-button">
            <li>
                <button type="submit">
                    <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                    {t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_comments_disqus}" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    {render_messages}
    <div>
         <table class="adminform" border=0>

            <tr>
                <td>
                    <div class="form-wrapper">
                        <div>
                            <label for="server">Disqus Id ({t}Short name{/t}):</label>
                            <input type="text" class="required" name="shortname" value="{$shortname|default:""}" />
                        </div>
                    </div>
                </td>
            </tr>

        </table>
    </div>
</div>
</form>
{/block}
