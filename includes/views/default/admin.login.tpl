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
