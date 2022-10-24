<?php

use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;
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
    parent::setDefaultOrder('id', 'desc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('id', '=', 'id'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_emprestimo', '=', 'id_emprestimo'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_usuario', '=', 'id_usuario'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('id_status', '=', 'id_status'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('created_at', '=', 'created_at'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO
    $this->form = new BootstrapFormBuilder('form_search');
    $form = $this->form->setFormTitle('Emprestimo de ferramentas');
    // CRIE OS CAMPOS DO FORMULÁRIO

    $unique = new TDBUniqueSearch('FerramentaList', 'bancodados', 'emprestimo', 'id', 'id');
    $unique->setMinLength(1);
    $unique->setMask('{id}');
    $unique->placeholder = 'Pesquise o emprestido pelo id, usuario ou status';
    $data = new TDate('created_at');
    $data->placeholder = 'Pesquise pela data de criação';

    // ADICIONE OS CAMPOS
    $row = $this->form->addFields(
      [new TLabel('Id')],
      [$unique],
      [new Tlabel('Data')],
      [$data],
    );

    $row = $this->form->addFields();
    $data->setSize('70%');

    // MANTENHA O FORMULÁRIO PREENCHIDO DURANTE A NAVEGAÇÃO COM OS DADOS DA SESSÃO
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // ADICIONE AS AÇÕES DO FORMULÁRIO DE PESQUISA
    $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search white');
    $btn->style = 'background-color:#2c7097; color:white';
    $btn = $this->form->addAction("Solicitar emprestimo", new TAction(array('EmprestimoFerramentasForm', "onEdit")), "fa:plus-circle white");
    $btn->style = 'background-color:#218231; color:white';

    // CRIA UMA GRADE DE DADOS
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%; border-radius: 20rem;';
    $this->datagrid->setHeight(320);

    // CRIA AS COLUNAS DA GRADE DE DADOS

    $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
    $column_usuario = new TDataGridColumn('user->name', 'Usuário', 'center');
    $column_status = new TDataGridColumn('id_status', 'Status', 'center');
    $column_created = new TDataGridColumn('created_at', 'Data da solicitação', 'center');

    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_usuario);
    $this->datagrid->addColumn($column_status);
    $this->datagrid->addColumn($column_created);

    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $action1 = new TDataGridAction(['AprovacaoSolicitacaoForm', 'onShow']);
    $action1->setField('id');
    $this->datagrid->addAction($action1, 'Visualizar solicitação', 'fa:check-circle background-color:#218231');

    // CRIAR AÇÃO EDITAR
    $action_edit = new TDataGridAction(array('EmprestimoFerramentasForm', 'onEdit'));
    $action_edit->setField('id');
    $this->datagrid->addAction($action_edit, 'Editar solicitação', 'far:edit blue');

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
    TTransaction::close(); // fecha a transação.
  }
}
