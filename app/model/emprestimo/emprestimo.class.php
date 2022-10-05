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
class emprestimo extends TRecord
{
    const TABLENAME = 'EMPRESTIMO_FERRAMENTAS';
    const PRIMARYKEY = 'ID_EMPRESTIMO';
    const IDPOLICY = 'max'; // {max, serial}


    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ID_EMPRESTIMO');
        parent::addAttribute('FERRAMENTA');
        parent::addAttribute('DATA_EMPRESTIMO');
        parent::addAttribute('DATA_DEVOLUCAO');
        parent::addAttribute('COLABORADOR_RESPONSAVEL_EMPRESTIMO');
        parent::addAttribute('MATRICULA');
    }
}
