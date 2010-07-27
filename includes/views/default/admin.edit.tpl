{if count($data->getEditForm()) == 0}
    
    <p>
        No record found.
    </p>
    
{else}

    <form action="" method="post">
    
    {foreach from=$data->getEditForm() item=control}
        {$control->output()}
    {/foreach}
    
    <p>
        <input type="submit" value="Save Changes" />
    </p>
    
    </form>
    
{/if}
