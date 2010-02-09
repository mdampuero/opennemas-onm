{include file="mobile/header.tpl"}

<div id="opinion">
    
    {* DIRECTOR *}
    <div class="opinion principal director">
        { if $dir.photo}
        <div class="img-author">
            <img src="/media/images/{$dir.photo}" alt="{$dir.name}" />
            <br class="clearer" />
        </div>
        {else}
            <img src="{$params.IMAGE_DIR}opinion/editorial.jpg" alt="{$dir.name}"/>
        {/if}
        
        <div class="titular">
            {$dir.name} &raquo;
            <a href="{$smarty.const.BASE_URL}{$director->permalink}">{$director->title|clearslash}</a>
        </div>
        
        <div class="fecha">{humandate article=$director created=$director->created updated=$director->updated}</div>        
        
        <div class="entradilla">
            {$director->body|strip_tags:false|clearslash|truncate:250}
            <a href="{$smarty.const.BASE_URL}{$director->permalink}">&raquo; Sigue</a>
        </div>
        
        <br class="clearer" />
    </div>
    
            
    {* Editoriales: *}
    <div class="opinion principal editorial">
        <strong>Editoriales</strong>
        
        {section name="ed" loop=$editorial}
        <div class="titular">
            &raquo; <a href="{$smarty.const.BASE_URL}{$editorial[ed]->permalink|default:"#"}" >{$editorial[ed]->title|clearslash}</a>
        </div>
        <div class="entradilla">
            {$editorial[ed]->body|strip_tags:false|clearslash|truncate:250}
            <a href="{$smarty.const.BASE_URL}{$editorial[ed]->permalink}">&raquo; Sigue</a>
        </div>
        {/section}
        
        <br class="clearer" />
    </div>
    
    {section name=ac loop=$opinions}
        <div class="opinion principal {cycle values="odd,even"}">
            <div class="img-author">
                <img src="/media/images{$opinions[ac].path_img}" alt="{$opinions[ac].name}" />
                <br class="clearer" />
            </div>
            
            <div class="titular">
                {$opinions[ac].name} &raquo;
                <a href="{$smarty.const.BASE_URL}{$opinions[ac].permalink}">{$opinions[ac].title|clearslash}</a>
            </div>
            
            <div class="fecha">{humandate article=$opinions[ac] created=$opinions[ac].created updated=$opinions[ac].updated}</div>
            
            <div class="entradilla">
                {$opinions[ac].body|strip_tags:false|clearslash|truncate:250}
                <a href="{$smarty.const.BASE_URL}{$opinions[ac].permalink}">&raquo; Sigue</a>
            </div>
             
            <br class="clearer" />
        </div>
    {/section}        
</div>

{include file="mobile/footer.tpl"}