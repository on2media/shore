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

<div class="status info">The button below is just for testing.</div>

<form action="" method="post">
    <p>
        <input type="hidden" name="test" value="123" />
        <input type="submit" value="Send Empty Post" />
    </p>
</form>