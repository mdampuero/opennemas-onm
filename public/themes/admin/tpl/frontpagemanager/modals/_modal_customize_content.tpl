
<div class="modal hide fade" id="modal-element-change-bgcolor">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Customize font and color for this element{/t}</h3>
    </div>
    <div class="modal-body">
        <p>

            <div class="controls">
                <label for="color" class="control-label">{t}Background Color{/t}</label>
                <input readonly="readonly" size="6" type="text" id="color" name="color" value="{$category->color|default:$smarty.capture.websiteColor|trim}">
                <div id="colorpicker_viewer" class="colorpicker_viewer" style="background-color:#{$category->color|default:$smarty.capture.websiteColor|trim}"></div>
                <button class="onm-button">{t}Reset color{/t}</button>
            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-family" class="control-label">{t}Title font family{/t}</label>
                {assign var='availableFonts'  value=','|explode:"Arial, Verdana, Georgia, Helvetica"}
                <select>
                    {html_options values=$availableFonts options=$availableFonts selected=22}
                </select>
            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-style" class="control-label">{t}Title font family{/t}</label>
                {assign var='availableStyle'  value=','|explode:" italic, bold, normal "}
                <select>
                    {html_options values=$availableStyle options=$availableStyle selected=22}
                </select>
            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-size" class="control-label">{t}Title font size{/t}</label>
                {assign var='availableSizes'  value=','|explode:"12,14,16,18,20,22,24,26,28,30"}
                <select id="font-size" name="font-size">
                    {html_options values=$availableSizes options=$availableSizes selected=22}
                </select>

            </div>
        </p>
         <p>
            <div class="controls">
                <label for="font-size" class="control-label">{t}Title font color{/t}</label>
                <input readonly="readonly" size="6" type="text" id="fontcolor" name="font-color" value="{$category->fontcolor|default:$smarty.capture.websiteColor|trim}">
                <div id="colorpicker_viewer" class="colorpicker_viewer" style="background-color:#{$category->fontcolor|default:$smarty.capture.websiteColor|trim}"></div>
                <button class="onm-button">{t}Reset color{/t}</button>
            </div>
        </p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Save{/t}</a>
        <a class="btn no" href="#">{t}Keep{/t}</a>
    </div>
</div>



    {css_tag href="/css/colorpicker.css" basepath="/js/jquery/jquery_colorpicker/"}
    {script_tag src="/jquery/jquery_colorpicker/js/colorpicker.js"}
<script>
    jQuery(document).ready(function($) {

        var color = $('.colorpicker_viewer');
        var inpt  = $('#color');
        var btn   = $('.onm-button');

        inpt.ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val(hex);
                $(el).ColorPickerHide();
            },
            onChange: function (hsb, hex, rgb) {
                inpt.val(hex);
                color.css('background-color', '#' + hex);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        })
        .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
        });

        btn.on('click', function(e, ui){
            inpt.val( '#FFF' );
            color.css('background-color', '#FFF');
           var parent = $(this).closest('.content-provider-element');
            e.preventDefault();
        });
    });
</script>

