<?php

class helpers 
{
    public static function formatDate($date)
    {
        if($date == null){
            return '00/00/0000 - 00:00';
            }
        $dt = new DateTime($date);
        return $dt->format('d/m/Y  H:i:s');
    }
}