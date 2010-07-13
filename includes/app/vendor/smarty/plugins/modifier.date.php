<?php

function smarty_modifier_date($timestamp, $format="d F Y H:i")
{
    return ($timestamp ? date($format, $timestamp) : "");
}
