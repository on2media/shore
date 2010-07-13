<h1>{$page_title|escape}</h1>

{foreach from=$data item=post}
    {include file='post.view.tpl' data=$post single_post=false}
{/foreach}
