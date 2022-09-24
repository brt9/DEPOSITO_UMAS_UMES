<?php

/**
 *cadastroList
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class EmprestimoList extends TStandardList
{
  protected $form;     // registration form
  protected $datagrid; // listing
  protected $pageNavigation;
  protected $formgrid;
  protected $deleteButton;
  protected $transformCallback;

  /**
   * Page constructor
   */
  public function __construct()
  {
    parent::__construct();

    parent::setDatabase('bancodados');            // defines the database
    parent::setActiveRecord('emprestimo');   // defines the active record
    parent::setDefaultOrder('ID_EMPRESTIMO', 'asc');         // defines the default order
    parent::addFilterField('ID_EMPRESTIMO', '=', 'ID_EMPRESTIMO'); // filterField, operator, formField
    parent::addFilterField('FERRAMENTA', 'like', 'FERRAMENTA'); // filterField, operator, formField

    // creates the form

    $this->form = new BootstrapFormBuilder('form_search');
    $this->form->setFormTitle('HISTORICO EMPRESTIMO DE FERRAMENTAS');

    // create the form fields

    $id = new TQRCodeInputReader('ID_EMPRESTIMO');
    $FERRAMENTA = new TEntry('FERRAMENTA');
    $DATA = new TEntry('DATA_CADASTRO_ITEM');
    $DESCRICAO       = new TCombo('DESCRICAO');
    $ITEM       = new TEntry('ITEM');

    // add the fields/ aqiu

    //s    $this->form->addFields([new TLabel('ID')], [$id]);
    $this->form->addFields([new TLabel('CODIGO DO ITEM')], [$id,  new TLabel('Only supported in HTTPS mode', 'gray')]);
    $this->form->addFields([new TLabel('DESCRICAO')], [$ITEM]);
    // $this->form->addFields([new TLabel('QUANTIDADE ESTOQUE')], [$FERRAMENTA]);
    // $this->form->addFields([new TLabel('DATA')], [$DATA]);
    $DESCRICAO->addItems(['JOELHO PVC 90°' => 'JOELHO PVC 90°', 'JOELHO PVC 45°' => 'JOELHO PVC 45°', 'LUVA PVC SOLD' => 'LUVA PVC SOLD', 'LUVA FERRO FUND BIPARTIDA' => 'LUVA FERRO FUND BIPARTIDA', 'LUVA DE CORRER PVC' => 'LUVA DE CORRER PVC', 'LUVA CORRER PVC DEFOFO' => 'LUVA CORRER PVC DEFOFO']);
    $this->form->addFields([new TLabel('DESCRICAO')], [$DESCRICAO]);
    // $sexo->addItems(['MASCULINO' => 'MASCULINO', 'FEMININO' => 'FEMININO']);
    $DESCRICAO = $ITEM;
    $id->setSize('10%');
    $DESCRICAO->setSize('50%');

    // keep the form filled during navigation with session data
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // add the search form actions
    $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
    $this->form->addAction("Novo Item", new TAction(["CadastroForm", "onEdit"]), "fa:plus-circle green");
    $btn->class = 'btn btn-sm btn-primary';
    //$this->form->addAction(_t('New'),  new TAction(array('cadastroForm', 'onEdit')), 'fa:plus green');

    // creates a DataGrid
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);


    // creates the datagrid columns
    $column_id = new TDataGridColumn('CODIGO', 'CODIGO DO ITEM', 'center', 50);
    $column_DESCRICAO = new TDataGridColumn('DESCRICAO', 'DESCRICAO', 'left');
    $column_FERRAMENTA = new TDataGridColumn('FERRAMENTA', 'FERRAMENTA', 'left');
    $column_DATA = new TDataGridColumn('DATA_CADASTRO_ITEM', 'DATA DA ATUALIZACAO', 'left');

    // add the columns to the DataGrid
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_DESCRICAO);
    $this->datagrid->addColumn($column_FERRAMENTA);
    $this->datagrid->addColumn($column_DATA);

    //creates the datagrid column actions
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'CODIGO');
    $column_id->setAction($order_id);

    $order_DESCRICAO = new TAction(array($this, 'onReload'));
    $order_DESCRICAO->setParameter('order', 'DESCRICAO');
    $column_DESCRICAO->setAction($order_DESCRICAO);

    $order_FERRAMENTA  = new TAction(array($this, 'onReload'));
    $order_FERRAMENTA->setParameter('order', 'FERRAMENTA');
    $column_FERRAMENTA->setAction($order_FERRAMENTA);

    $order_DATA  = new TAction(array($this, 'onReload'));
    $order_DATA->setParameter('order', 'DATA');
    $column_DATA->setAction($order_DATA);

    // create EDIT action
    $action_edit = new TDataGridAction(array('CadastroForm', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('far:edit blue');
    $action_edit->setField('CODIGO');
    $this->datagrid->addAction($action_edit);

    /*// create DELETE action
    $action_del = new TDataGridAction(array($this, 'onDelete'));
    $action_del->setButtonClass('btn btn-default');
    $action_del->setLabel(_t('Delete'));
    $action_del->setImage('far:trash-alt red');
    $action_del->setField('CODIGO');
    $this->datagrid->addAction($action_del);*/

    // create the datagrid model
    $this->datagrid->createModel();

    // create the page navigation
    $this->pageNavigation = new TPageNavigation;
    $this->pageNavigation->enableCounters();
    $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
    $this->pageNavigation->setWidth($this->datagrid->getWidth());

    $panel = new TPanelGroup;
    $panel->add($this->datagrid);
    $panel->addFooter($this->pageNavigation);

    // vertical box container
    $container = new TVBox;
    $container->style = 'width: 100%';
    $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    $container->add($this->form);
    $container->add($panel);

    parent::add($container);
  }
}
