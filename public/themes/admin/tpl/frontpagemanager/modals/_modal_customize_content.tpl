<div class="modal hide fade" id="modal-element-customize-content">
    <div class="modal-header">
      <button type="button" class="close shadow-close" data-dismiss="modal" aria-hidden="true"> </button>
      <h3><span id="content-title"></span> </h3>
    </div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">{t}Customize font and color style{/t}</a></li>
        <li class="image-disposition"><a href="#tab2" data-toggle="tab">{t}Image disposition in frontpage{/t}</a></li>
    </ul>
    <div class="modal-body ">
    <form id="customize-content" class="form-horizontal tab-content">
        <div class="tab-pane active" id="tab1">
            <div class="control-group background">
                <label for="bg-color" class="control-label">{t}Background Color{/t}</label>
                <div class="controls">
                    <input type="text" id="bg-color" name="bg-color" value="">
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
            </div>

            <div class="control-group fontcolor">
                <label for="font-color" class="control-label">{t}Title font color{/t}</label>
                <div class="controls">
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
            </div>
            <div class="control-group">
                <label for="font-family" class="control-label">{t}Title font family{/t}</label>
                <div class="controls">
                  {assign var='availableFonts' value=','|explode:"Auto,Arial,Verdana,Georgia,Helvetica"}
                  <select id="font-family" name="font-family">
                      {html_options values=$availableFonts output=$availableFonts selected=22}
                  </select>
                </div>
            </div>

            <div class="control-group">
                <label for="font-style" class="control-label">{t}Title font style{/t}</label>
                <div class="controls">
                  {assign var='availableStyle'  value=','|explode:"Auto,Italic,Oblique,Normal"}
                  <select id="font-style" name="font-style">
                      {html_options values=$availableStyle output=$availableStyle selected=Auto}
                  </select>
              </div>
            </div>

            <div class="control-group">
                <label for="font-weight" class="control-label">{t}Title font weight{/t}</label>
                <div class="controls">
                  {assign var='availableStyle'  value=','|explode:"Auto,bolder,bold,lighter,Normal"}
                  <select id="font-weight" name="font-weight">
                      {html_options values=$availableStyle output=$availableStyle selected=Auto}
                  </select>
                </div>
            </div>

            <div class="control-group">
                <label for="font-size" class="control-label">{t}Title font size{/t}</label>
                <div class="controls">
                  {assign var='availableSizes'  value=','|explode:"12,14,16,18,20,22,24,26,28,30,32,36,40,44,48,54,60,66,72"}
                  <select id="font-size" name="font-size">
                     <option value="">Auto</option>
                      {html_options values=$availableSizes output=$availableSizes selected=Auto}
                  </select>
                </div>
            </div>
        </div>

        <div class="tab-pane form-inline-block select-disposition" id="tab2">
            <div class="control-group disposition">
                <label class="radio">
                    {t}Top Wide Image{/t}<input name="imageDisposition" value="image-top-wide" type="radio">
                    <img class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition1.png" name="image-top-wide" alt="{t}Image wide{/t}" >
                </label>
            </div>
            <div class="control-group disposition">
                <label class="radio">
                    {t}Top right Image{/t}<input name="imageDisposition" value="image-top-right" type="radio">
                    <img  class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition3.png" name="image-top-right" alt="{t}image-top-right{/t}" >
                </label>
            </div>
            <div class="control-group disposition">
                <label class="radio">
                    {t}Top Left Image{/t}<input name="imageDisposition" value="image-top-left" type="radio">
                    <img class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition2.png" name="image-top-left" alt="{t}image-top-left{/t}" >
                </label>
            </div>
            <div class="control-group disposition">
                <label class="radio">
                    {t}Wide image{/t}<input name="imageDisposition" value="image-middle-wide" type="radio">
                    <img class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition4.png" name="image-middle-wide" alt="{t}image-middle-wide{/t}" >
                </label>
            </div>
            <div class="control-group disposition">
                <label class="radio">
                    {t}Right image{/t}<input name="imageDisposition" value="image-middle-left" type="radio">
                    <img class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition6.png" name="image-middle-left" alt="{t}image-middle-left{/t}">
                </label>
            </div>
            <div class="control-group disposition">
                <label class="radio">
                    {t}Left image{/t}<input name="imageDisposition" value="image-middle-left" type="radio">
                    <img class="image-disposition" src="{$params.IMAGE_DIR}dispositions/disposition5.png" name="image-middle-left" alt="{t}image-middle-left{/t}" >
                </label>
            </div>
        </div>
        </form>
      </div>
      <div class="modal-footer">
          <a class="btn btn-secondary reset" href="#">{t}Reset{/t}</a>
          <a class="btn btn-primary yes" href="#">{t}Save{/t}</a>
          <a class="btn no" href="#">{t}Cancel{/t}</a>
      </div>
</div>

{stylesheets src="@AdminTheme/js/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.css" filters="cssrewrite"}
    <link rel="stylesheet" href="{$asset_url}">
{/stylesheets}

{javascripts src="@AdminTheme/js/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.js"}
    <script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}

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


<style>
  #modal-element-customize-content  {
    position:fixed;
  }
  #modal-element-customize-content .form-horizontal .control-group {
    margin-bottom: 10px;
  }
  #modal-element-customize-content div.modal-header {
    font-size: 11px;
    max-height: 45px;
    overflow: hidden;
 }
</style>
