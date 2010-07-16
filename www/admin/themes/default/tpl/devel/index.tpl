{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Developers tutorial{/t}{/block}

{block name="body-content"}

    {flashmessenger}
    
    <h1>{t}Hello{/t} {$nome}</h1>
    
    {for $x=0; $x<strlen($nome) && $x<7; $x++}
        <h{$x+1}> {$nome[$x]} </h{$x+1}>
    {/for}
    
    <form action="{baseurl}/{url route="devel-hello"}" method="get" id="myForm">
        <input type="text" name="nome" id="nome" value="{$nome}" />
        
        <input type="submit" value="Enviar" />
    </form>
    
    <script type="text/javascript">
    {* Mira mamá sin tag literal *}
    $('#myForm').submit(function(event) {
        return confirm('Confirma o envío de: ' + $('#nome').val());
    });
    </script>
    
{/block}