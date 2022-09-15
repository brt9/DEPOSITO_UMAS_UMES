<?php

use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;

/**
 * CadastroForm
 *
 * @version    1.0
 * @package    model
 * @subpackage Jogos Olimpicos Tokoyo 2020
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class CadastroForm extends TStandardForm
{
    protected $form; // form

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        $this->setDatabase('bancodados');              // defines the database
        $this->setActiveRecord('lista');     // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_cadastro');
        $this->form->setFormTitle('Cadastro Items DEPOSITO');

        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $DESCRICAO = new TEntry('DESCRICAO');
        $QUANTIDADE_ESTOQUE = new TEntry('QUANTIDADE_ESTOQUE');
        $DATA = new TEntry('data_cadastro');
        $DATA->setValue(date("Y-m-d H:i:s"));


        $ITEM       = new TEntry('ITEM');
        /*$colaborador_responsavel = new TEntry('colaborador_responsavel');
       $data_cadastro = new TEntry('data_cadastro');
        $nome = new TEntry('nome_cliente');
        $sexo = new TDBCombo('sexo', 'bancodados', 'sexo', 'nome_sexo', 'nome_sexo');
        $telefone = new TEntry('telefone');
        $email = new TEntry('email');
        $canal = new TEntry('canal');
        $data_recebimento = new TDate('data_recebimento');
        $data_cadastro->setValue(date("Y-m-d H:i:s"));
        $fase_atual = new THidden('fase_atual');
        $fase_atual->setValue('QUALIFICACAO');*/

        // add the fields
        $this->form->addFields([new TLabel('CODIGO')], [$CODIGO]);
        $this->form->addFields([new TLabel('DESCRICAO')], [$DESCRICAO]);
        $this->form->addFields([new TLabel('QUANTIDADE_ESTOQUE')], [$QUANTIDADE_ESTOQUE]);
        $this->form->addFields([new TLabel('DATA')], [$DATA]);
        /*$this->form->addFields([new TLabel('COLABORADOR RESPONSAVEL')], [$colaborador_responsavel]);
        $this->form->addFields([new TLabel('DATA CADASTRO')], [$data_cadastro]);
        $this->form->addFields([new TLabel('NOME')], [$nome]);
        $this->form->addFields([new TLabel('SEXO')], [$sexo]);
        $this->form->addFields([new TLabel('EMAIL')], [$email]);
        $this->form->addFields([new TLabel('TELEFONE')], [$telefone]);
        $this->form->addFields([new TLabel('CANAL')], [$canal]);
        $this->form->addFields([new TLabel('DATA RECEBIMENTO')], [$data_recebimento]);
        $this->form->addFields([$fase_atual]);*/

        //$DATA->setDatabaseMask("Y-m-d H:i:s");

        /*  $CODIGO->setSize('15%');
        $CODIGO->setEditable(FALSE);
        $nome->setSize('70%');
        $data_cadastro->setSize('15%');
        $email->setSize('30%');
        $canal->setSize('30%');
        $data_recebimento->setSize('15%');
        $sexo->setSize('15%');
        $colaborador_responsavel->setSize('70%');
        $colaborador_responsavel->setValue(TSession::getValue('username'));
        $colaborador_responsavel->setEditable(FALSE);
        $data_cadastro->setEditable(FALSE);
        $nome->setSize('70%');
        $nome->addValidation(('NOME'), new TRequiredValidator);
        $nome->forceUpperCase();
        $telefone->setSize('30%');
        $telefone->setMask('+(99) 99999-9999');*/

        /*
        $numero->setSize('25%');
        $complemento->setSize('50%');
        $cep->setMask('99.999-999');
  */
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('Clear'),  new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'), new TAction(array('CadastroList', 'onReload')), 'far:arrow-alt-circle-left blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CadastroList'));
        $container->add($this->form);

        parent::add($container);
    }
}
