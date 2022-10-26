<?php

use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;

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
class PeididoList extends TStandardList
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
    parent::setActiveRecord('ListaPedido');   // DEFINE O REGISTRO ATIVO
    parent::setDefaultOrder('id', 'asc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('id', '=', 'id'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_status', '=', 'id_status'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_usuario', '=', 'id_usuario'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('created_at', '=', 'created_at'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('updated_at', '=', 'updated_at'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO

    $this->form = new BootstrapFormBuilder('form_search');
    $this->form->setFormTitle('ESTOQUE UMAS UMES');

    // CRIE OS CAMPOS DO FORMULÁRIO

    $id = new TEntry('id');
    $id_status = new TDBCombo('id_status', 'bancodados', 'Status', 'id_status', 'nome');
    $id_usuario = new TDBCombo('id_usuario', 'bancodados', 'SystemUser', 'id', 'matricula');
    $data_pedido = new TEntry('created_at');
    $data_aprovacao = new TDateTime('updated_at');


    // ADICIONE OS CAMPOS

    $this->form->addFields([new TLabel('CODIGO DO PEDIDO')], [$id]);
    $this->form->addFields([new TLabel('CODIGO DO STATUS')], [$id_status]);
    $this->form->addFields([new TLabel('MATRICULA')], [$id_usuario]);
    $this->form->addFields([new TLabel('DATA DO PEDIDO')], [$data_pedido]);
    $this->form->addFields([new TLabel('DATA DA APROVAÇÃO')], [$data_aprovacao]);


    $id->setTip('COLOQUE O CODIGO DO PEDIDO QUE VOCE PROCURA');
    $id_status->setTip('COLOQUE O TIPO DE STATUS QUE VOCE PROCURA');
    $id_usuario->setTip('COLOQUE A MATRICULA DO USUARIO PARA EFETUAR A BUSCA');
    $data_pedido->setTip('COLOQUE A DATA DO PEDIDO PARA EFETUAR A BUSCA');
    $data_aprovacao->setTip('COLOQUE A DATA DA APROVAÇÃO DO PEDIDO PARA EFETUAR A BUSCA');


    $id->setSize('35%');
    $id_status->setSize('35%');
    $id_usuario->setSize('35%');
    $data_pedido->setSize('35%');
    $data_aprovacao->setSize('35%');
    $id_usuario->enableSearch();


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
    $column_id = new TDataGridColumn('id', 'CODIGO DO PEDIDO', 'center');
    $column_id_status = new TDataGridColumn('Status->nome', 'CODIGO DO STATUS', 'center');
    $column_id_usuario = new TDataGridColumn('user->name', 'USUÁRIO', 'center');
    $column_data_pedido = new TDataGridColumn('created_at', 'DATA DO PEDIDO', 'center');
    $column_data_aprovacao = new TDataGridColumn('updated_at', 'DATA DA APROVACAO', 'center');



    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_id_status);
    $this->datagrid->addColumn($column_id_usuario);
    $this->datagrid->addColumn($column_data_pedido);
    $this->datagrid->addColumn($column_data_aprovacao);



    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'id');
    $column_id->setAction($order_id);

    $order_id_status = new TAction(array($this, 'onReload'));
    $order_id_status->setParameter('order', 'id_status');
    $column_id_status->setAction($order_id_status);

    $order_id_usuario = new TAction(array($this, 'onReload'));
    $order_id_usuario->setParameter('order', 'id_status');
    $column_id_usuario->setAction($order_id_status);

    $order_data_pedido = new TAction(array($this, 'onReload'));
    $order_data_pedido->setParameter('created_at', 'created_at');
    $column_data_pedido->setAction($order_data_pedido);

    $order_data_aprovacao = new TAction(array($this, 'onReload'));
    $order_data_aprovacao->setParameter('updated_at', 'updated_at');
    $column_data_aprovacao->setAction($order_data_aprovacao);


    // CRIAR AÇÃO EDITAR
    $action_edit = new TDataGridAction(array('PedidoMaterial', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('far:edit blue');
    $action_edit->setField('id');
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