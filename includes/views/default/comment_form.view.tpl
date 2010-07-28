<h3 id="leave-comment">Leave a Comment</h3>

<p>
    {if $status}
        {if $status == 'invalid'}
            Please correct the error(s) below:
        {elseif $status == 'error'}
            An error occured whilst submitting your comment.
        {elseif $status == 'ok'}
            Thank you for your comment.
        {/if}
    {else}
        Complete the form below to leave a comment:
    {/if}
</p>

{if $status != 'ok'}
    <form action="#leave-comment" method="post">
        
        <p>
            <label>Name</label>
            <input type="text" name="name" value="{$data->getName()|escape}" size="50" />
            {assign var=control value=$data->getControls("name")}
            {if $control->getError()}<small>{$control->getError()|escape}</small>{/if}
        </p>
        
        <p>
            <label>Email</label>
            <input type="text" name="email" value="{$data->getEmail()|escape}" size="50" />
            {assign var=control value=$data->getControls("email")}
            {if $control->getError()}<small>{$control->getError()|escape}</small>{/if}
        </p>
        
        <p>
            <label>Website</label>
            <input type="text" name="website" value="{$data->getWebsite()|escape}" size="50" />
            {assign var=control value=$data->getControls("website")}
            {if $control->getError()}<small>{$control->getError()|escape}</small>{/if}
        </p>
        
        <p>
            <label>Message</label>
            <textarea name="content" cols="70" rows="5">{$data->getContent()|escape}</textarea>
            {assign var=control value=$data->getControls("content")}
            {if $control->getError()}<small>{$control->getError()|escape}</small>{/if}
        </p>
        
        <p>
            <input type="submit" name="do" value="Leave Comment" />
        </p>
        
    </form>
{/if}
