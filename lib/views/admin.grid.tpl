{$welcome}

{if !$edit_only}
    <form action="edit/new/" method="get">
        
        <p>
            <input type="submit" value="Add New" />
        </p>
        
    </form>
{/if}

{if $data->getCollection()->count() == 0}
    
    <p>
        No records found.
    </p>
    
{else}
    
    <p>
        Select: <span class="cb_sel_grid">
            <a href="#" rel="all">All</a>
            <a href="#" rel="range">Range</a>
            <a href="#" rel="invert">Invert</a>
            <a href="#" rel="none">None</a>
        </span>
    </p>
    
    <form action="./" method="post">
        
        <table class="grid">
            
            <thead>
                <tr>
                    <th>
                        &nbsp;
                    </th>
                    {foreach from=$data->getGridHead() item=th}
                        <th>
                            {$th.heading|escape}
                        </th>
                    {/foreach}
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
            
            <tbody>
                
                {foreach from=$data->getCollection() key=id item=row}
                    <tr>
                        <td>
                            <input type="checkbox" name="items[]" value="{$row->uid()}" class="cb_sel_grid" />
                        </td>
                        {foreach from=$data->getGridHead() item=th}
                            <td>
                                {assign var=func value=$th.field}
                                {if $row->$func()|is_bool}
                                    {if $row->$func() == true}
                                        <img src="{$base}core/assets/img/ico/tick.png" width="16" height="16" alt="Yes" />
                                    {else}
                                        <img src="{$base}core/assets/img/ico/cross.png" width="16" height="16" alt="No" />
                                    {/if}
                                {else}
                                    {$row->$func()}
                                {/if}
                            </td>
                        {/foreach}
                        <td>
                            <a href="edit/{$row->uid()|escape}/">Edit</a>
                            {if $add_similar}| <a href="add-similar/{$row->uid()|escape}/">Add Similar</a>{/if}
                        </td>
                    </tr>
                {/foreach}
                
            </tbody>
            
        </table>
        
        <p>
            <input type="submit" name="do" value= "Delete Selected" class="delete" />
        </p>
        
    </form>
    
{/if}