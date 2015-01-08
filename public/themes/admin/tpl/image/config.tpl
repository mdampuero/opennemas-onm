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
    input[type="number"],
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
<form action="{url name=admin_images_config}" method="POST" name="formulario" id="formulario" {$formAttrs}>

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Images{/t} :: {t}Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <button type="submit">
                    <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                    {t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_images_statistics}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="form-horizontal panel">
        <div class="control-group">
            <label for="" class="control-label">{t}Main image thumbnails{/t}</label>
            <div class="controls">
                <div class="form-inline-block">
                    <div class="control-group">
                        <label for="image_thumb_size[width]" class="control-label">{t}Width{/t}</label>
                        <div class="controls">
                            <input type="number" name="image_thumb_size[width]" value="{$configs['image_thumb_size']['width']|default:"140"}" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="image_thumb_size[height]" class="control-label">{t}Height{/t}</label>
                        <div class="controls">
                            <input type="number" class="required" name="image_thumb_size[height]" value="{$configs['image_thumb_size']['height']|default:"100"}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label for="" class="control-group">{t}Frontpage image thumbnails{/t}</label>
            <div class="controls">
                <div class="form-inline-block">
                    <div class="control-group">
                        <label for="image_front_thumb_size[width]" class="control-label">{t}Width:{/t}</label>
                        <div class="controls">
                            <input type="number" name="image_front_thumb_size[width]" value="{$configs['image_front_thumb_size']['width']|default:"350"}" required/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="image_front_thumb_size[height]" class="control-label">{t}Height:{/t}</label>
                        <div class="controls">
                            <input type="number" name="image_front_thumb_size[height]" value="{$configs['image_front_thumb_size']['height']|default:"250"}" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label for="" class="control-label">{t}Inner article image thumbnails{/t}</label>
            <div class="controls">
                <div class="form-inline-block">
                    <div class="control-group">
                        <label for="image_inner_thumb_size[width]" class="control-label">{t}Width:{/t}</label>
                        <div class="controls">
                            <input type="number" name="image_inner_thumb_size[width]" value="{$configs['image_inner_thumb_size']['width']|default:"350"}" required/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="image_inner_thumb_size[height]" class="control-label">{t}Height:{/t}</label>
                        <div class="controls">
                            <input type="number" name="image_inner_thumb_size[height]" value="{$configs['image_inner_thumb_size']['height']|default:"250"}" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="action" name="action" value="config" />
</div>
</form>
{/block}
