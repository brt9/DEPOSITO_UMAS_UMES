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
    function __construct()
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_SaleMultiValue');
        $this->form->setFormTitle('Aprovar solicitação de material');
        $this->form->generateAria(); // automatic aria-label

        // create the form fields
        $id             = new TEntry('id');
        $created             = new TDateTime('created_at');
        $ferramenta = new TDBCombo('ferramenta[]', 'bancodados', 'Ferramentas', 'id', 'nome', 'nome');
        $quantidade = new TSpinner('quantidade[]');
        $qtd_emprestada = new TSpinner('qtd_emprestada[]');

        $id->setSize('30%');
        $ferramenta->setSize('100%');
        $quantidade->setSize('100%');
        $created->setSize('70%');

        $id->setEditable(FALSE);
        $created->setEditable(FALSE);
        $quantidade->setEditable(FALSE);
        $ferramenta->setEditable(FALSE);

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b>',  $ferramenta,  ['width' => '70%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Quantidade solicitada</b>',   $quantidade,   ['width' => '50%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Quantidade emprestada</b>',   $qtd_emprestada,   ['width' => '10%'], new TRequiredValidator);

        $row = $this->form->addFields(
            [new TLabel('id')],
            [$id],
            [new TLabel('created_at')],
            [$created],
        );
        $row->style = 'margin-bottom: 4rem; align-items: center';

        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);
        $this->form->addField($qtd_emprestada);

        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail(new stdClass);

        // add field list to the form
        $this->form->addContent([$this->fieldlist]);

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        /*         $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white'; */

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%; margin:40px';
        $container->add($this->form);

        parent::add($container);
    }

    public function onShow($param)
    {
    }
}
