<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <title>{$page_title|escape} &laquo; Admin</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="{$base}shore-assets/admin.css" />

    </head>

    <body>

        <div id="header">

            <ul>
                <li><a href="{$base}">View Site</a></li>
            </ul>

        </div>

        <div id="wrapper">

            <div id="user">
                Not Logged In
            </div>

            <h1>{$page_title|escape}</h1>

            {if $status_confirm}<div class="status confirm">{$status_confirm}</div>{/if}
            {if $status_info}<div class="status info">{$status_info}</div>{/if}
            {if $status_alert}<div class="status alert">{$status_alert}</div>{/if}

            <form action="" method="post">

                <p>
                    <label>{$label_u}</label>
                    <input type="text" name="u" value="{$smarty.post.u|escape}" size="40" />
                </p>

                <p>
                    <label>{$label_p}</label>
                    <input type="password" name="p" value="{$smarty.post.p|escape}" size="40" />
                </p>

                <p>
                    <input type="submit" name="do" value="Login" />
                </p>

            </form>

        </div>

    </body>

</html>
