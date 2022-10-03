<?php

use Adianti\Base\TStandardForm;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TSpinner;

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
class CadastroFerramentasForm extends TStandardForm
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
        $this->setActiveRecord('Ferramentas');

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Cadastro de ferramentas');
        $this->form->generateAria(); // automatic aria-label
        
        // create the form fields
        $id          = new THidden('id');
        $nome = new TEntry('nome');
        $quantidade    = new TSpinner('quantidade');
                
        $id->setEditable(FALSE);

        // disable dates (bootstrap date picker
        
        // add the fields inside the form
        $this->form->addFields( [new TLabel('Nome')], [$nome] );
        $this->form->addFields( [new TLabel('Quantidade')],    [$quantidade] );
        
        $nome->placeholder = 'Nome do matérial';
        $quantidade->placeholder = 'Informe a quantidade';
        //$nome->setTip('Quantidade de materiais');

        // define the form action 
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'),new TAction(array('FerramentasList','onReload')),'far:arrow-alt-circle-left blue');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     * Post data
     */
    public function onSend($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data);
        
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}