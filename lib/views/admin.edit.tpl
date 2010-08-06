{if count($data->getControls()) == 0}
    
    <p>
        No record found.
    </p>
    
{else}

    <form action="" method="post">
    
    {foreach from=$data->getControls() item=control}
        {$control->output()}
    {/foreach}
    
    <p>
        <input type="submit" value="Save Changes" />
    </p>
    
    </form>
    
{/if}
