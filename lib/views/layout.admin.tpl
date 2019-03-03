<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <title>{$page_title|escape} &laquo; Admin</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="{$base}core/assets/admin.css" />
        <link rel="stylesheet" type="text/css" href="{$base}core/assets/datepicker.css" />

        <script type="text/javascript">var base = '{$base}';</script>

        <script type="text/javascript" src="{$base}core/assets/vendor/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="{$base}assets/editor.js"></script>

        <script type="text/javascript" src="{$base}core/assets/moo-core.js"></script>
        <script type="text/javascript" src="{$base}core/assets/datepicker.js"></script>
        <script type="text/javascript" src="{$base}core/assets/admin.js"></script>

    </head>

    <body>

        <div id="header">

            <ul>
                <li><a href="{$base}{$admin}">Home</a></li>
            </ul>

        </div>

        <div id="wrapper">

            <div id="user">
                {if $current_user}
                    Current User: <strong>{$current_user->getName()|escape}</strong>
                    {assign var=authSuperUser value=$smarty.const.AUTH_SUPERUSER}
                    {if $current_user->$authSuperUser}(Super User){/if}
                {else}
                    Not Logged In
                {/if}
            </div>

            <h1>{$page_title|escape}</h1>

            {if $status_confirm}<div class="status confirm">{$status_confirm}</div>{/if}
            {if $status_info}<div class="status info">{$status_info}</div>{/if}
            {if $status_alert}<div class="status alert">{$status_alert}</div>{/if}

            {$content}

        </div>

    </body>

</html>
