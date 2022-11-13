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
  private static $formName = 'form_search';
  private $showMethods = ['onReload', 'onSearch'];
  // CONSTRUTOR DE PÁGINA
  public function __construct()
  {
    parent::__construct();

    parent::setDatabase('bancodados');            // DEFINE O BANCO DE DADOS
    parent::setActiveRecord('lista');   // DEFINE O REGISTRO ATIVO
    parent::setDefaultOrder('descricao', 'asc');         //  DEFINE A ORDEM PADRÃO
    parent::addFilterField('id_item', '=', 'id_item'); //  CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO
    parent::addFilterField('descricao', '=', 'descricao'); // CAMPO DE FILTRO, OPERADOR, CAMPO DE FORMULÁRIO

    // CRIA O FORMULÁRIO
   
    $this->form = new BootstrapFormBuilder('form_search');
    $this->form->setFormTitle('ESTOQUE UMAS UMES');

    // CRIE OS CAMPOS DO FORMULÁRIO

    $id = new TQRCodeInputReader('id_item');
    $descricao = new TDBCombo('descricao', 'bancodados', 'lista', 'descricao', 'descricao');
    

    // ADICIONE OS CAMPOS

    $this->form->addFields([new TLabel('CODIGO DO ITEM')], [$id]);
    $this->form->addFields([new TLabel('DESCRIÇÃO')], [$descricao]);

    $id->setSize('50%');
    $id->placeholder = '00000';
    $id->setMask('99999');
    $id->maxlength = 5;
    $id->setTip('Digite o codigo do item desejado');


    $descricao->enableSearch();
    $descricao->setSize('50%');
    $descricao->setTip('Digite a descrição do item desejado');


    // MANTENHA O FORMULÁRIO PREENCHIDO DURANTE A NAVEGAÇÃO COM OS DADOS DA SESSÃO
    $this->form->setData(TSession::getValue('cadastro_filter_data'));

    // ADICIONE AS AÇÕES DO FORMULÁRIO DE PESQUISA
    $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
    $this->form->addAction("Cadastrar Novo Item", new TAction(["CadastroForm", "onEdit"]), "fa:plus-circle green");
    $btn->class = 'btn btn-sm btn-primary';

   

    // CRIA UMA GRADE DE DADOS
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->datatable = 'true';
    $this->datagrid->style = 'width: 100%';
    $this->datagrid->setHeight(320);


    // CRIA AS COLUNAS DA GRADE DE DADOS
    $column_id = new TDataGridColumn('id_item', 'CODIGO DO ITEM', 'center', 50);
    $column_descricao = new TDataGridColumn('descricao', 'DESCRIÇÃO', 'left');
    $column_quantidade_estoque = new TDataGridColumn('quantidade_estoque', 'QUANTIDADE EM ESTOQUE', 'left');
    $column_update_at = new TDataGridColumn('updated_at', 'DATA DA ATUALIZAÇÂO', 'left');


    // ADICIONE AS COLUNAS À GRADE DE DADOS
    $this->datagrid->addColumn($column_id);
    $this->datagrid->addColumn($column_descricao);
    $this->datagrid->addColumn($column_quantidade_estoque);
    $this->datagrid->addColumn($column_update_at);

    $column_update_at->setTransformer(array($this, 'formatDate'));

    // CRIA AS AÇÕES DA COLUNA DA GRADE DE DADOS
    $order_id = new TAction(array($this, 'onReload'));
    $order_id->setParameter('order', 'id_item');
    $column_id->setAction($order_id);

    $order_descricao = new TAction(array($this, 'onReload'));
    $order_descricao->setParameter('order', 'descricao');
    $column_descricao->setAction($order_descricao);

    $order_quantidade_estoque  = new TAction(array($this, 'onReload'));
    $order_quantidade_estoque->setParameter('order', 'quantidade_estoque');
    $column_quantidade_estoque->setAction($order_quantidade_estoque);

    $order_update_at  = new TAction(array($this, 'onReload'));
    $order_update_at->setParameter('order', 'updated_at');
    $column_update_at->setAction($order_update_at);


    // CRIAR AÇÃO EDITAR
    $action_edit = new TDataGridAction(array('CadastroForm', 'onEdit'));
    $action_edit->setButtonClass('btn btn-default');
    $action_edit->setLabel(_t('Edit'));
    $action_edit->setImage('far:edit blue');
    $action_edit->setField('id_item');
    $this->datagrid->addAction($action_edit);

    TScript::create('$(\'#' . self::$formName . '\').collapse(\'toggle\');');
 $this->form->addHeaderActionLink('Filtros de busca', new TAction(array($this, 'toggleSearch')), 'fa:filter green fa-fw');
        
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
  public function formatDate($date, $object)
  {
      $dt = new DateTime($date);
      return $dt->format('d/m/Y - H:i');
  }
  static function toggleSearch()
  {
      // também pode apagar esses blocos if/else se não quiser usar a "memória" de estado do form
      if (TSession::getValue('toggleSearch_'.self::$formName) == 1) {
          TSession::setValue('toggleSearch_'.self::$formName,0);
      } else {
          TSession::setValue('toggleSearch_'.self::$formName,1);
      }

      // esta linha é a responsável por abrir/fechar o form
      TScript::create('$(\'#' . self::$formName . '\').collapse(\'toggle\');');
      // caso retire a função de "memória", copie a linha acima para dentro do onSearch,
      // para que o form "permaneça aberto" (reabra automaticamente) ao realizar buscas
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