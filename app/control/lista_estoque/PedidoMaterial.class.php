<?php
/**
 * LISTA DE PEDIDO DE MATERIAS
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class PedidoMaterial extends TPage
{
    private $form;
    private $fieldlist;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // create form and table container
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle(('FORMULARIO DE PEDIDO DE MATERIAL'));
        
        $codigo = new TDBCombo('CODIGO', 'bancodados', 'lista', 'CODIGO', 'CODIGO');
        $codigo->enableSearch();
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
  
    
    /**
     * Clear form
     */
    public static function onClear($param)
    {
        TFieldList::clear('my_field_list');
        TFieldList::addRows('my_field_list', 4);
    }
    
   
    /**
     * Save simulation
     */
    public static function onSave($param)
    {
        // show form values inside a window
        $win = TWindow::create('test', 0.6, 0.8);
        $win->add( '<pre>'.str_replace("\n", '<br>', print_r($param, true) ).'</pre>'  );
        $win->show();
    }
}
