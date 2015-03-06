<div class="grid simple">
  <div class="grid-title">
    <h4>{t}Special contents{/t}</h4>
  </div>
  <div class="grid-body">
    <div class="related-row row">
      <div class="col-md-6" {if isset($orderFront)}ng-init="contents_left_column = {json_encode($contentsLeft)|replace:'"':'\''}"{/if}>
        <h5>{t}Articles in left column{/t}</h5>
        <div ui-sortable ng-model="contents_left_column">
          <div class="related-item" ng-repeat="content in contents_left_column">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('contents_left_column', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="contents_left_column" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <button type="button" class="btn btn-primaty">{t}Click here to add contents{/t}</button>
        </div>
      </div>
      <div class="col-md-6" {if isset($orderInner)}ng-init="contents_right_column = {json_encode($contentsRight)|replace:'"':'\''}"{/if}>
        <h5>{t}Articles in right column{/t}</h5>
        <div ui-sortable ng-model="contents_right_column">
          <div class="related-item" ng-repeat="content in contents_right_column">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('contents_right_column', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="contents_right_column" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <button type="button" class="btn btn-primaty">{t}Click here to add contents{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" name="contents_left_column" ng-value="contents_left_column"/>
<input type="hidden" name="contents_right_column" ng-value="contents_right_column"/>

{*
<div id="cates" class="special-container" style="display:{if $special->only_pdf eq 0}inline{else}none{/if};">
    <table style="width:100%">
        <tr>
            <td>
                <div id="column_right" class="column-receiver">
                    <h5>  Para añadir contenidos columna izquierda arrastre sobre este cuadro </h5>
                    <hr>
                    <ul class="content-receiver" >
                        {section name=d loop=$contentsRight}
                            <li class="" data-type="{$contentsRight[d]->content_type}" data-id="{$contentsRight[d]->pk_content}">
                                {$contentsRight[d]->created|date_format:"%d-%m-%Y"}:{$contentsRight[d]->title|clearslash}
                                <span class="icon"><i class="fa fa-trash"></i></span>
                            </li>
                        {/section}
                    </ul>
                </div>

                <div id="column_left" class="column-receiver">
                        <h5> Para añadir contenidos columna derecha arrastre sobre este cuadro </h5>
                        <hr>
                        <ul class="content-receiver" >
                        {section name=d loop=$contentsLeft}
                            <li class="" data-type="{$contentsLeft[d]->content_type}" data-id="{$contentsLeft[d]->pk_content}">
                                {$contentsLeft[d]->created|date_format:"%d-%m-%Y"}:{$contentsLeft[d]->title|clearslash}
                                <span class="icon"><i class="fa fa-trash"></i></span>
                            </li>
                        {/section}
                        </ul>
                </div>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td>
                {include file="article/related/_related_provider.tpl"}

            </td>
        </tr>
    </table>
</div>*}
