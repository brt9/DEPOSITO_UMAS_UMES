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
class lista extends TRecord
{
    const TABLENAME = 'ESTOQUE_UMAS_UMES';
    const PRIMARYKEY = 'CODIGO';
    const IDPOLICY = 'max'; // {max, serial}
    
    CONST CREATEDAT = 'created_at';
    CONST UPDATEDAT = 'updated_at';
    CONST DELETEDAT = 'deleted_at';

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODIGO');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('QUANTIDADE_ESTOQUE');
        parent::addAttribute('COLABORADOR_RESPONSAVEL_CADASTRO');
    }
    
}
