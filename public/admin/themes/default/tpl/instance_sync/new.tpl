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
    </style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#connect').on('click',function(e){
                e.preventDefault();
                var url = $("#site_url").serialize();

                $.ajax({
                    type: 'POST',
                    url: "{url name=admin_instance_sync_fetch_categories}",
                    data: url,
                    dataType: 'html',
                    success: function(data) {
                        $('#categories .controls').html(data).show();

                        $('#colorDiv').show();
                    }
                });

            });

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
<form action="{url name=admin_instance_sync_create}" method="POST" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Sync Manager{/t} :: {t}Client configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{url name=admin_instance_sync}" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Clients list{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <div class="panel">
            <div class="form-horizontal">
                <div class="control-group">
                    <label for="site_url" class="control-label">{t}Site URL{/t}</label>
                    <div class="controls">
                        <input type="text" required="required" name="site_url" id="site_url" value="{$site_url}" placeholder="http://example.com"/>
                        <input type="button" name="connect" value="{t}Connect{/t}" id="connect" class="onm-button blue">
                    </div>
                </div>
                <div class="control-group" id="colorDiv" {if !$site_color}style="display:none;"{/if}>
                    <label for="site_color" class="control-label">{t}Site color{/t}</label>
                    <div class="controls">
                        <input readonly="readonly" type="text" class="colorpicker_input" id="color-picker" name="site_color" value="{$site_color}" required="required">
                        <div class="colorpicker_viewer" style="background-color:#{$site_color}"></div>
                    </div>
                </div>
                <div class="control-group" id="categories">
                    <label for="site_color" class="control-label">{t}Available categories for sync{/t}</label>
                    <div class="controls">
                        {$output}
                    </div>
                </div>
            </div>

            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div>
            </div>

        </div>
    </div>
</form>
{/block}
