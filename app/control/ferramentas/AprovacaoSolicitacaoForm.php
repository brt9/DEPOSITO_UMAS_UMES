<?php

use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Sabberworm\CSS\Value\Value;

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
class AprovacaoSolicitacaoForm extends TPage
{
    protected $form;
    protected $fieldlist;
    protected $datagrid;
    protected $pageNavigation;

    /**
     * Class constructor
     * Creates the page
     */
    function __construct($param = null)
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_SaleMultiValue');
        $this->form->setFormTitle('Aprovar solicitação de material');

        // create the form fields
        $id             = new TEntry('id');
        $created             = new TDateTime('created_at');
        $ferramenta = new TEntry('ferramenta[]');
        $quantidade = new TEntry('quantidade[]');
        $qtdEmprestada = new TEntry('emprestada[]');

        //Config dos campos
        $id->setSize('20%');
        $id->setEditable(FALSE);

        $created->setSize('70%');
        $created->setEditable(FALSE);

        $ferramenta->setSize('90%');
        $ferramenta->setEditable(FALSE);

        $quantidade->setSize('100%');
        $quantidade->setEditable(FALSE);

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '90%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Qtd solicitada</b><font color="red">*</font>',   $quantidade,   ['width' => '100%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Qtd emprestada</b><font color="red">*</font>',   $qtdEmprestada,   ['width' => '10%'], new TRequiredValidator);

        $this->form->addFields(
            [new TLabel('Id da solicitação')],
            [$id],
            [new TLabel('Data da solicitação')],
            [$created],
        );

        //add itens ao field list
        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);
        $this->form->addField($qtdEmprestada);
        $this->fieldlist->disableRemoveButton();

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%; margin:40px';
        $container->add($this->form);

        parent::add($container);
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('bancodados');
                $emprestimo = Emprestimo::find($param['key']);
                $this->form->setData($emprestimo); //inserindo dados no formulario. 

                $pivot = PivotEmprestimoFerramentas::where('id_emprestimo', '=', $emprestimo->id)->load();

                if ($pivot) {
                    $this->fieldlist->addHeader();
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        $obj->id_ferramenta = intval($value->id_ferramenta);
                        $obj->quantidade = $value->quantidade;

                        $this->fieldlist->addDetail($obj);
                    }
                }
                // add field list to the form
                $this->form->addContent([$this->fieldlist]);
                TTransaction::close();
            } else {
                $this->onClear($param);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onSave($param)
    {
    }
}
