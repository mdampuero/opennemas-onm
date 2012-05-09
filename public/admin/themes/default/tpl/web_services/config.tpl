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
                    url: "{$smarty.server.PHP_SELF}?action=connect",
                    data: url,
                    dataType: 'html',
                    success: function(data) {
                        $('div.categories').html(data);
                        $('#colorDiv').css('display', 'inline');
                    }
                });

            });

            jQuery('#color-picker').ColorPicker({
                onSubmit: function(hsb, hex, rgb, el) {
                    jQuery(el).val(hex);
                    jQuery(el).ColorPickerHide();
                },
                onChange: function (hsb, hex, rgb) {
                    jQuery('.colopicker_viewer').css('background-color', '#' + hex);
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
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Client configuration{/t}</h2></div>
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
        <br>

        {if $message}
        <div class="error">
             <ul>
                {foreach from=$message item=msg}
                <li>{$msg}</li>
                {/foreach}
             </ul>
        </div>
        {/if}

        {if (!empty($error))}
        <div class="error">
             {render_error}
        </div>
        {/if}

        <div>

             <table class="adminheading">
                 <tr>
                     <th>{t}Synchronization settings{/t}</th>
                 </tr>
             </table>

             <table class="adminform">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="site_url">{t}Site Url:{/t}</label>
                                <input type="text" class="required" name="site_url" id="site_url" value="" placeholder="http://example.com"/>
                                <input type="button" name="connect" value="{t}Connect{/t}" id="connect" class="onm-button blue">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="colorDiv" style="display: none;">
                            <label for="site_color">{t}Site color:{/t}</label>
                            <input readonly="readonly" type="text" class="colorpicker_input" id="color-picker" name="site_color" value="">
                            <div class="colopicker_viewer" style="background-color:#{$site_color}"></div>
                        </div>
                        <div class="categories">

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
