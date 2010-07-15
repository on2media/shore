<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        
        <title>{if $page_title}{$page_title|escape} &laquo; {/if}Glooware Blog</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <link rel="stylesheet" type="text/css" href="{$base}assets/core.css" />
        
        <script type="text/javascript" src="{$base}assets/moo-core.js"></script>
        <script type="text/javascript" src="{$base}assets/core.js"></script>
        
    </head>

    <body>
        
        <div id="wrapper">
            
            <div id="header">
                
                <ul>
                    <li><a href="{$base}">Latest Posts</a></li>
                    <li><a href="{$base}page/2/">About Us</a></li>
                </ul>
                
                <p>
                    <big><a href="{$base}">Glooware Blog</a></big>
                </p>
                
            </div>
            
            <div id="content">
                
                {$content}
                
            </div>
            
            <div id="side">
                
                {$sidebar}
                
            </div>
            
            <div class="spacer"></div>
            
        </div>
        
        <div id="footer">
            
            <p class="lft">
                &copy;2010, Glooware Ltd.
            </p>
            
            <p class="rgt">
                Site by <a href="http://www.glooware.com/">Glooware</a>
            </p>
            
        </div>
        
    </body>

</html>
