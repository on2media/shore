{$welcome}

{if !$edit_only}
    <form action="edit/new/" method="get">
        
        <p>
            <input type="submit" value="Add New" />
        </p>
        
    </form>
{/if}

<form action="" method="post">
    
    <p class="floated">
        {if $data->getCollection()->count() > 0 && !$no_delete}
            Select: <span class="cb_sel_grid">
                <a href="#" rel="all">All</a>
                <a href="#" rel="range">Range</a>
                <a href="#" rel="invert">Invert</a>
                <a href="#" rel="none">None</a>
            </span>
        {else}
            &nbsp;
        {/if}
    </p>
    
    <p class="floated rgt">
        Records per page: <select name="pp">
            <option value="10"{if $per_page == 10} selected="selected"{/if}>10</option>
            <option value="20"{if $per_page == 20} selected="selected"{/if}>20</option>
            <option value="50"{if $per_page == 50} selected="selected"{/if}>50</option>
            <option value="100"{if $per_page == 100} selected="selected"{/if}>100</option>
            <option value="250"{if $per_page == 250} selected="selected"{/if}>250</option>
        </select>
        &nbsp;
        <input type="submit" name="do" value="Update" />
    </p>
    
    <table class="grid">
        
        <thead>
            
            <tr>
                {if !$no_delete}<th>&nbsp;</th>{/if}
                {foreach from=$data->getGridHead() key=pos item=th}
                    <th>
                        {if $th.sortable}
                            
                            {capture assign=asc}{$pos}a{/capture}
                            {capture assign=desc}{$pos}d{/capture}
                            {capture assign=class}{if $smarty.get.s == $asc}asc{elseif $smarty.get.s == $desc}desc{/if}{/capture}
                            
                            <a href="?s={$pos}{if !$class || $class == 'desc'}a{else}d{/if}{if $filter_str}&amp;{$filter_str}{/if}"{if $class} class="{$class}"{/if}>{$th.heading|escape}</a>
                            
                        {else}
                            
                            {$th.heading|escape}
                            
                        {/if}
                    </th>
                {/foreach}
                <th>&nbsp;</th>
            </tr>
            
            {assign var=hasFilters value=false}{capture assign=filters}
                <tr class="filters">
                    {if !$no_delete}<th>&nbsp;</th>{/if}
                    {foreach from=$data->getGridHead() key=pos item=th}
                        <th>
                            {if $th.filter}
                                
                                {assign var=hasFilters value=true}
                                
                                {capture assign=fPos}f{$pos}{/capture}
                                {capture assign=fVal}{if $smarty.get.$fPos}{$smarty.get.$fPos}{/if}{/capture}
                                
                                {if $th.filter.type == 'freetext'}
                                    
                                    <input type="text" name="filter[{$pos}]" value="{$fVal|escape}" />
                                    
                                {elseif $th.filter.type == 'dropdown'}
                                    
                                    <select name="filter[{$pos}]">
                                        <option value="0">&nbsp;</option>
                                        {foreach from=$th.filter.options key=val item=opt}
                                            <option value="{$val|escape}"{if $val == $fVal} selected="selected"{/if}>{$opt}</option>
                                        {/foreach}
                                    </select>
                                    
                                {/if}
                                
                            {else}
                                
                                &nbsp;
                                
                            {/if}
                        </th>
                    {/foreach}
                    <th>
                        {if $smarty.get.s|escape}<input type="hidden" name="s" value="{$smarty.get.s|escape}" />{/if}
                        <input type="submit" name="do" value="Filter" />
                    </th>
                </tr>
            {/capture}{if $hasFilters}{$filters}{/if}
            
        </thead>
        
        <tbody>
            
            {if $data->getCollection()->count() == 0}
                
                <tr>
                    <td colspan="{count($data->getGridHead())+2}">
                        No records found.<br />
                        <a href="{$base}{$here}">Reset Filters</a> |
                        <a href="{$base}{$here}?{$filter_str}">Return to First Page</a>
                    </td>
                </tr>
                
            {else}
                
                {foreach from=$data->getCollection() key=id item=row}
                    <tr>
                        {if !$no_delete}
                            <td>
                                <input type="checkbox" name="items[]" value="{$row->uid()}" class="cb_sel_grid" />
                            </td>
                        {/if}
                        {foreach from=$data->getGridHead() item=th}
                            <td>{strip}
                                {assign var=func value=$th.field}
                                {if $row->$func()}
                                    {$th.prefix}
                                    {if $row->$func()|is_bool}
                                        {if $row->$func() == true}
                                            <img src="{$base}core/assets/img/ico/tick.png" width="16" height="16" alt="Yes" />
                                        {else}
                                            <img src="{$base}core/assets/img/ico/cross.png" width="16" height="16" alt="No" />
                                        {/if}
                                    {else}
                                        {$row->$func()}
                                    {/if}
                                    {$th.suffix}
                                {/if}
                            {/strip}</td>
                        {/foreach}
                        <td>
                            {if $row->uid() && $action !== false}
                                {if $action|is_array}
                                    {foreach from=$action key=act item=descr name=actions}
                                        {if !$smarty.foreach.actions.first}|{/if}
                                        <a href="{$act}/{$row->uid()|escape}/">{$descr|escape}</a>
                                    {/foreach}
                                {else}
                                    <a href="{if $action}{$action}{else}edit{/if}/{$row->uid()|escape}/">{if $action}{$action|ucwords}{else}Edit{/if}</a>
                                {/if}
                                {if $add_similar}| <a href="add-similar/{$row->uid()|escape}/">Add Similar</a>{/if}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                
            {/if}
            
        </tbody>
        
    </table>
    
    {if $data->getCollection()->count() > 0}
        
        {capture assign=url}?p=%d{if $filter_str}&amp;{$filter_str}{/if}{if $smarty.get.s}&amp;s={$smarty.get.s|escape}{/if}{/capture}
        {$data->getCollection()->paginate(true, 6, '&lt;', '&gt;', $url)}
        
        {if !$no_delete}
            <p>
                <input type="submit" name="do" value="Delete Selected" class="delete" />
            </p>
        {/if}
        
    {/if}
    
</form>
