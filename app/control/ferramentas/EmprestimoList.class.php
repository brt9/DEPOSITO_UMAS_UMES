<?php

use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TSpinner;

/**
 * LISTA DE EMPRESTIMO
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class EmprestimoList extends TStandardList
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
    parent::setActiveRecord('Emprestimo');   // DEFINE O REGISTRO ATIVO
    parent::setDefaultOrder('id', 'asc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('id', '=', 'id'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_emprestimo', '=', 'id_emprestimo'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_usuario', '=', 'id_usuario'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_status', '=', 'id_status'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO

    $this->form = new BootstrapFormBuilder('form_search');
    $form = $this->form->setFormTitle('Emprestimo de ferramentas');
    // CRIE OS CAMPOS DO FORMULÁRIO

    $unique = new TDBUniqueSearch('FerramentaList', 'bancodados', 'emprestimo', 'id', 'id');
    $unique->setMinLength(1);
    $unique->setMask('{id}');
    $unique->setTip('Pesquise o emprestido pelo id');
    $id_usuario = new TEntry('id_usuario');
    $id_status = new TEntry('id_status');
    $data = new TDate('created_at');

    // ADICIONE OS CAMPOS
    $row = $this->form->addFields(
      [new TLabel('Campo de busca')],
      [$unique]
    );
  
    $row = $this->form->addFields(
      [new TLabel('Usuário')],
      [$id_usuario],
      [new TLabel('Status')],
      [$id_status],
      [new Tlabel('Data')],
      [$data]
    );

    // MANTENHA O FORMULÁRIO PREENCHIDO DURANTE A NAVEGAÇÃO COM OS DADOS DA SESSÃO
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // ADICIONE AS AÇÕES DO FORMULÁRIO DE PESQUISA
    $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search black');
    $btn->class = 'btn btn-sm btn-primary';
    $btn = $this->form->addAction("Solicitar emprestimo", new TAction(array('EmprestimoFerramentasForm', "onEdit")), "fa:plus-circle black");
    $btn->class = 'btn btn-sm btn-success'; 

    // CRIA UMA GRADE DE DADOS
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%; border-radius: 20rem;';
    $this->datagrid->setHeight(320);

    // CRIA AS COLUNAS DA GRADE DE DADOS
    $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
    $column_nome = new TDataGridColumn('id_ferramenta', 'Nome da ferramenta', 'center');
    $column_usuario = new TDataGridColumn('id_usuario', 'Usuário', 'center');
    $column_status = new TDataGridColumn('id_status', 'Status', 'center');


    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_nome);
    $this->datagrid->addColumn($column_usuario);
    $this->datagrid->addColumn($column_status);


    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'id');
    $column_id->setAction($order_id);

    $order_usuario  = new TAction(array($this, 'onReload'));
    $order_usuario->setParameter('order', 'id_usuario');
    $column_usuario->setAction($order_usuario);



    // CRIAR AÇÃO EDITAR
    $action_edit = new TDataGridAction(array('CadastroFerramentasForm', 'onEdit'));
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
