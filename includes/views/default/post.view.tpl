{if $single_post === null}{assign var=single_post value=true}{/if}

{capture assign=hx}{if $single_post}h1{else}h2{/if}{/capture}

{if $data instanceof PostObject}
    
    <div class="post">
        
        <{$hx}><a href="{$base}post/{$data->uid()|escape}/">{$data->getTitle()|escape}</a></{$hx}>
        
        <div class="post_meta">
            
            <p>
                Posted by <a href="{$base}author/{$data->getAuthor()->uid()|escape}/">{$data->getAuthor()->getName()|escape}</a>
                on {$data->getPosted()|date:'jS F Y \a\t g:ia'}{if $data->getTopic() instanceof TopicObject}
                under <a href="{$base}topic/{$data->getTopic()->uid()|escape}/">{$data->getTopic()->getTopic()|escape}</a>{/if}.
            </p>
            
            {assign var=numTags value=$data->getTags()->count()}
            {if $numTags > 0}
                
                <p>
                    Tags:{counter assign=i start=0}{foreach from=$data->getTags() item=tag} {strip}
                        {counter assign=i}
                        <a href="{$base}tag/{$tag->uid()|escape}/">{$tag->getTag()|escape}</a>
                        {if $i != $numTags},{else}.{/if}
                    {/strip}{/foreach}
                </p>
                
            {/if}
            
        </div>
        
        {if $single_post}
            
            {$data->getContent()}
            
            <div class="post_comments">
                
                {assign var=numComments value=0}
                {foreach from=$data->getComments() item=comment}
                    
                    {if $comment->getApproved()}
                        
                        {if $numComments++ == 0}<h3>Comments</h3>{/if}
                        
                        <div class="post_comment">
                            
                            <p class="post_comment_meta">
                                <img
                                    src="http://www.gravatar.com/avatar/{$comment->getEmail()|trim|strtolower|md5}.jpg?w=60&amp;d=identicon"
                                    width="60" height="60" alt="{$comment->getName()|escape}"
                                />
                                Left by {if $comment->getWebsite() != ''}
                                    <a href="{$comment->getWebsite()|escape}" rel="nofollow">{$comment->getName()|escape}</a>
                                {else}
                                    {$comment->getName()|escape}
                                {/if} on {$comment->getReceived()|date:'jS F Y \a\t g:ia'}:
                            </p>
                            
                            <p>
                                {$comment->getContent()|nl2br}
                            </p>
                            
                        </div>
                        
                    {/if}
                    
                {/foreach}
                
            </div>
            
            {$comment_form}
            
        {else}
            
            <p>
                {$data->getContent()|strip_tags|truncate:400}
            </p>
            
            <p>
                <a href="{$base}post/{$data->uid()|escape}/">Read More&hellip;</a>
            </p>
            
        {/if}
        
    </div>
    
{/if}