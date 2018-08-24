<div class="modal fade" id="modal-element-customize-content">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          <span class="content-title" id="content-title"></span>
        </h4>
      </div>
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#tab1" data-toggle="tab">{t}Customize font and color style{/t}</a>
        </li>
        {*<li class="image-disposition">
          <a href="#tab2" data-toggle="tab">{t}Image disposition in frontpage{/t}</a>
        </li>*}
      </ul>
      <div class="modal-body no-padding">
        <form id="customize-content" class="tab-content no-margin">
          <div class="no-margin tab-pane active form-horizontal" id="tab1">
            <div class="form-group background">
              <label class="col-sm-3 form-label" for="bg-color">{t}Background Color{/t}</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button type="button" class="btn-background btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-style="{ 'background-color': bg_color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right no-padding pull-right" style="min-width: 150px; overflow: hidden;">
                      <li ng-click="bg_color='#000000'" style="background-color: #000000; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#e1e1e1'" style="background-color: #e1e1e1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#ece5f1'" style="background-color: #ece5f1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#e8edfa'" style="background-color: #e8edfa; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#ffffff'" style="background-color: #ffffff; box-shadow: inset 0 0 0 1px #d1dade; cursor: pointer; float: left; height: 30px; width: 30px;">
                        <span style="background: #980101; display: block; height: 1px; width: 40px; margin-left: -5px; margin-top: 15px; transform: rotate(-45deg)"></span>
                      </li>
                      <li ng-click="bg_color='#980101'" style="background-color: #980101; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#dc2127'" style="background-color: #dc2127; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#ff887c'" style="background-color: #ff887c; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#ffb878'" style="background-color: #ffb878; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#ffe5d1'" style="background-color: #ffe5d1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#fbd75b'" style="background-color: #fbd75b; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#fcfbdf'" style="background-color: #fcfbdf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#e3f7e2'" style="background-color: #e3f7e2; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#7ae7bf'" style="background-color: #7ae7bf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#46d6db'" style="background-color: #46d6db; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#7bd148'" style="background-color: #7bd148; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#51b749'" style="background-color: #51b749; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#5484ed'" style="background-color: #5484ed; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#a4bdfc'" style="background-color: #a4bdfc; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="bg_color='#dbadff'" style="background-color: #dbadff; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                    </ul>
                  </span>
                  <input class="form-control" colorpicker="hex" id="bg-color" name="bg-color" ng-model="bg_color" type="text">
                  <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="bg_color='#ffffff'" type="button">
                      {t}Reset{/t}
                    </button>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group font-color">
              <label class="col-sm-3 form-label" for="font-color">{t}Title font color{/t}</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-font btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-style="{ 'background-color': font_color }">
                      &nbsp;&nbsp;&nbsp;&nbsp;
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right no-padding pull-right" style="min-width: 150px; overflow: hidden;">
                      <li ng-click="font_color='#000000'" style="background-color: #000000; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#e1e1e1'" style="background-color: #e1e1e1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#ece5f1'" style="background-color: #ece5f1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#e8edfa'" style="background-color: #e8edfa; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#ffffff'" style="background-color: #ffffff; box-shadow:  inset 0 0 0 1px #d1dade; cursor: pointer; float: left; height: 30px; width: 30px;">
                        <span style="background: #980101; display: block; height: 1px; width: 40px; margin-left: -5px; margin-top: 15px; transform: rotate(-45deg)"></span>
                      </li>
                      <li ng-click="font_color='#980101'" style="background-color: #980101; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#dc2127'" style="background-color: #dc2127; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#ff887c'" style="background-color: #ff887c; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#ffb878'" style="background-color: #ffb878; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#ffe5d1'" style="background-color: #ffe5d1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#fbd75b'" style="background-color: #fbd75b; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#fcfbdf'" style="background-color: #fcfbdf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#e3f7e2'" style="background-color: #e3f7e2; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#7ae7bf'" style="background-color: #7ae7bf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#46d6db'" style="background-color: #46d6db; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#7bd148'" style="background-color: #7bd148; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#51b749'" style="background-color: #51b749; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#5484ed'" style="background-color: #5484ed; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#a4bdfc'" style="background-color: #a4bdfc; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                      <li ng-click="font_color='#dbadff'" style="background-color: #dbadff; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
                    </ul>
                  </span>
                  <input class="form-control" colorpicker="hex" id="font-color" name="font-color" ng-model="font_color" type="text">
                  <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="font_color='#000000'" type="button">
                      {t}Reset{/t}
                    </button>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 form-label" for="font-family">{t}Title font family{/t}</label>
              <div class="col-sm-9">
                {assign var='availableFonts' value=','|explode:"Auto,Arial,Verdana,Georgia,Helvetica"}
                <select id="font-family" name="font-family">
                  {html_options values=$availableFonts output=$availableFonts selected=22}
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 form-label" for="font-style">{t}Title font style{/t}</label>
              <div class="col-sm-9">
                {assign var='availableStyle'  value=','|explode:"Italic,Oblique,Normal"}
                <select id="font-style" name="font-style">
                  {html_options values=$availableStyle output=$availableStyle selected=Normal}
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 form-label" for="font-weight">{t}Title font weight{/t}</label>
              <div class="col-sm-9">
                {assign var='availableStyle'  value=','|explode:"Auto,bolder,bold,lighter,Normal"}
                <select id="font-weight" name="font-weight">
                  {html_options values=$availableStyle output=$availableStyle selected=Auto}
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 form-label" for="font-size">{t}Title font size{/t}</label>
              <div class="col-sm-9">
                {assign var='availableSizes'  value=','|explode:"12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,44,48,54,60,66,72"}
                <select id="font-size" name="font-size">
                  <option value="">Auto</option>
                  {html_options values=$availableSizes output=$availableSizes selected=Auto}
                </select>
              </div>
            </div>
          </div>
          {*<div class="tab-pane form-inline-block select-disposition" id="tab2">
            <div class="form-group disposition">
              <label class="radio">
                {t}Top Wide Image{/t}<input name="imageDisposition" value="image-top-wide" type="radio">
                <img class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition1.png" name="image-top-wide" alt="{t}Image wide{/t}" >
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Top right Image{/t}<input name="imageDisposition" value="image-top-right" type="radio">
                <img  class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition3.png" name="image-top-right" alt="{t}image-top-right{/t}" >
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Top Left Image{/t}<input name="imageDisposition" value="image-top-left" type="radio">
                <img class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition2.png" name="image-top-left" alt="{t}image-top-left{/t}" >
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Wide image{/t}<input name="imageDisposition" value="image-middle-wide" type="radio">
                <img class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition4.png" name="image-middle-wide" alt="{t}image-middle-wide{/t}" >
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Right image{/t}<input name="imageDisposition" value="image-middle-left" type="radio">
                <img class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition6.png" name="image-middle-left" alt="{t}image-middle-left{/t}">
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Left image{/t}<input name="imageDisposition" value="image-middle-left" type="radio">
                <img class="image-disposition" src="{$_template->getImageDir()}/dispositions/disposition5.png" name="image-middle-left" alt="{t}image-middle-left{/t}" >
              </label>
            </div>
            <div class="form-group disposition">
              <label class="radio">
                {t}Default{/t}<input name="imageDisposition" value="" type="radio">
              </label>
            </div>
          </div>*}
        </form>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary reset" href="#">{t}Reset{/t}</a>
        <a class="btn btn-primary yes" href="#">{t}Save{/t}</a>
        <a class="btn no" href="#">{t}Cancel{/t}</a>
      </div>
    </div>
  </div>
</div>
