<h1>{$page_title|escape}</h1>

{if $data->count() == 0}
    
    <p>
        No posts found.
    </p>
    
{else}
    
    {foreach from=$data item=post}
        {include file='post.view.tpl' data=$post single_post=false}
    {/foreach}

{/if}
