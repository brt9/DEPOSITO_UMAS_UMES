<?php

use Adianti\Base\TStandardForm;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Form\TText;

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
class CadastroFerramentasForm extends TPage
{
    protected $form;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();



        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Cadastro de ferramentas');
        $this->form->generateAria(); // automatic aria-label

        // create the form fields
        $id = new TEntry('id');
        $nomeFerramenta = new TEntry('nome');

        $quantidade    = new TSpinner('quantidade');

        $id->setEditable(FALSE);
        // add the fields inside the form
        $row = $this->form->addFields( [new TLabel('Id')],    [$id] );
        $id->setSize('20%');

        $row = $this->form->addFields(
            [$labelFerramenta = new TLabel('Ferramenta <font color="red">*</font>')],[$nomeFerramenta],
            [$labelQuantidade = new TLabel('Quantidade <font color="red">*</font>')],[$quantidade],
        );
        $row->style = 'align-items: center';

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('<font color="red">ATENÇÃO</font> Ao cadastrar uma ferramenta, 
            caso queira continuar cadastrando outras ferramentas, basta clicar no botão de "Limpar"
            e continuar cadastrando')],
        );
        $row->style = 'margin-top: 3rem; text-align: center';

        //Style in form
        $labelFerramenta->setTip('Campo obrigatório');
        $labelQuantidade->setTip('Campo obrigatório');
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
    public function onSave( $param )
    {
        try
        {
            if(!isset($param['id'])){
                TTransaction::open('bancodados'); // open a transaction
                $this->form->validate(); // validate form data
                
                $object = new Ferramentas();  // create an empty object
                $data = $this->form->getData(); // get form data as array
                $object->fromArray( (array) $data); // load the object with data
                $object->store(); // save the object
                
                // get the generated id
                $data->id = $object->id;
            }
            
            $this->form->setData($data); // fill form data
            
            $this->fireEvents( $object );
            
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Ferramenta cadastrada'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onEdit($param) {

    }
    public function onClear($param) {

    }
}
