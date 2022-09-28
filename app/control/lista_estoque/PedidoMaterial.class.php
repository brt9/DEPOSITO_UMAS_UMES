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
        $this->setActiveRecord('lista');               // DEFINE O REGISTRO ATIVO

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle('FORMULARIO DE PEDIDO DE MATERIAL');
        $codigo = new TQRCodeInputReader('CODIGO');
        $codigo->setSize('100%');
        
        $combo = new TDBCombo('DESCRICAO', 'bancodados', 'lista', 'DESCRICAO', 'DESCRICAO');
        $combo->enableSearch();
        $combo->setSize('100%');

        
        $text = new TEntry('text[]');
        $text->setSize('100%');
   
     
        
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField( '<b>CODIGO ITEM</b>',  $codigo,  ['width' => '10%'] );
        $this->fieldlist->addField( '<b>DESCRIÇÂO</b>',  $combo,  ['width' => '40%'] );
        $this->fieldlist->addField( '<b>QUANTIDADE</b>',   $text,   ['width' => '40%'] );
  
        
        $this->form->addField($combo);
        $this->form->addField($text);
       

        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();
        
        // add field list to the form
        $this->form->addContent( [$this->fieldlist] );
        
        // form actions
        $this->form->addAction( 'SALVAR', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction( 'LIMPAR', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        
        parent::add($vbox);
      
    }
}
