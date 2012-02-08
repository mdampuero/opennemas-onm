{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/mediamanager.css"}
{/block}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:90%;
    }
     .help-block {
        max-width: 300px;
     }
    </style>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Image manager :: Configuration{/t}</h2></div>
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
                     <th align="left">{t}Information about video module settings{/t}</th>
                 </tr>
            </table>

            <table class="adminform" border="0" style="padding:10px;">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <fieldset>
                                <legend>{t}Main image thumbnails{/t}</legend>

                                <label for="image_thumb_size[width]">{t}Width:{/t}</label>
                                <input type="text" class="required" name="image_thumb_size[width]" value="{$configs['image_thumb_size']['width']|default:"140"}" />
                                <br><br>

                                <label for="image_thumb_size[height]">{t}Height:{/t}</label>
                                <input type="text" class="required" name="image_thumb_size[height]" value="{$configs['image_thumb_size']['height']|default:"100"}" />
                            </fieldset>

                            <fieldset>
                                <legend>{t}Inner article image thumbnails{/t}</legend>

                                <label for="image_front_thumb_size[width]">{t}Width:{/t}</label>
                                <input type="text" class="required" name="image_front_thumb_size[width]" value="{$configs['image_front_thumb_size']['width']|default:"350"}" />
                                <br><br>

                                <label for="image_front_thumb_size[height]">{t}Height:{/t}</label>
                                <input type="text" class="required" name="image_front_thumb_size[height]" value="{$configs['image_front_thumb_size']['height']|default:"250"}" />
                            </fieldset>

                            <fieldset>
                                <legend>{t}Inner article image thumbnails{/t}</legend>

                                <label for="image_inner_thumb_size[width]">{t}Width:{/t}</label>
                                <input type="text" class="required" name="image_inner_thumb_size[width]" value="{$configs['image_inner_thumb_size']['width']|default:"480"}" />
                                <br><br>

                                <label for="image_inner_thumb_size[height]">{t}Height:{/t}</label>
                                <input type="text" class="required" name="image_inner_thumb_size[height]" value="{$configs['image_inner_thumb_size']['height']|default:"250"}" />
                            </fieldset>

                        </div>
                    </td>
                    <td valign="top">
                        <div class="help-block">
                                <div class="title"><h4>{t}Settings{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t}From here you can set all the generated image thumbnails sizes.{/t}</li>
                                        <li>{t}All sizes must be in pixels{/t}</li>
                                    </ul>
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div><!-- / -->
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="config" />
   </form>
</div>
{/block}
