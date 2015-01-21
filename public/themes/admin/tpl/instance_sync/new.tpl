{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@AdminTheme/js/jquery/jquery_colorpicker/css/colorpicker.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}">
    {/stylesheets}
{/block}

{block name="footer-js" append}
    {javascripts src="@AdminTheme/js/jquery/jquery_colorpicker/js/colorpicker.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

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
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Instance Synchronization{/t} :: {t}Adding site{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_instance_sync}" title="{t}Go back to list{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="grid simple ">
            <div class="grid-body">
                <div class="form-group">
                    <label for="site_url" class="form-label">{t}Site URL{/t}</label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="text" required="required" name="site_url" id="site_url" value="{$site_url}" placeholder="http://example.com" class="form-control">
                            <span class="input-group-addon primary">
                                <span class="arrow"></span>
                                <i class="fa fa-plug"></i>
                                <input class="btn btn-link" type="button" name="connect" value="{t}Connect{/t}" id="connect"> </input>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="colorDiv" {if !$site_color}style="display:none;"{/if}>
                    <label for="site_color" class="form-label">{t}Site color{/t}</label>
                    <div class="controls">
                        <input readonly="readonly" type="text" class="colorpicker_input" id="color-picker" name="site_color" value="{$site_color}" required="required">
                        <div class="colorpicker_viewer" style="background-color:#{$site_color}"></div>
                    </div>
                </div>
                <div class="form-group" id="categories">
                    <label for="site_color" class="form-label">{t}Available categories for sync{/t}</label>
                    <div class="controls">
                        {$output}
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
{/block}
