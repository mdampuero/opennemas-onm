{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    <style type="text/css">
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:50%;
    }
    .categories {
        width: 33%;
        margin-left: 10px;
    }
    </style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('#color-picker').ColorPicker({
                onSubmit: function(hsb, hex, rgb, el) {
                    jQuery(el).val(hex);
                    jQuery(el).ColorPickerHide();
                },
                onChange: function (hsb, hex, rgb) {
                    jQuery('.colorpicker_viewer').css('background-color', '#' + hex);
                },
                onBeforeShow: function () {
                    jQuery(this).ColorPickerSetColor(this.value);
                }
            }).bind('keyup', function(){
                jQuery(this).ColorPickerSetColor(this.value);
            });

        });

    </script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Edit Site Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                <img src="{$params.IMAGE_DIR}previous.png" title="{t}Clients list{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario">

        <div>

             <table class="adminform">
                <tr>
                    <td>
                        <div id="colorDiv">
                            <label for="site_color">{t}Site color:{/t}</label>
                            <input readonly="readonly" type="text" class="colorpicker_input" id="color-picker" name="site_color" value="{$site_color}">
                            <div class="colorpicker_viewer" style="background-color:#{$site_color}"></div>
                        </div>
                        <div class="categories">
                            {$output}
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
        <input type="hidden" id="site_url" name="site_url" value="{$site_url}" />
   </form>
</div>
{/block}
