<hr>

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
</div>
