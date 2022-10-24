<?php

use Adianti\Database\TRecord;

/**
 * SystemUnit
 *
 * @version    1.0
 * @package    model
 * @subpackage Atletas Olimpicos
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class ListaPedido extends TRecord
{
    const TABLENAME = 'pedido_material';
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
        parent::addAttribute('id_status');
        parent::addAttribute('id_usuario');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }
    public function get_User()
    {
        // loads the associated object
        if (empty($this->idUser))
            $this->idUser = new SystemUser($this->id_usuario);

        // returns the associated object
        return $this->idUser;
    }
    public function get_Status()
    {
        // loads the associated object
        if (empty($this->idStatus))
            $this->idStatus = new Status($this->id_status);

        // returns the associated object
        return $this->idStatus;
    }
}
