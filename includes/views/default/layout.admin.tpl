<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        
        <title>{$page_title|escape} &laquo; Glooware Blog Admin</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <link rel="stylesheet" type="text/css" href="{$base}assets/admin.css" />
        <link rel="stylesheet" type="text/css" href="{$base}assets/datepicker.css" />
        
        <script type="text/javascript">var base = '{$base}';</script>
        
        <script type="text/javascript" src="{$base}assets/vendor/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="{$base}assets/editor.js"></script>
        
        <script type="text/javascript" src="{$base}assets/moo-core.js"></script>
        <script type="text/javascript" src="{$base}assets/datepicker.js"></script>
        <script type="text/javascript" src="{$base}assets/admin.js"></script>
        
    </head>

    <body>
        
        <div id="header">
            
            <ul>
                <li><a href="{$base}{$admin}">Home</a></li>
                <li><a href="{$base}{$admin}posts/">Posts</a></li>
                <li><a href="{$base}{$admin}comments/">Comments</a></li>
                <li><a href="{$base}{$admin}tags/">Tags</a></li>
                <li><a href="{$base}{$admin}authors/">Authors</a></li>
            </ul>
            
        </div>
        
        <div id="wrapper">
            
            <h1>{$page_title|escape}</h1>
            
            {if $status_confirm}<div class="status confirm">{$status_confirm}</div>{/if}
            {if $status_info}<div class="status info">{$status_info}</div>{/if}
            {if $status_alert}<div class="status alert">{$status_alert}</div>{/if}
            
            {$content}
            
        </div>
        
    </body>

</html>
