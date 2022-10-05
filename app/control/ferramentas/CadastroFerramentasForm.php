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
        $this->form->addFields(
            [new TLabel('Nome')],
            [$nome],
            [new TLabel('Quantidade')],
            [$quantidade],
        );

        $nome->setSize('100%');
        $quantidade->setSize('20%');

        $nome->placeholder = 'Nome do matérial';
        $quantidade->setTip = ('Informe a quantidade de materiais');

        // define the form action 
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save black');
        $btnSave->class = 'btn btn-sm btn-success';
        $btnClear = $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser black');
        $btnClear->class = 'btn btn-sm btn-danger';
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('FerramentasList', 'onReload')), 'far:arrow-alt-circle-left black');
        $btnBack->class = 'btn btn-sm btn-secondary';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
