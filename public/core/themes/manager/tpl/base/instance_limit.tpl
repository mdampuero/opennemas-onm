{if $language === 'en'}
  <ul>
    {if $instance->users > 1}
      <li>
        You have {$instance->users} activated users.
        {if $instance->users > 1}
          The cost is {($instance->users - 1) * 0.40 } €/day or {($instance->users - 1) * 10 } €/month.
        {/if}
      </li>
    {/if}
    {if $instance->page_views > 45000}
      <li>
        You have {$instance->page_views} page views.
        {if $instance->page_views > 50000}
          The cost is {number_format($instance->page_views * 0.000075, 2)} €/month.
        {/if}
      </li>
    {/if}
    {if $instance->media_size > 450}
      <li>
        Your storage size is {round($instance->media_size)} MB.
        {if $instance->media_size > 500}
          The cost is {number_format($instance->media_size * 0.01, 2)} €/month.
        {/if}
      </li>
    {/if}
  </ul>
{/if}
{if $language === 'es'}
  <ul>
    {if $instance->users > 1}
      <li>
        Tienes {$instance->users} usuarios activados.
        {if $instance->users > 1}
          El coste es de {($instance->users - 1) * 0.40 } €/día o {($instance->users - 1) * 10 } €/mes.
        {/if}
      </li>
    {/if}
    {if $instance->page_views > 45000}
      <li>
        Tienes {$instance->page_views} páginas vistas.
        {if $instance->page_views > 50000}
          El coste es de {number_format($instance->page_views * 0.000075, 2)} €/mes.
        {/if}
      </li>
    {/if}
    {if $instance->media_size > 450}
      <li>
        El tamaño ocupado es de {number_format($instance->media_size, 2)} MB.
        {if $instance->media_size > 500}
          El coste es de {number_format($instance->media_size * 0.01, 2) } €/mes.
        {/if}
      </li>
    {/if}
  </ul>
{/if}
{if $language === 'gl'}
  <ul>
    {if $instance->users > 1}
      <li>
        Tes {$instance->users} usuarios activados.
        {if $instance->users > 1}
          O custo é de {($instance->users - 1) * 0.40 } €/día ou {($instance->users - 1) * 10 } €/mes.
        {/if}
      </li>
    {/if}
    {if $instance->page_views > 45000}
      <li>
        Tes {$instance->page_views} páxinas vistas.
        {if $instance->page_views > 50000}
          O custo é de {number_format($instance->page_views * 0.000075, 2)} €/mes.
        {/if}
      </li>
    {/if}
    {if $instance->media_size > 450}
      <li>
        O tamaño ocupado é de {round($instance->media_size)} MB.
        {if $instance->media_size > 500}
          O custo é de {number_format($instance->media_size * 0.01, 2)} €/mes.
        {/if}
      </li>
    {/if}
  </ul>
{/if}
