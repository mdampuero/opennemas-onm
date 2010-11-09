{extends file="mobile/mobile_layout.tpl"}


{block sname="content"}
	<div id="content">

        <strong>Director</strong>

    	<br class="clearer" />
        {if isset($director)}
            {include file="mobile/partials/opinion_author_element_list.tpl" article=$director}
        {else}
            El director no tiene opiniones todavía.
        {/if}


        <strong>Editoriales</strong>

		<br class="clearer" />
        {if isset($editorial)}
            {section name="edit" loop=$editorial}
                {include file="mobile/partials/opinion_author_element_list.tpl" article=$editorial[edit]}
            {/section}
        {else}
            No hai editoriales todavia
        {/if}

        <strong>Colaboradores</strong>

		<br class="clearer" />

        {if isset($opinions)}

            {section name=iter loop=$opinions}
                {include file="mobile/partials/opinion_author_element_list.tpl" op_colaborador=$opinions[iter]}
            {/section}
        {else}
            No hai opiniones de colaboradores
        {/if}

	</div>

{/block}
