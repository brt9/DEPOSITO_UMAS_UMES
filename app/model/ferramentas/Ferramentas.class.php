<?php

use Adianti\Database\TRecord;

class Ferramentas extends TRecord
{
    const TABLENAME = 'ferramentas';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    const CREATEDAT = 'created_at';
    const UPDATEDAT = 'updated_at';
    const DELETEDAT = 'deleted_at';

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id');
        parent::addAttribute('nome');
        parent::addAttribute('quantidade');

    }
}
