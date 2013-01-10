
<div class="modal hide fade" id="modal-element-customize-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Customize font and color for this element{/t}</h3>
    </div>
    <div class="modal-body">
        <p>

            <div class="controls">
                <label for="color" class="control-label">{t}Background Color{/t}</label>
                <input type="text" id="bg-color" name="bg-color" value="{$category->color|default:$smarty.capture.websiteColor|trim}">
                <select name="colorpicker-background">
                    <option value="#ffffff">White</option>
                    <option value="#e8edfa">Light Blue</option>
                    <option value="#f1e5e5">Light Red</option>
                    <option value="#ffe5d1">Light Orange</option>
                    <option value="#ece5f1">Light purple</option>
                    <option value="#e3f7e2">Light green</option>
                    <option value="#fcfbdf">Light yellow</option>
                    <option value="#7bd148">Green</option>
                    <option value="#5484ed">Bold blue</option>
                    <option value="#a4bdfc">Blue</option>
                    <option value="#46d6db">Turquoise</option>
                    <option value="#7ae7bf">Light green</option>
                    <option value="#51b749">Bold green</option>
                    <option value="#fbd75b">Yellow</option>
                    <option value="#ffb878">Orange</option>
                    <option value="#ff887c">Red</option>
                    <option value="#dc2127">Bold red</option>
                    <option value="#dbadff">Purple</option>
                    <option value="#e1e1e1">Gray</option>
                    <option value="#000000">Black</option>
                    <option value="#fbd75b">Yellow</option>
                    <option value="#ffb878">Orange</option>
                    <option value="#ff887c">Red</option>
                    <option value="#dc2127">Bold red</option>
                </select>

            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-family" class="control-label">{t}Title font family{/t}</label>
                {assign var='availableFonts' value=','|explode:"Auto,Arial,Verdana,Georgia,Helvetica"}
                <select id="font-family" name="font-family">
                    {html_options values=$availableFonts output=$availableFonts selected=22}

                </select>
            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-style" class="control-label">{t}Title font style{/t}</label>
                {assign var='availableStyle'  value=','|explode:"Auto,Italic,Bold,Normal"}
                <select id="font-style" name="font-style">
                    {html_options values=$availableStyle output=$availableStyle selected=Auto}
                </select>
            </div>
        </p>
        <p>
            <div class="controls">
                <label for="font-size" class="control-label">{t}Title font size{/t}</label>
                {assign var='availableSizes'  value=','|explode:"12,14,16,18,20,22,24,26,28,30"}
                <select id="font-size" name="font-size">
                   <option value="">auto</option>
                    {html_options values=$availableSizes output=$availableSizes selected=auto}
                </select>

            </div>
            <div class="controls">
                <label for="font-color" class="control-label">{t}Title font color{/t}</label>
                <input type="text" id="font-color" name="font-color" value="">
                <select name="colorpicker-font">
                  <option value="#000000">Black</option>
                  <option value="#7bd148">Green</option>
                  <option value="#5484ed">Bold blue</option>
                  <option value="#a4bdfc">Blue</option>
                  <option value="#46d6db">Turquoise</option>
                  <option value="#7ae7bf">Light green</option>
                  <option value="#51b749">Bold green</option>
                  <option value="#fbd75b">Yellow</option>
                  <option value="#ffb878">Orange</option>
                  <option value="#ff887c">Red</option>
                  <option value="#dc2127">Bold red</option>
                  <option value="#dbadff">Purple</option>
                  <option value="#e1e1e1">Gray</option>
                  <option value="#000000">Black</option>
                  <option value="#7bd148">Green</option>
                  <option value="#5484ed">Bold blue</option>
                  <option value="#a4bdfc">Blue</option>
                  <option value="#46d6db">Turquoise</option>
                  <option value="#7ae7bf">Light green</option>
                  <option value="#51b749">Bold green</option>
                  <option value="#fbd75b">Yellow</option>
                  <option value="#ffb878">Orange</option>
                  <option value="#ff887c">Red</option>
                  <option value="#dc2127">Bold red</option>
                </select>
            </div>
        </p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Save{/t}</a>
        <a class="btn no" href="#">{t}Cancel{/t}</a>
    </div>
</div>

    {css_tag href="jquery.simplecolorpicker.css" basepath="js/jquery/jquery_simplecolorpicker/"}
    {script_tag src="/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.js"}

<script>

    $('#modal-element-customize-content').on('show', function (e) {
        $('select[name="colorpicker-background"]').simplecolorpicker({
            picker: true
        }).change(function() {
            $('#bg-color').val($('select[name="colorpicker-background"]').val());
        });
        $('select[name="colorpicker-font"]').simplecolorpicker({
           picker: true
        }).change(function() {
            $('#font-color').val($('select[name="colorpicker-font"]').val());

        });
    });
</script>

