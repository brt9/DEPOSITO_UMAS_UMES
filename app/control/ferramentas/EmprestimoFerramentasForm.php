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

        $uniq = new THidden('uniq[]');

        $id = new TCombo('combo[]');
        $id->enableSearch();
        $id->addItems(['1' => 1, '2' => 2, '3' => 3, '4' => '<b>Four</b>', '5' => '<b>Five</b>']);
        $id->setSize('100%');

        $text = new TEntry('text[]');
        $text->setSize('100%');

        $number = new TEntry('number[]');
        $number->setNumericMask(2, ',', '.', true);
        $number->setSize('100%');
        $number->style = 'text-align: right';

        $date = new TDate('date[]');
        $date->setSize('100%');

        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Combo</b>',  $id,  ['width' => '25%']);
        $this->fieldlist->addField('<b>Text</b>',   $text,   ['width' => '25%']);
        $this->fieldlist->addField('<b>Number</b>', $number, ['width' => '25%']);
        $this->fieldlist->addField('<b>Date</b>',   $date,   ['width' => '25%']);

        // $this->fieldlist->setTotalUpdateAction(new TAction([$this, 'x']));

        $this->fieldlist->enableSorting();

        $this->form->addField($id);
        $this->form->addField($text);
        $this->form->addField($number);
        $this->form->addField($date);


        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addCloneAction();

        // add field list to the form
        $this->form->addContent([$this->fieldlist]);

        // form actions
        $this->form->addAction('Save', new TAction([$this, 'onSave']), 'fa:save blue');
        $this->form->addAction('Clear', new TAction([$this, 'onClear']), 'fa:eraser red');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
