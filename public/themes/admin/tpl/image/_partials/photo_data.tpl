<div id="photo-{$photo->id}" class="form-vertical clearfix photo-edit">
  <div class="col-md-4">
    <div class="thumbnail">
      {if preg_match('/^swf$/i', $photo->type_img)}
      <object>
        <param name="wmode" value="opaque" />
        <embed src="{$smarty.const.INSTANCE_MAIN_DOMAIN}{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
      </object>
      {else}
      {dynamic_image src="{$photo->path_file}{$photo->name}" transform="thumbnail,330,330" onunload="GUnload()"}
      {/if}
    </div>
    <div>
      <a class="btn btn-white" href="{$smarty.const.INSTANCE_MAIN_DOMAIN}{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" target="_blank">
        <span class="fa fa-download"></span> {t}View original{/t}
      </a>
    </div>
    <br>
    <div class="well well-small">
      <div><strong>{t}Original filename:{/t}</strong> {$photo->title}</div>
      <div><strong>{t}Resolution:{/t}</strong> {$photo->width}px x {$photo->height}px</div>
      <div><strong>{t}Size:{/t}</strong> {$photo->size} Kb</div>
      <div><strong>{t}Date:{/t}</strong> {$photo->created}</div>
    </div>
  </div>
  <div class="photo-basic-information col-md-8">
    <div class="form-group">
      <label for="description-{$photo->id}" class="form-label">{t}Description{/t}</label>
      <div class="controls">
        <textarea required id="description-{$photo->id}" name="description[{$photo->id}]" class="form-control" rows="4">{$photo->description|clearslash}</textarea>
      </div>
    </div>
    <div class="form-group">
      <label for="metadata-{$photo->id}" class="form-label">{t}Keywords{/t}</label>
      <div class="controls">
        <input data-role="tagsinput" type="text" id="metadata-{$photo->id}" name="metadata[{$photo->id}]" placeholder="{t}Write a tag and press Enter...{/t}" required value="{$photo->metadata|strip}" class="form-control" />
        <div class="help-block">{t}Used for searches and automated suggestions.{/t}</div>
      </div>
    </div>
    <div class="form-group">
      <label for="author_name[{$photo->id}]" class="form-label">{t}Copyright{/t}</label>
      <div class="controls">
        <div class="input-group">
          <input class="form-control" type="text" id="author_name[{$photo->id}]" name="author_name[{$photo->id}]" value='{$photo->author_name|clearslash}'/>
          <span class="input-group-addon add-on">
            <span class="fa fa-copyright"></span>
          </span>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="geolocation" class="form-label">{t}Location{/t}</label>
      <div class="controls">
        <div class="input-group col-xs-6">
          <input class="form-control photo_address" type="text" id="address_{$photo->id}" name="address[{$photo->id}]" value="{$photo->address}">
          <span class="input-group-btn">
            <button class="locate btn btn-default" data-image-id="{$photo->id}" data-toggle="modal" href="#modal-image-location" type="button">
              <i class="fa fa-map-marker"></i>
            </button>
          </span>
        </div>
      </div>
    </div>
    <div class="iptc-exif">
      <h5 class="toggler toggler_{$photo->id}" ng-click="info_{$photo->id} = !info_{$photo->id}">
        {t escape=off}View advanced data{/t}
        <span>
          <i class="fa" ng-class="{ 'fa-plus-square-o': !info_{$photo->id}, 'fa-minus-square-o': info_{$photo->id} }"></i>
        </span>
      </h5>
      <div class="info info_{$photo->id} ng-cloak" ng-if="info_{$photo->id}">
        {if is_null($photo->exif) neq true}
          <h6>{t}EXIF Data{/t}</h6>
          <div id="exif" class="photo-static-info">
            {foreach $photo->exif as $name => $value}
              {foreach $value as $d => $dato}
              <p>
                <strong>{$d}</strong> : {$dato}
              </p>
              {/foreach}
            {/foreach}
          </div>
        {else}
          <div id="exif" class="photo-static-info">
            {t}No available EXIF data.{/t}
          </div>
        {/if}

        {if isset($photo->myiptc)}
          <h6>{t}IPTC Data{/t}</h6>
          <div id="iptc" class="photo-static-info">
            {foreach $photo->myiptc as $name => $value}
            {if $name}
            <p>
              <strong>{$name}</strong> : {$value}
            </p>
            {/if}
            {/foreach}
            <br />
          </div>
        {else}
          <div id="iptc" class="photo-static-info">
            {t}No available IPTC data.{/t}
          </div>
        {/if}
      </div><!-- /additional-info -->
    </div>
    <input type="hidden" name="resolution[{$photo->id}]" value="{$photo->resolution}" />
    <input type="hidden" name="title[{$photo->id}]" value="{$photo->title}" />
    <input type="hidden" name="category[{$photo->id}]" value="{$photo->category}" />
  </div><!-- /photo-{$rnf} -->
</div>
{javascripts}
  <script type="text/javascript">
    $(document).ready(function() {
      $('#description-{$photo->id}').on('change', function(e, ui) {
        fill_tags(jQuery('#description-{$photo->id}').val(),'#metadata-{$photo->id}', '{url name=admin_utils_calculate_tags}');
      });
    });
  </script>
{/javascripts}
