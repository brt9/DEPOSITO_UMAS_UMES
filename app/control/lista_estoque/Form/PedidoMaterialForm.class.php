<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Sabberworm\CSS\Value\Value;
use Adianti\Util\AdiantiUIBuilder;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TQRCodeInputReader;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * FORMULÁRIO DE CADASTRO DE MATERIAL
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class PedidoMaterialForm extends TPage
{
    protected $form; //  FORMULÁRIO
    protected $subFormFirst;
    protected $subFormSecound;
    protected $dataGrid;

    function __construct()
    {
        TPage::include_css('app/resources/styles.css');
        parent::__construct();

        // cria o formulário
        $this->form = new BootstrapFormBuilder('pedidoMaterial');
        $this->form->setFormTitle('<b>FORMULARIO DE PEDIDO DE MATERIAL</b>');

        $this->subFormFirst = new BootstrapFormBuilder('subFormFirst');
        $this->subFormSecound = new BootstrapFormBuilder('subFormSecound');
        $this->subFormSecound->setName('pedidoMaterial');

        $id = new TEntry('id');
        $id->setEditable(FALSE);
        $id->setSize('20%');

        $id_item = new TQRCodeInputReader('id_item');
        $id_item->setChangeAction(new TAction(array($this, 'onChangeDescricao')));
        $id_item->setTip('Digite o codigo do item desejado');
        $id_item->placeholder = '00000';
        $id_item->setSize('100%');
        $id_item->setMask('99999');
        $id_item->maxlength = 5;
        $id_item->setSize('50%');

        $status = new TEntry('status');
        $status->setSize('50%');
        $status->setEditable(false);
        $status->class = 'form';

        $descricao = new TDBCombo('descricao', 'bancodados', 'Material', 'id_item', '{id_item} - {descricao}', 'id_item');
        $descricao->setChangeAction(new TAction(array($this, 'onChangeQuantidade')));
        $descricao->setTip('Digite a descrição do item desejado');
        $descricao->setSize('100%');
        $descricao->enableSearch();

        $quantidade = new TSpinner('quantidade');
        $quantidade->setTip('Digite a quantidade do item desejado');
        $quantidade->setSize('50%');
        $quantidade->setRange(0, 1000, 1);

        $quantidadeDisponivel = new TCombo('quantidadeDisponivel');
        $quantidadeDisponivel->setEditable(FALSE);
        $quantidadeDisponivel->setSize('50%');
        $quantidadeDisponivel->class = 'emprestimo';
        $quantidadeDisponivel->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';

        $descricao->addValidation('descricao', new TRequiredValidator);
        $quantidade->addValidation('quantidade', new TRequiredValidator);

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('<b>Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios</b>')],
        );

        $row = $this->form->addFields(
            [$label = new TLabel('<b>Id</b>')],
            [$id],
            [$label =  new TLabel('<b>Status</b>')],
            [$status],
        );
        $row = $this->subFormFirst->addFields(
            [$label = new TLabel('<b>Codigo item</b>')],
            [$id_item],
            [$label = new TLabel('<b>Material</b>')],
            [$descricao],
        );
        $row = $this->subFormFirst->addFields(
            [$label =  new TLabel('<b>Quantidade</b>')],
            [$quantidade],
            [$label =  new TLabel('<b>Quantidade disponivel</b>')],
            [$quantidadeDisponivel],
        );
        $addMaterial = TButton::create('addMaterial', [$this, 'onProductAdd'], 'Adicionar material', 'fa:plus-circle green');
        $addMaterial->getAction()->setParameter('static', '1');
        $this->subFormFirst->addFields([], [$addMaterial]);
        $this->form->addContent([$this->subFormFirst]);

        //Grade de materiais
        $this->dataGrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->dataGrid->setHeight(150);
        $this->dataGrid->makeScrollable();
        $this->dataGrid->setId('listaMaterial');
        $this->dataGrid->generateHiddenFields();
        $this->dataGrid->style = "min-width: 700px; width:100%;margin-bottom: 10px";

        $colunaIditem   = new TDataGridColumn('id_item', 'Codigo item', 'center', '30%');
        $colunaDescicao   = new TDataGridColumn('descricao', 'Descricao', 'center', '30%');
        $colunaQuantidade     = new TDataGridColumn('quantidade', 'Quantidade', 'center', '30%');

        $this->dataGrid->addColumn($colunaIditem);
        $this->dataGrid->addColumn($colunaDescicao);
        $this->dataGrid->addColumn($colunaQuantidade);

        $action2 = new TDataGridAction([$this, 'onDeleteItem']);
        $action2->setField('descricao');

        $this->dataGrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        $this->dataGrid->createModel();

        $panel = new TPanelGroup();
        $panel->add($this->dataGrid);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent([$panel]);

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('PedidoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white; border-radius: 0.5rem;';
        $btnClear = $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser white');
        $btnClear->style = 'background-color:#c73927; color:white; border-radius: 0.5rem;';
        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white; border-radius: 0.5rem;';

        $vbox = new TVBox;
        $vbox->style = 'width: 100%; margin-top: 2rem';
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('bancodados');

                $pedidoMaterial = PedidoMaterial::find($param['key']);
                $this->form->setData($pedidoMaterial); //inserindo dados no formulario. 

                $pivot = PivotPedidoMaterial::where('id_pedido_material', '=', $pedidoMaterial->id)
                    ->load();

                if ($pivot) {
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        $obj->id_item = $value->id_item;
                        $obj->descricao = $value->id_item;
                        $obj->quantidade = $value->quantidade;
                    }
                }
                // add field list to the form
                $this->form->addContent([$this->fieldlist]);
                TTransaction::close();
            } else {
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->fireEvents($param);
        }
    }
    public function onSave($param)
    {
        try {
            $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');
            $usuarioLogado = TSession::getValue('userid');

            $duplicates = $this->getDuplicates($param['descricao']);
            if ($param['descricao'] == [""]) {
                throw new Exception('Campo "Descrição" é obrigatorio não pode ser vazio');
            }
            if ($param['quantidade'] <= ['0']) {
                throw new Exception('Campo "Quantidade" não pode ser vazio ou negativo');
            } else {
                if (isset($param["id"]) && !empty($param["id"])) {
                    $object = new PedidoMaterial($param["id"]);
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                } else {
                    $object = new PedidoMaterial();
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                }
                $object->store();

                PivotPedidoMaterial::where('id_pedido_material', '=', $object->id)->delete();

                $descricao = array_map(function ($value) {
                    return (int)$value;
                }, $param['descricao']);

                if (isset($descricao)) {
                    for ($i = 0; $i < count($descricao); $i++) {
                        if (empty($param['quantidade'][$i])) {
                            throw new Exception('A quantidade está vazia na linha ' . ($i + 1));
                        }
                        if (!empty($duplicates[$i])) {
                            throw new Exception(
                                'Item e repetido na linha ' . ($i + 1) .
                                    '. Um material nao poder ser solicitada mais de uma vez'
                            );
                        }
                        $pivot = new PivotPedidoMaterial();
                        $pivot->id_pedido_material = $object->id;
                        $pivot->id_item = $param['descricao'][$i];
                        $pivot->quantidade  = $param['quantidade'][$i];

                        $tools = Material::where('id_item', 'in', $param['descricao'])->load();
                        $qtdTools = [];
                        foreach ($tools as $key) {
                            $qtdTools[] = $key->quantidade_estoque;
                        }

                        //Verifica se a quantidade solicitada for maior que a do estoque 
                        if ($param['quantidade'][$i] >= $qtdTools[$i] or $param['quantidade'][$i] < 0) {
                            throw new Exception(
                                'A quantidade na ' . ($i + 1) .
                                    '° linha não pode ser maior que a disponível no estoque que é: '
                                    . $qtdTools[$i]
                            );
                        } else {
                            $pivot->quantidade = $param['quantidade'][$i];
                            $result = $qtdTools[$i] - $param['quantidade'][$i]; //valor subtraido.
                            $this->updateQuantidade($param['descricao'][$i], $result);
                        }
                        $pivot->store();
                    }
                }
            }
            TTransaction::close(); // close the transaction
            $action = new TAction(array('PedidoList', 'onReload'));
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollbackAll();

            $action = new TAction(array('PedidoMaterialForm', 'fireEvents'));

            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $action);
        }
    }
    public function validate()
    {
        // assign post data before validation
        // validation exception would prevent
        // the user code to execute setData()
        $this->setData($this->getData());

        foreach ($this->fields as $fieldObject) {
            $fieldObject->validate();
        }
    }
    public function updateQuantidade($id, $value)
    {
        try {
            TTransaction::open('bancodados');
            Material::where('id_item', '=', $id)
                ->set('quantidade_estoque', $value)
                ->update();
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onClear($param)
    {
    }
    public function fireEvents($param)
    {
        if (!empty($param['id_item'])) {
            TTransaction::open('bancodados');
            $pedidoMaterial = Material::where('id', '=', $param['id']);
            $this->form->setData($pedidoMaterial); //inserindo dados no formulario. 

            $pivot = pivot::where('id_pedido_material', '=', $pedidoMaterial->id)->load();

            if ($pivot) {
                $this->fieldlist->addHeader();
                foreach ($pivot as $itens => $value) {
                    $obj = new stdClass;
                    $obj->id_item = $value->id_item;
                    $obj->quantidade = $value->quantidade;

                    $this->fieldlist->addDetail($obj);
                }
                $this->fieldlist->addCloneAction();
            }
            // add field list to the form
            $this->form->addContent([$this->fieldlist]);
            TTransaction::close();
        } else {
            $this->fieldlist->addHeader();
            $this->fieldlist->addDetail(new stdClass);
            $this->fieldlist->addCloneAction();
            $this->form->addContent([$this->fieldlist]);
        }
    }
    function getDuplicates($param)
    {
        return array_unique(array_diff_assoc($param, array_unique($param)));
    }
    public function onChangeDescricao($param)
    {
        $input_id = $param['_field_id']; //Pega o campo e o id (campo_id)
        $id_item = $param['_field_value']; //pegar o valor do campo
        $nomeField = explode('_', $input_id); //Nome do campo 
        $uniqueIdField = end($nomeField); //Pega apenas o valor id do campo

        if ($id_item) {
            $obj = new stdClass;

            try {
                TTransaction::open('bancodados');
                $material = Material::find($id_item);
                $obj->{'descricao_' . $uniqueIdField} = $material->id_item;
                //$obj->{'quantidadeDisponivel_' .$uniqueIdField} = $material->quantidade_estoque;
                TForm::sendData('pedidoMaterial', $obj);

                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
            }
        }
    }
    public static function onChangeQuantidade($param)
    {
        /* $input_id = $param['_field_id']; //Pega o campo e o id (campo_id)
        $id_item = $param['_field_value']; //pegar o valor do campo
        $nomeField = explode('_', $input_id); //Nome do campo 
        $uniqueIdField = end($nomeField); //Pega apenas o valor id do campo

        if ($id_item) {
            $obj = new stdClass;

            try {
                TTransaction::open('bancodados');
                $material = Material::find($id_item);
                $obj->{'quantidadeDisponivel_' . $uniqueIdField} = number_format($material->quantidade_estoque);
                TForm::sendData('pedidoMaterial', $obj);
                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
            }
        } */
        var_dump($param);
        TTransaction::open('bancodados');
        if (!empty($param['key'])) {
            $criteria = TCriteria::create(['id_item' => $param['key']]);
            TCombo::reload('pedidoMaterial', 'quantidadeDisponivel', $criteria);
        } else {
            TCombo::clearField('pedidoMaterial', 'quantidadeDisponivel');
        }
        TTransaction::close();

    }
    /**
     * Add a product into item list
     * @param $param URL parameters
     */
    public function onProductAdd($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();
            if ((!$data->descricao)) {
                throw new Exception('Erro ao adicionar material ao campo');
            }

            TTransaction::open('bancodados');
            $material = Material::find($param['descricao']);
            TTransaction::close();

            $uniqid = !empty($data->descricao) ? $data->descricao : uniqid();
            $grid_data = [
                'id_item'      => $uniqid,
                'descricao'      => $material->descricao,
                'quantidade'          => $param['quantidade'],
            ];

            $grid = array_map(function ($value) {
                return (string)$value;
            }, $grid_data);

            // insert row dynamically
            $row = $this->dataGrid->addItem((object) $grid);
            $row->id = $uniqid;

            TDataGrid::replaceRowById('listaMaterial', $uniqid, $row);

            // clear product form fields after add
            $data->id_item     = '';
            $data->descricao     = '';
            $data->quantidade         = '';


            // send data, do not fire change/exit events
            TForm::sendData('pedidoMaterial', $data, false, false);
        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Delete a product from item list
     * @param $param URL parameters
     */
    public static function onDeleteItem($param)
    {
        $data = new stdClass;
        $data->id_item     = '';
        $data->descricao   = '';
        $data->quantidade  = '';

        // send data, do not fire change/exit events
        TForm::sendData('pedidoMaterial', $data, false, false);

        // remove row
        TDataGrid::removeRowById('listaMaterial', $param['descricao']);
    }
}
