<?php

use Adianti\Base\TStandardForm;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Wrapper\TDBUniqueSearch;

/**
 * FormNestedBuilderView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class EmprestimoFerramentasForm extends TStandardForm
{
    protected $form;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('bancodados');
        $this->setActiveRecord('Emprestimo');

        // create form and table container
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle("Solicitação de emprestimo");

        $ferramenta = new TDBUniqueSearch('EmprestimoFerramentasForm','bancodados','ferramentas','id','{id} - {nome}');
        $ferramenta->placeholder ='Pesquise pela ferramenta desejada';
        $ferramenta->setSize('100%');

        $quantidade = new TSpinner('quantidade[]');
        $quantidade->setSize('100%');

        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color:"red">*</font>',  $ferramenta,  ['width' => '70%']);
        $this->fieldlist->addField('<b>Quantidade</b>',   $quantidade,   ['width' => '10%']);

        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);


        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addCloneAction();

        // add field list to the form
        $this->form->addContent([$this->fieldlist]);

        // form actions
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';
        $btnClear = $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser white');
        $btnClear->style = 'background-color:#c73927; color:white';
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%;';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
