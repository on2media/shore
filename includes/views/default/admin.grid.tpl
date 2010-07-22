{if $data->getCollection()->count() == 0}
    
    <p>
        No records found.
    </p>
    
{else}
    
    <form action="edit/add" method="get">
        
        <p>
            <input type="submit" value="Add New" />
        </p>
        
    </form>
    
    <form action="delete/" method="post">
        
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
                            <input type="checkbox" name="items" value="" />
                        </td>
                        {foreach from=$data->getGridHead() item=th}
                            <td>
                                {assign var=func value=$th.field}
                                {if $row->$func()|is_bool}
                                    {if $row->$func() == true}
                                        <img src="{$base}assets/img/ico/tick.png" width="16" height="16" alt="Yes" />
                                    {else}
                                        <img src="{$base}assets/img/ico/cross.png" width="16" height="16" alt="No" />
                                    {/if}
                                {else}
                                    {$row->$func()}
                                {/if}
                            </td>
                        {/foreach}
                        <td>
                            <a href="edit/{$row->uid()|escape}/">Edit</a>
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