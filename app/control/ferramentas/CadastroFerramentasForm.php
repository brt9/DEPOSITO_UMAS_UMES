<?php

use Adianti\Base\TStandardForm;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
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
        $id          = new TEntry('id');
        $nomeFerramenta = new TEntry('nome');
        $quantidade    = new TSpinner('quantidade');

        $id->setEditable(FALSE);

        // disable dates (bootstrap date picker

        // add the fields inside the form
        $row = $this->form->addFields(
            [$labelInfo = new TLabel('<font color="red">ATENÇÃO</font>')],
            [$labelID = new TLabel('Id')],[$id],
        );
        
        $row = $this->form->addFields(
            [$labelFerramenta = new TLabel('Ferramenta <font color="red">*</font>')],[$nomeFerramenta],
            [$labelQuantidade = new TLabel('Quantidade <font color="red">*</font>')],[$quantidade],
        );
        //Style in form
        $labelInfo->setTip('Ao cadastrar uma ferramenta, caso queira </br>continuar 
        cadastrando outras ferramentas</br>, basta clicar no botão de "Limpar"</br> e continuar cadastrando');
        $labelFerramenta->setTip('Campo obrigatório');
        $labelQuantidade->setTip('Campo obrigatório');
        $id->setSize('50%');
        $labelFerramenta->style = 'left: -100%;';
        $nomeFerramenta->setSize('100%');
        $quantidade->setSize('20%');
        $nomeFerramenta->placeholder = 'Nome do ferramenta';
        $quantidade->setTip = ('Informe a quantidade de materiais');

        // define the form action 
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('FerramentasList', 'onReload')), 'far:arrow-alt-circle-left White');
        $btnBack->style = 'background-color:gray; color:white';
        $btnClear = $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser White');
        $btnClear->style = 'background-color:#c73927; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save White');
        $btnSave->style = 'background-color:#218231; color:white';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
