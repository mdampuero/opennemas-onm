<table style="margin-bottom:0; width:100%;">
    <tbody>
        <tr>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                {include file="article/related/_related_provider.tpl"}
            </td>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                <div id="frontpage_related" class="column-receiver">
                    <h5>{t}Related in frontpage{/t}</h5>
                    <ul class="content-receiver" >
                        {foreach from=$orderFront item=content}
                            <li data-id="{$content->pk_content}" data-type="Opinion" data-title="{$content->title|clearslash}">
                                <span class="type">{ucfirst($content->content_type_name)} -</span>
                                <span class="date">{$content->created|date_format:"%d-%m-%Y"} -</span>
                                {$content->title|clearslash}
                                <span class="icon"><i class="icon-trash"></i></span>
                            </li>
                        {/foreach}
                    </ul>
                </div>

                <div id="inner_related" class="column-receiver">
                        <h5>{t}Related in inner{/t}</h5>
                        <ul class="content-receiver" >
                        {foreach from=$orderInner item=content}
                            <li data-id="{$content->pk_content}" data-type="Opinion" data-title="{$content->title|clearslash}">
                                <span class="type">{ucfirst($content->content_type_name)} -</span>
                                <span class="date">{$content->created|date_format:"%d-%m-%Y"} -</span>
                                {$content->title|clearslash}
                                <span class="icon"><i class="icon-trash"></i></span>
                            </li>
                        {/foreach}
                        </ul>
                </div>


                {is_module_activated name="CRONICAS_MODULES"}
                    <div id="home_related" class="column-receiver">
                        <h5>{t}Related in home{/t}</h5>
                        <ul class="content-receiver" >
                        {foreach from=$orderHome item=content}
                            <li data-id="{$content->pk_content}" data-type="Opinion" data-title="{$content->title|clearslash}">
                                <span class="type">{ucfirst($content->content_type_name)} -</span>
                                <span class="date">{$content->created|date_format:"%d-%m-%Y"} -</span>
                                {$content->title|clearslash}
                                <span class="icon"><i class="icon-trash"></i></span>
                            </li>
                        {/foreach}
                        </ul>
                    </div>
                {/is_module_activated}
                {is_module_activated name="CRONICAS_MODULES"}
                    <div id="gallery-Frontpage" class="column-receiver gallery">
                        <h5>{t}Gallery for frontpage{/t}(*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries['front']->pk_album)}
                                <li class="" data-type="Album" data-id="{$galleries['front']->pk_album}">
                                    {$galleries['front']->created|date_format:"%d-%m-%Y"} : {$galleries['front']->title|clearslash}
                                    <span class="icon"><i class="icon-trash"></i></span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Inner" class="column-receiver gallery">
                        <h5>{t}Gallery for inner{/t}(*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGalleryInt']) && !empty($galleries['inner']->pk_album)}
                                <li class="" data-type="Album" data-id="{$galleries['inner']->pk_album}">
                                    {$galleries['inner']->created|date_format:"%d-%m-%Y"} : {$galleries['inner']->title|clearslash}
                                    <span class="icon"><i class="icon-trash"></i></span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Home" class="column-receiver gallery">
                        <h5>{t}Gallery for Home{/t} (*{t}Only one album{/t})</h5>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGalleryHome']) && !empty($galleries['home']->pk_album)}
                                <li class="" data-type="Album" data-id="{$galleries['home']->pk_album}">
                                    {$galleries['home']->created|date_format:"%d-%m-%Y"} : {$galleries['home']->title|clearslash}
                                    <span class="icon"><i class="icon-trash"></i></span>
                                </li>
                            {/if}
                        </ul>

                    </div>
                {/is_module_activated}

            </td>
        </tr>
    </tbody>
</table>