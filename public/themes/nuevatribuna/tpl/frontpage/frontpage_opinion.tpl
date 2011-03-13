{*
    OpenNeMas project

    @theme      Lucidity
*}


<div class="onm-new {$cssclass}">
     <div class="nw-author"><strong>OPINION</strong> | {$item->author|mb_upper}</div>
     {if $category_name eq 'home'}
        <div class="nw-category-name {$item->category_name}">{$item->category_title|upper|clearslash} <span>&nbsp;</span></div>
     {/if}
    <h3 class="nw-title"><a href="{generate_uri   content_type="opinion"
                                                id=$item->id
                                                date=$item->created
                                                title=$item->title
                                                category_name=$item->author_name_slug}" title="{$item->title|clearslash}">{$item->title|clearslash}</a></h3>
    <div class="nw-subtitle">{$item->body|clearslash|strip_tags|truncate:200:'...':false:false}</div>

</div>