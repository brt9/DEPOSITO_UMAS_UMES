<?php

use Adianti\Widget\Form\TEntry;

/**
 * LISTA DE FERRAMENTAS
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class FerramentasList extends TStandardList
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
    parent::setActiveRecord('Ferramentas');   // DEFINE O REGISTRO ATIVO
    parent::setDefaultOrder('nome', 'asc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('id', '=', 'id'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('nome', 'like', 'nome'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO

    $this->form = new BootstrapFormBuilder('form_search');
    $this->form->setFormTitle('Lista de ferramentas');

    // CRIE OS CAMPOS DO FORMULÁRIO

    $id = new TEntry('id');
    $nome = new TEntry('nome');

    // ADICIONE OS CAMPOS

    $this->form->addFields(
      [new TLabel('Id')],
      [$id],
      [new TLabel('Nome')],
      [$nome]
    );

    //style do campo de busca
    $id->setSize('20%');
    $id->setTip("id da ferramenta");
    $nome->setSize('100%');
    $nome->placeholder = "Nome da ferramenta";


    // MANTENHA O FORMULÁRIO PREENCHIDO DURANTE A NAVEGAÇÃO COM OS DADOS DA SESSÃO
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // ADICIONE AS AÇÕES DO FORMULÁRIO DE PESQUISA
    $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
    $btn->class = 'btn btn-sm btn-primary';
    $this->form->addAction("Cadastrar ferramenta", new TAction(array('CadastroFerramentasForm', "onEdit")), "fa:plus-circle green");

    // CRIA UMA GRADE DE DADOS
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);


    // CRIA AS COLUNAS DA GRADE DE DADOS
    $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
    $column_nome = new TDataGridColumn('nome', 'Nome da ferramenta', 'center');
    $column_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'center');


    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_nome);
    $this->datagrid->addColumn($column_quantidade);


    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'id');
    $column_id->setAction($order_id);

    $order_nome = new TAction(array($this, 'onReload'));
    $order_nome->setParameter('order', 'nome');
    $column_nome->setAction($order_nome);

    $order_quantidade  = new TAction(array($this, 'onReload'));
    $order_quantidade->setParameter('order', 'quantidade');
    $column_quantidade->setAction($order_quantidade);



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
