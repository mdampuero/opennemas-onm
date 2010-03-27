
    <li>
        <a class="thumb" href="{$MEDIA_IMG_PATH_WEB}{$photoData->path_file}{$photoData->name}" title="{$photoData->title|clearslash|escape:'html'}">
            <img src="{$MEDIA_IMG_PATH_WEB}{$photoData->path_file}{$photoData->name}" alt="{$photoData->title|clearslash|escape:'html'}" />
        </a>
        <div class="caption">
            <div class="image-title">{$photo.description}</div>
            <div class="image-desc">{$photoData->title}</div>
        </div>
    </li>
