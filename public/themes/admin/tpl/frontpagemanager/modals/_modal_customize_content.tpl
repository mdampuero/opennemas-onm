<div class="modal hide fade" id="modal-element-customize-content">
  <form id="customize-content">
    <div class="modal-header">
      <button type="button" class="close shadow-close" data-dismiss="modal" aria-hidden="true"> </button>
      <h3>{t}Customize font and color for this element{/t}</h3>
    </div>
    <div class="modal-body form-vertical form-inline-block">
        <div class="control-group background">
                <label for="color" class="control-label">{t}Background Color{/t}</label>
                <input type="text" id="bg-color" name="bg-color" value="{$category->color|default:$smarty.capture.websiteColor|trim}">
                <select name="colorpicker-background">
                    <option value="#ffffff">{t}White{/t}</option>
                    <option value="#e8edfa">{t}Light Blue{/t}</option>
                    <option value="#f1e5e5">{t}Light Red{/t}</option>
                    <option value="#ffe5d1">{t}Light Orange{/t}</option>
                    <option value="#ece5f1">{t}Light purple{/t}</option>
                    <option value="#e3f7e2">{t}Light green{/t}</option>
                    <option value="#fcfbdf">{t}Light yellow{/t}</option>
                    <option value="#7bd148">{t}Green{/t}</option>
                    <option value="#5484ed">{t}Bold blue{/t}</option>
                    <option value="#a4bdfc">{t}Blue{/t}</option>
                    <option value="#46d6db">{t}Turquoise{/t}</option>
                    <option value="#7ae7bf">{t}Light green{/t}</option>
                    <option value="#51b749">{t}Bold green{/t}</option>
                    <option value="#fbd75b">{t}Yellow{/t}</option>
                    <option value="#ffb878">{t}Orange{/t}</option>
                    <option value="#ff887c">{t}Red{/t}</option>
                    <option value="#dc2127">{t}Bold red{/t}</option>
                    <option value="#dbadff">{t}Purple{/t}</option>
                    <option value="#e1e1e1">{t}Gray{/t}</option>
                    <option value="#000000">{t}Black{/t}</option>
                    <option value="#fbd75b">{t}Yellow{/t}</option>
                    <option value="#ffb878">{t}Orange{/t}</option>
                    <option value="#ff887c">{t}Red{/t}</option>
                    <option value="#980101">{t}Bold red{/t}</option>
                </select>
            </div>

            <div class="control-group fontcolor">
                <label for="font-color" class="control-label">{t}Title font color{/t}</label>
                <input type="text" id="font-color" name="font-color" value="">
                <select name="colorpicker-font">
                  <option value="#000000">{t}Black{/t}</option>
                  <option value="#7bd148">{t}Green{/t}</option>
                  <option value="#5484ed">{t}Bold blue{/t}</option>
                  <option value="#a4bdfc">{t}Blue{/t}</option>
                  <option value="#46d6db">{t}Turquoise{/t}</option>
                  <option value="#7ae7bf">{t}Light green{/t}</option>
                  <option value="#51b749">{t}Bold green{/t}</option>
                  <option value="#fbd75b">{t}Yellow{/t}</option>
                  <option value="#ffb878">{t}Orange{/t}</option>
                  <option value="#ff887c">{t}Red{/t}</option>
                  <option value="#dc2127">{t}Bold red{/t}</option>
                  <option value="#dbadff">{t}Purple{/t}</option>
                  <option value="#e1e1e1">{t}Gray{/t}</option>
                  <option value="#ffffff">{t}White{/t}</option>
                  <option value="#7bd148">{t}Green{/t}</option>
                  <option value="#5484ed">{t}Bold blue{/t}</option>
                  <option value="#a4bdfc">{t}Blue{/t}</option>
                  <option value="#46d6db">{t}Turquoise{/t}</option>
                  <option value="#7ae7bf">{t}Light green{/t}</option>
                  <option value="#51b749">{t}Bold green{/t}</option>
                  <option value="#fbd75b">{t}Yellow{/t}</option>
                  <option value="#ffb878">{t}Orange{/t}</option>
                  <option value="#ff887c">{t}Red{/t}</option>
                  <option value="#980101">{t}Bold red{/t}</option>
                </select>
            </div>
            <div class="control-group">
                <label for="font-family" class="control-label">{t}Title font family{/t}</label>
                {assign var='availableFonts' value=','|explode:"Auto,Arial,Verdana,Georgia,Helvetica"}
                <select id="font-family" name="font-family">
                    {html_options values=$availableFonts output=$availableFonts selected=22}

                </select>
            </div>

            <div class="control-group">
                <label for="font-style" class="control-label">{t}Title font style{/t}</label>
                {assign var='availableStyle'  value=','|explode:"Auto,Italic,Oblique,Normal"}
                <select id="font-style" name="font-style">
                    {html_options values=$availableStyle output=$availableStyle selected=Auto}
                </select>
            </div>

            <div class="control-group">
                <label for="font-style" class="control-label">{t}Title font weight{/t}</label>
                {assign var='availableStyle'  value=','|explode:"Auto,bolder,bold,lighter,Normal"}
                <select id="font-weight" name="font-weight">
                    {html_options values=$availableStyle output=$availableStyle selected=Auto}
                </select>
            </div>

            <div class="control-group">
                <label for="font-size" class="control-label">{t}Title font size{/t}</label>
                {assign var='availableSizes'  value=','|explode:"12,14,16,18,20,22,24,26,28,30,32,36,40,48,72"}
                <select id="font-size" name="font-size">
                   <option value="">Auto</option>
                    {html_options values=$availableSizes output=$availableSizes selected=Auto}
                </select>
            </div>
            <hr>
    </div>

    <div class="modal-body form-vertical form-inline-block">
      <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Auto Disposition{/t}<input name="imageDisposition" value="auto" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button0.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Justify Disposition{/t}<input name="imageDisposition" value="justifyTop" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button6.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Right top Disposition{/t}<input name="imageDisposition" value="rightTop" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button1.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Left top Disposition{/t}<input name="imageDisposition" value="leftTop" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button3.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label for="font-size" class="control-label">
              {t}JustifyUnder Disposition{/t}<input name="imageDisposition" value="justifyUnder" type="radio"></label>
            <img class="disposition" src="{$params.IMAGE_DIR}button5.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Right Under Disposition{/t}<input name="imageDisposition" value="rightUnder" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button4.png" alt="{t}righttop{/t}" >
        </div>
        <div class="control-group background">
            <label class="disposition" for="font-size" class="control-label">
              {t}Left Under Disposition{/t}<input name="imageDisposition" value="leftUnder" type="radio"></label>
            <img src="{$params.IMAGE_DIR}button2.png" alt="{t}righttop{/t}" >
        </div>
    </div>
    <div class="modal-footer">
        <a class="btn btn-secondary reset" href="#">{t}Reset{/t}</a>
        <a class="btn btn-primary yes" href="#">{t}Save{/t}</a>
        <a class="btn no" href="#">{t}Cancel{/t}</a>
    </div>
  </form>
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

