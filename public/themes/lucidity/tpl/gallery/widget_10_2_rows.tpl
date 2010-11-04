<style type="text/css">
{literal}
#more-galleries-widget{
    background:#ddd;
}
#more-galleries-widget .wrapper{
    padding:20px;
}
#more-galleries-widget .widget-title{
    width:141px;
    height:80px;
}
#more-galleries-widget div.more-galleries-row {
    margin:0;
    width:780px;
}

#more-galleries-widget div.more-galleries-row ul{
    margin:0;
}

#more-galleries-widget div.more-galleries-row li{
    display:inline-block;
    width:143px;
    height:160px;
    overflow:hidden;
    margin-right:10px;
    float:left;
}

#more-galleries-widget div.more-galleries-row li.last{
    margin:0;
}
#more-galleries-widget div.more-galleries-row li a{
    text-decoration:none;
}
#more-galleries-widget div.more-galleries-row li a img{
    background:#545454;
    border:1px solid #adadad;
}

#more-galleries-widget div.more-galleries-row li:hover a img{
    border:1px solid #333;
}

#more-galleries-widget div.more-galleries-row li a p{
    margin:0;
    padding:0;
}
#more-galleries-widget div.more-galleries-row li a p.gallery-title{
    color:#1f4f82;
    font-family:Arial;
    font-weight:bold;
}
#more-galleries-widget div.more-galleries-row li a p.gallery-description{
    color:#505050;
    font-family:Arial;
    font-weight:normal;
}
#more-galleries-widget div.more-galleries-row li:hover a p.gallery-description{
    color:#000;
}
{/literal}
</style>
<div id="more-galleries-widget" class="span-24 last">
    <div class="wrapper clearfix">
        <div class="span-3 widget-title">
            <h3><img src="{$params.IMAGE_DIR}/gallery/more-galleries-widget-icon.png" alt="Mas galerias"> </h3>
        </div>
        <div class="span-20 last more-galleries-row">
            <ul>
                {section name="galleryelements" loop=$galleries}
                <li {if (($smarty.section.galleryelements.iteration % 5) eq 0) && !($smarty.section.i.first eq 1)}class="last"{/if}>
                    <a href="{$galleries[galleryelements]->permalink}" class="clearfix" title="Ver la galeria: {$galleries[galleryelements]->title}">
                        <img width="141" height="80" src="{$smarty.const.MEDIA_URL}/media/images/{$galleries[galleryelements]->cover}" alt="">
                        <div class="gallery-explanation">
                            <p class="gallery-title">{$galleries[galleryelements]->title}</p>
                            <p class="gallery-description">{$galleries[galleryelements]->description|truncate:60:"..."}</p>
                        </div>
                    </a>
                </li>
                {/section}
            </ul>
        </div>  
    </div>
</div>