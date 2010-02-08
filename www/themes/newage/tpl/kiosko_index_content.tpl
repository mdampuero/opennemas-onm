
<div class="zonaClasificacionContenidosPortadaPC" style="margin: 10px;">
    {if ($smarty.request.action eq "list") && (!empty($kiosko))}

        <h2 style="margin-left:4px; font-weight:normal; color:#024685;">Portadas de {$MONTH|month_spanish} del {$YEAR}</h2>

        <div class="listadoEnlacesPlanConecta">
            {section name=i loop=$kiosko}
                {if !empty($kiosko[i].portadas)}
                <div>
                    <a name="{$kiosko[i].category|lower}"></a>
                    <h2 style="margin-top: 20px; margin-left:4px; font-weight:normal; color:#024685;">{$kiosko[i].category}</h2>

                    {section name=j loop=$kiosko[i].portadas}
                    <div class="portada">
                        <a href="{$MEDIA_FILE_PATH_WEB}kiosko{$kiosko[i].portadas[j]->path}{$kiosko[i].portadas[j]->name}" target="_blank" title="{$kiosko[i].portadas[j]->title|clearslash}" alt="{$kiosko[i].portadas[j]->title|clearslash}">
                            <img width="150" src="{$MEDIA_IMG_PATH_WEB}kiosko{$kiosko[i].portadas[j]->path}{$kiosko[i].portadas[j]->name|regex_replace:"/.pdf$/":".jpg"}" title="{$kiosko[i].portadas[j]->title|clearslash}" alt="{$kiosko[i].portadas[j]->title|clearslash}" /><br />
                            {$kiosko[i].portadas[j]->date|kiosko_date_format}
                        </a>
                    </div>
                    {/section}

                    <div style="clear:both; width="1px"></div>
                    <div class="fileteHorizontalPC"></div>
                </div>
                {/if}
            {/section}

        </div>
    {/if}       

</div>