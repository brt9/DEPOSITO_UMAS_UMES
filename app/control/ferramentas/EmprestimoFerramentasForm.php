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
    protected $xlogin;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();

        // create form and table container
        $this->form = new BootstrapFormBuilder('form_SaleMultiValue');
        $this->form->setFormTitle("Solicitação de emprestimo");

        $id             = new TEntry('id');
        $ferramenta = new TDBUniqueSearch('ferramenta', 'bancodados', 'Ferramentas', 'id', 'nome', 'nome');
        $quantidade = new TSpinner('quantidade[]');

        $ferramenta->placeholder = 'Pesquise pela ferramenta desejada';
        $ferramenta->setMinLength(1);
        $ferramenta->setSize('100%');
        $quantidade->setSize('100%');
        $id->setEditable(FALSE);
        $id->setSize('20%');

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '70%']);
        $this->fieldlist->addField('<b>Quantidade</b><font color="red">*</font>',   $quantidade,   ['width' => '10%']);

        $ferramenta->setTip('Campo obrigatório');
        $quantidade->setTip('Campo obrigatório');
        
        $this->form->addFields( [new TLabel('id')], [$id] );
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
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');
            if (isset($param['id'])) {
                $this->form->validate(); // validate form data
                
                $object = new Emprestimo();  // create an empty object
                $data = $this->form->getData(); // get form data as array
                $object->fromArray((array) $data); // load the object with data
                $object->id_usuario = TSession::getValue('userid');
                $object->id_status = 1;
                $object->store(); // save the object
                
                // get the generated id
                $data->id = $object->id;
            }
            var_dump($data);
             //$obj_pivots = objpivot::where('obj_id', '=', $obj->id)->delete();
            
            //  if( !empty($param['product_id']) AND is_array($param['id']) )
            //  {
                foreach( $data as $id)
                {
                        $pivot = new PivotEmprestimoFerramentas();
                        $pivot->id_emprestimo = $data->id;
                        $pivot->id_ferramenta  = $param['ferramenta'];                        
                        $pivot->store();
                }
            // } 
            
            TTransaction::close(); // close the transaction
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {

                $id = $param['key'];
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
