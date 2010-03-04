{* Load config from index.php, it isn't possible load conditional config this mode
   use Template::loadConfig($file, $section) *}
{* config_load file='../config/template.conf' section='index' *} 
{include file="modulo_head.tpl"}
<body>
{insert name="intersticial" type="50"}
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">
{include file="modulo_separadorbanners1.tpl"}
{include file="modulo_header.tpl"}
            <div class="container">
            {if $category_name eq 'home' }
                <div class="containerNoticias">
                    <div class="column12">
                        {* Check if 1st article is showed as principal *}
                        {if $category_name eq 'home' && $destaca[0]->home_columns == '2' }
                            {include file="index_noticiadestacada.tpl"}
                            <div class="separadorHorizontal"></div>
                        {/if}
                        <div class="containerCol12">
                            {include file="index_column1.tpl"}
                            {include file="index_column2.tpl"}
                        </div>
                    </div>
                    {include file="index_column3.tpl"}
                </div>
            {else}
                <div class="containerNoticiasFrontpage" style="background-image:transparent;">
                    <div class="column12big">
                        {* Check if 1st article is showed as principal *}
                        {if $destaca[0]->columns == '2' }
                            {include file="index_noticiadestacada_frontpages.tpl"}
                            <div class="separadorHorizontal"></div>
                        {/if}
                        <div class="containerCol123" >
                            {include file="index_column1_frontpages.tpl"}
                            {include file="index_column2_frontpages.tpl"}
                            {*include file="index_column3_frontpages.tpl"*}
                        </div>
                    </div>
                </div>
            {/if}
            {if $category_name eq 'home'}
                <div class="separadorHorizontal"></div>
                {include file="modulo_separadorbanners2.tpl"}
                <div class="separadorHorizontal"></div>
                <div class="titularesDia">
                    <div class="contenedorColTitulares">
                        {include file="index_columnstitulares.tpl"}
                        {include file="index_column3titulares.tpl"}
                    </div>
                </div>
            {/if}
            <div class="separadorHorizontal"></div>
            {include file="modulo_separadorbanners3.tpl"}
            </div>
        {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{* Don't load if preview mode is actived *}
{if !isset($smarty.session.userid) || empty($smarty.session.userid)}
    {include file="modulo_analytics.tpl"}

    {scriptsection name="footer"}
    <script type="text/javascript" src="{php}echo($this->js_dir);{/php}wz_tooltip.js"></script>
    {/scriptsection}
{/if}
</body>
</html>