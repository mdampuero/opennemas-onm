<table style="margin-bottom:0; width:100%;">
    <tbody>
        <tr>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                {include file="article/related/_related_provider.tpl"}
            </td>
            <td style="width:50%; vertical-align:top; padding:4px 0;" >
                <div id="frontpage_related" class="column-receiver">
                    <h5>{t}Related in frontpage{/t}</h5>
                    <hr>
                    <ul class="content-receiver" >
                        {section name=d loop=$losrel}
                            <li class="" data-type="{$losrel[d]->content_type}" data-id="{$losrel[d]->pk_content}">
                                {$losrel[d]->created|date_format:"%d-%m-%Y"}:{$losrel[d]->title|clearslash}
                            </li>
                        {/section}
                    </ul>
                </div>

                <div id="inner_related" class="column-receiver">
                        <h5>{t}Related in inner{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                        {section name=d loop=$intrel}
                            <li class="" data-type="{$intrel[d]->content_type}" data-id="{$intrel[d]->pk_content}">
                                {$intrel[d]->created|date_format:"%d-%m-%Y"}:{$intrel[d]->title|clearslash}
                            </li>
                        {/section}
                        </ul>
                </div>


                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                    <div id="home_related" class="column-receiver">
                        <h5>{t}Related in home{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {section name=d loop=$contentsHome}
                                <li class="" data-type="{$contentsHome[d]->content_type}" data-id="{$contentsHome[d]->pk_content}">
                                    {$contentsHome[d]->created|date_format:"%d-%m-%Y"}:{$contentsHome[d]->title|clearslash}
                                </li>
                            {/section}
                        </ul>

                    </div>
                    <div id="gallery-Frontpage" class="column-receiver gallery">
                        <h5>{t}Gallery for frontpage{/t}(*{t}Only one album{/t})</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries[front]->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries[front]->created|date_format:"%d-%m-%Y"}:{$galleries[front]->title|clearslash}
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Inner" class="column-receiver gallery">
                         <h5>{t}Gallery for inner{/t}(*{t}Only one album{/t})</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries[inner]->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries[inner]->created|date_format:"%d-%m-%Y"}:{$galleries[inner]->title|clearslash}
                                </li>
                            {/if}
                        </ul>
                    </div>
                    <div id="gallery-Home" class="column-receiver gallery">
                        <h5>{t}Gallery for Home{/t} (*{t}Only one album{/t})</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {if !empty($article->params['withGallery']) && !empty($galleries[home]->pk_album)}
                                <li class="" data-type="Album" data-id="{$article->params['withGallery']}">
                                    {$galleries[home]->created|date_format:"%d-%m-%Y"}:{$galleries[home]->title|clearslash}
                                </li>
                            {/if}
                        </ul>

                    </div>
                {/is_module_activated}

            </td>
        </tr>
    </tbody>
</table>