{if $single_post === null}{assign var=single_post value=true}{/if}

{capture assign=hx}{if $single_post}h1{else}h2{/if}{/capture}

{if $data instanceof PostObject}
    
    <{$hx}><a href="{$base}post/{$data->getId()}/">{$data->getTitle()|escape}</a></{$hx}>
    
    <p>
        Posted by <a href="{$base}author/{$data->getAuthor()->getId()}/">{$data->getAuthor()->getName()|escape}</a>
        on {$data->getPosted()|date:'jS F Y'} at {$data->getPosted()|date:'g:ia'}.
    </p>
    
    {assign var=numTags value=$data->getTags()->count()}
    {if $numTags > 0}
        
        <p>
            Tags:{counter assign=i start=0}{foreach from=$data->getTags() item=tag} {strip}
                {counter assign=i}
                <a href="{$base}tag/{$tag->getId()}/">{$tag->getTag()|escape}</a>
                {if $i != $numTags},{else}.{/if}
            {/strip}{/foreach}
        </p>
        
    {/if}
    
    {if $single_post}
        
        {$data->getContent()}
        
        {assign var=numComments value=$data->getComments()->count()}
        {if $numComments > 0}
            
            <hr />
            
            {foreach from=$data->getComments() item=comment}
            
                <p>
                    Received: {$comment->getReceived()|date}
                </p>
                
                <p>
                    {$comment->getContent()|nl2br}
                </p>
                
                <hr />
                
            {/foreach}
        {/if}
        
        {if $data->getCanComment() === true}
            
            Comment form.
            
        {/if}
        
    {else}
        
        <p>
            {$data->getContent()}
        </p>
        
        <p>
            <a href="{$base}post/{$data->getId()}/">Read More&hellip;</a>
        </p>
        
        <hr />
    {/if}
    
{/if}