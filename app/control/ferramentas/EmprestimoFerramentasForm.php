<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
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
class EmprestimoFerramentasForm extends TPage
{
    protected $form;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();

        // create form and table container
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle("Solicitação de emprestimo");

        $user_id = TSession::getValue('username');
        $xlogin = new TEntry($user_id);
        $xlogin->setValue($user_id);
        var_dump($user_id);

        $ferramenta = new TDBUniqueSearch('ferramenta', 'bancodados', 'Ferramentas', 'id', 'nome', 'nome');
        $ferramenta->placeholder = 'Pesquise pela ferramenta desejada';
        $ferramenta->setMinLength(1);
        $ferramenta->setSize('100%');

        $quantidade = new TSpinner('quantidade[]');
        $quantidade->setSize('100%');

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '70%']);
        $this->fieldlist->addField('<b>Quantidade</b><font color="red">*</font>',   $quantidade,   ['width' => '10%']);

        $ferramenta->setTip('Campo obrigatório');
        $quantidade->setTip('Campo obrigatório');

        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);

        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addCloneAction();

        // add field list to the form
        $this->form->addContent([$this->fieldlist]);

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%;';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    public function onSave($param)
    {

        try {
            TTransaction::open('bancodados'); // open a transaction
            if (isset($param['id'])) {
                $this->form->validate(); // validate form data

                $object = new Emprestimo();  // create an empty object
                $data = $this->form->getData(); // get form data as array
                $object->fromArray((array) $data); // load the object with data

                PivotEmprestimoFerramentas::where('id', '=', $param['id'])->delete();

                if (isset($param['key'])) {
                    foreach ($param['key'] as $key) {
                        $pivot = new PivotEmprestimoFerramentas();
                        $pivot->ferramentas = $key;
                    }
                }
                $object->store(); // save the object

                $username = TSession::getValue('userid');
                // get the generated id
                $data->id = $object->id;
                $username = $object->id_usuario;
            }
            $this->form->setData($data); // fill form data


            TTransaction::close(); // close the transaction

            new TMessage('info', 'Emprestimo solicitado');
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData($this->form->getData()); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {

                $id = $param['key'];
                var_dump($id);
                TTransaction::open('bancodados');
                $object = new Ferramentas($id);

                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onClear($param)
    {
    }
}
