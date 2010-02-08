{include file="modulo_head.tpl"}
<body>
{insert name="intersticial" type="50"}
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">
            {include file="modulo_separadorbanners1.tpl"}
            {include file="modulo_header.tpl"}
            <div class="container">
                <div class="containerNoticias">
                    <div class="column12">
                        <div class="containerCol12 fondoContainerActualidad">
                            {include file="kiosko_index_content.tpl"}
                        </div>
                        
                    </div>
                    {include file="kiosko_index_column3.tpl"}
                </div>
                
                <div class="separadorHorizontal"></div>
                {include file="modulo_separadorbanners3.tpl"}
                {* <div class="separadorHorizontal"></div> *}
             </div>
        {include file="modulo_footer.tpl"}
        </div>
    </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>