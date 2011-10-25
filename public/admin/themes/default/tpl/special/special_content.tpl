<tr>
    <td valign="top" align="right" style="padding:4px;" >
        <label for="div_pdf">Noticias:</label>

        <div id="div_pdf" class="seccion" style="{if $special->only_pdf eq 1}display:inline;{else}display:none;{/if} padding:12px; margin-right:10px;float:left;width:240px;border:1px solid gray;">
            <br />
            <input type="hidden" size="20" id="my_pdf" name="my_pdf" value="" />
            <input type="text" size="80" id="pdf" name="pdf" value="{$special->pdf}" />
            <br />
            <a onclick="javascript:abrirArchives('my_pdf','{$category}');" href="#">
            <img border="0" src="images/iconos/examinar.gif"/>
            Buscar documento pdf
            </a>
        </div>
     </td>
</tr>
 <tr>
    <td>
        <br style="clear:both;"/>
        <div id="cates" style="width:90%;{if $special->only_pdf eq 0}display:inline;{else}display:none;{/if}">
                 <div id="cates_right" style="position:relative;float:left;width:50%;border:1px solid gray;">
                     <h5 style="margin-right:8px;">  Para añadir contenidos columna izquierda arrastre sobre este cuadro </h5>
                     <hr>
                     <ul>
                     {section name=d loop=$noticias_right}
                        <li>
                            {$noticias_right[d]->title|clearslash}
                            {$noticias_right[d]->created|date_format:"%d-%m-%Y"}
                        </li>
                     {/section}
                     </ul>
                 </div>

                  <div id="cates_left"  style="position:relative;float:left;width:50%;border:1px solid gray;">
                        <h5 style="margin-left:8px;"> Para añadir contenidos columna derecha arrastre sobre este cuadro </h5>
                        <hr>
                        <ul>
                        {section name=d loop=$noticias_left}
                            <li>
                                 {$noticias_left[d]->title|clearslash}
                                 {$noticias_left[d]->created|date_format:"%d-%m-%Y"}
                            </li>
                        {/section}
                        </ul>
                 </div>
                 <br style="clear:both;"/>

                <div id="divarticles" style="float:left;width:100%;">

                    <select id="ccategory" name="ccategory" onChange="changeSpecials(this.options[this.selectedIndex].value, 1);">
                         <option value="0">GLOBAL</option>
                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                                {section name=su loop=$subcat[as]}
                                        <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                {/section}
                            {/section}
                    </select>

                    <div id="art" style="width:60%;float:left;border:1px solid gray;"> <br>

                    </div>

                    <div id="litter" style="margin-left:20px;float:left;width:40%;background-color:#DDD;border:1px solid gray;">
                        <h5> <img src="themes/default/images/trash_ico.png"> Arrastre aquí los elementos que desea quitar <br /></h5>
                    </div>
                </div>
    
        </div>
      </td>
</tr>

 
<script type="text/javascript">
 // <![CDATA[
   Sortable.create('art',{
   tag:'table',
   dropOnEmpty: true,
   containment:["art","cates_right", "cates_left","litter"],
   constraint:false});

  Sortable.create('cates_right',{
   tag:'table',
   dropOnEmpty: true,
   containment:["art","cates_right", "cates_left","litter"],
   constraint:false});
 Sortable.create('cates_left',{
   tag:'table',
   dropOnEmpty: true,
   containment:["art","cates_right", "cates_left","litter"],
   constraint:false});
 Sortable.create('litter',{
   tag:'table',
   dropOnEmpty: true,
   containment:["art","cates_right", "cates_left","litter"],
   constraint:false});

 // ]]>
</script>
