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
                            <li class="" data-type="{$contentsLeft[d]->content_type}" data-id="{$contentsLeft[d]->pk_content}">
                                {$contentsLeft[d]->created|date_format:"%d-%m-%Y"}:{$contentsLeft[d]->title|clearslash}
                            </li>
                        {/section}
                        </ul>
                </div>


                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                    <div id="home_related" class="column-receiver">
                        <h5>{t}Related in home{/t}</h5>
                        <hr>
                        <ul class="content-receiver" >
                            {section name=d loop=$contentsRight}
                                <li class="" data-type="{$contentsRight[d]->content_type}" data-id="{$contentsRight[d]->pk_content}">
                                    {$contentsRight[d]->created|date_format:"%d-%m-%Y"}:{$contentsRight[d]->title|clearslash}
                                </li>
                            {/section}
                        </ul>

                    </div>
                {/is_module_activated}

                <input type="hidden" id="relatedFrontpage" name="ordenArti" value="" />
                <input type="hidden" id="relatedInner" name="ordenArtiInt" value="" />

                <input type="hidden" id="params[withGallery]" name="params[withGallery]" value="" />
                <input type="hidden" id="params[withGalleryInt]" name="params[withGalleryInt]" value="" />

                <input type="hidden" id="params[relatedHome]" name="params[relatedHome]" value="" />
                <input type="hidden" id="params[withGalleryHome]" name="params[withGalleryHome]" value="" />

            </td>
        </tr>
    </tbody>
</table>