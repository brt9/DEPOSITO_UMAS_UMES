<?php

use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;

/**
 * FORMULÁRIO DE CADASTRO DE MATERIAL
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class PedidoMaterial extends TStandardForm
{
    protected $form; //  FORMULÁRIO

    // CONSTRUTOR DE CLASSE
    // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

    function __construct()
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        $this->setDatabase('bancodados');              // DEFINE O BANCO DE DADOS
        $this->setActiveRecord('pedido');               // DEFINE O REGISTRO ATIVO

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle('FORMULARIO DE PEDIDO DE MATERIAL');


        $codigo = new TQRCodeInputReader('id_item');
        $codigo->setSize('100%');

        $combo = new TDBCombo('descricao', 'bancodados', 'lista', 'descricao', 'descricao');
        $combo->enableSearch();
        $combo->setSize('100%');


        $text = new TEntry('quantidade');
        $text->setSize('100%');

        $id_status = new TEntry('id_status');
        $id_status->setSize('100%');


        $id_usuario = new TEntry('id_usuario');
        $id_usuario->setSize('100%');



        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>STATUS</b>',   $id_status,   ['width' => '20%']);
        $this->fieldlist->addField('<b>CODIGO ITEM</b>',  $codigo,  ['width' => '20%']);
        $this->fieldlist->addField('<b>DESCRIÇÂO</b>',  $combo,  ['width' => '20%']);
        $this->fieldlist->addField('<b>QUANTIDADE</b>',   $text,   ['width' => '20%']);
        $this->fieldlist->addField('USUARIO', $id_usuario, ['width' => '42%']);

        $this->form->addField($codigo);
        $this->form->addField($id_status);
        $this->form->addField($combo);
        $this->form->addField($text);
        $this->form->addField($id_usuario);


        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addHeader();
        $this->fieldlist->addCloneAction();

        // add field list to the form
        $this->form->addContent([$this->fieldlist]);
        $id_usuario->setValue(TSession::getValue('userid'));
        // form actions
        $this->form->addAction('SALVAR', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('LIMPAR', new TAction([$this, 'onClear']), 'fa:eraser red');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);

        parent::add($vbox);
    }
}
