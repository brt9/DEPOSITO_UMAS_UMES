<?php

/**
 * LISTA DE MATERIAS EM ESTOQUE
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class CadastroList extends TStandardList
{
  protected $form;     // FORMULÁRIO DE REGISTRO
  protected $datagrid; //  LISTAGEM
  protected $pageNavigation;
  protected $formgrid;
  protected $deleteButton;
  protected $transformCallback;

  // CONSTRUTOR DE PÁGINA
  public function __construct()
  {
    parent::__construct();

    parent::setDatabase('bancodados');            // DEFINE O BANCO DE DADOS
    parent::setActiveRecord('lista');   // DEFINE O REGISTRO ATIVO
    parent::setDefaultOrder('DESCRICAO', 'asc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('CODIGO', '=', 'CODIGO'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('DESCRICAO', 'like', 'DESCRICAO'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO

    $this->form = new BootstrapFormBuilder('form_search');
    $this->form->setFormTitle('ESTOQUE UMAS UMES');

    // CRIE OS CAMPOS DO FORMULÁRIO

    $id = new TQRCodeInputReader('CODIGO');
    $QUANTIDADE_ESTOQUE = new TEntry('QUANTIDADE_ESTOQUE');
    $DATA = new TEntry('DATA_CADASTRO_ITEM');
    $DESCRICAO       = new TCombo ('DESCRICAO');
    //$ITEM       = new TEntry('DESCRICAO');

    // ADICIONE OS CAMPOS

    $this->form->addFields([new TLabel('CODIGO DO ITEM')], [$id]);
    //$this->form->addFields([new TLabel('DESCRICAO')], [$ITEM]);

    $DESCRICAO->addItems(['JOELHO PVC 90°' => 'JOELHO PVC 90°', 'JOELHO PVC 45°' => 'JOELHO PVC 45°', 'LUVA PVC SOLD' => 'LUVA PVC SOLD', 'LUVA FERRO FUND BIPARTIDA' => 'LUVA FERRO FUND BIPARTIDA', 'LUVA DE CORRER PVC' => 'LUVA DE CORRER PVC', 'LUVA CORRER PVC DEFOFO' => 'LUVA CORRER PVC DEFOFO']);
    $this->form->addFields([new TLabel('DESCRICAO')], [$DESCRICAO]);
    $id->setSize('50%');
    $DESCRICAO->setSize('50%');

    // MANTENHA O FORMULÁRIO PREENCHIDO DURANTE A NAVEGAÇÃO COM OS DADOS DA SESSÃO
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // ADICIONE AS AÇÕES DO FORMULÁRIO DE PESQUISA
    $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
    $this->form->addAction("Novo Item", new TAction(["CadastroForm", "onEdit"]), "fa:plus-circle green");
    $btn->class = 'btn btn-sm btn-primary';
    
    // CRIA UMA GRADE DE DADOS
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);
    
    
    // CRIA AS COLUNAS DA GRADE DE DADOS
    $column_id = new TDataGridColumn('CODIGO', 'CODIGO DO ITEM', 'center', 50);
    $column_DESCRICAO = new TDataGridColumn('DESCRICAO', 'DESCRICAO', 'left');
    $column_QUANTIDADE_ESTOQUE = new TDataGridColumn('QUANTIDADE_ESTOQUE', 'QUANTIDADE ESTOQUE', 'left');
    $column_DATA = new TDataGridColumn('DATA_CADASTRO_ITEM', 'DATA DA ATUALIZACAO', 'left');
    
    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_DESCRICAO);
    $this->datagrid->addColumn($column_QUANTIDADE_ESTOQUE);
    $this->datagrid->addColumn($column_DATA);
    
    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'CODIGO');
    $column_id->setAction($order_id);
    
    $order_DESCRICAO = new TAction(array($this, 'onReload'));
    $order_DESCRICAO->setParameter('order', 'DESCRICAO');
    $column_DESCRICAO->setAction($order_DESCRICAO);
    
    $order_QUANTIDADE_ESTOQUE  = new TAction(array($this, 'onReload'));
    $order_QUANTIDADE_ESTOQUE->setParameter('order', 'QUANTIDADE_ESTOQUE');
    $column_QUANTIDADE_ESTOQUE->setAction($order_QUANTIDADE_ESTOQUE);
    
    $order_DATA  = new TAction(array($this, 'onReload'));
    $order_DATA->setParameter('order', 'DATA');
    $column_DATA->setAction($order_DATA);
    
    // CRIAR AÇÃO EDITAR
    $action_edit = new TDataGridAction(array('CadastroForm', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('far:edit blue');
    $action_edit->setField('CODIGO');
    $this->datagrid->addAction($action_edit);
    
    
    
    // CRIAR O MODELO DE GRADE DE DADOS
    $this->datagrid->createModel();
    
    // CRIAR A NAVEGAÇÃO DA PÁGINA
    $this->pageNavigation = new TPageNavigation;
    $this->pageNavigation->enableCounters();
    $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
    $this->pageNavigation->setWidth($this->datagrid->getWidth());
    
    $panel = new TPanelGroup;
    $panel->add($this->datagrid);
    $panel->addFooter($this->pageNavigation);
    
    // recipiente de caixa vertical
    $container = new TVBox;
    $container->style = 'width: 100%';
    $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    $container->add($this->form);
    $container->add($panel);
    
    parent::add($container);
     
  }
}
//    $this->form->addFields([new TLabel('ID')], [$id]);
// $this->form->addFields([new TLabel('QUANTIDADE ESTOQUE')], [$QUANTIDADE_ESTOQUE]);
// $this->form->addFields([new TLabel('DATA')], [$DATA]);
/*// create DELETE action
$action_del = new TDataGridAction(array($this, 'onDelete'));
$action_del->setButtonClass('btn btn-default');
$action_del->setLabel(_t('Delete'));
$action_del->setImage('far:trash-alt red');
$action_del->setField('CODIGO');
$this->datagrid->addAction($action_del);*/
//$this->form->addAction(_t('New'),  new TAction(array('cadastroForm', 'onEdit')), 'fa:plus green');
// $sexo->addItems(['MASCULINO' => 'MASCULINO', 'FEMININO' => 'FEMININO']);