<?php

use Adianti\Database\TRecord;

class Emprestimo extends TRecord
{
    const TABLENAME = 'emprestimo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

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
        parent::addAttribute('id_usuario');
        parent::addAttribute('id_admin');
        parent::addAttribute('id_status');

    }
}
