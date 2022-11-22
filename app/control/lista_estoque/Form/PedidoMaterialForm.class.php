<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
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
use Adianti\Widget\Form\TForm;

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

    function __construct()
    {
        TPage::include_css('app/resources/styles.css');
        parent::__construct();

        // cria o formulário
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle('<b>FORMULARIO DE PEDIDO DE MATERIAL</b>');

        $this->subFormFirst = new BootstrapFormBuilder('subFormFirst');
        $this->subFormSecound = new BootstrapFormBuilder('subFormSecound');

        $id = new TEntry('id');
        $id->setEditable(FALSE);
        $id->setSize('20%');

        $id_item = new TQRCodeInputReader('id_item[]');
        $id_item->setChangeAction(new TAction(array($this, 'onChangeDescricao')));
        $id_item->setTip('Digite o codigo do item desejado');
        $id_item->placeholder = '00000';
        $id_item->setSize('100%');
        $id_item->setMask('99999');
        $id_item->maxlength = 5;
        $id_item->setSize('50%');

        $status             = new TEntry('status');
        $status->setSize('50%');
        $status->setEditable(false);
        $status->class = 'form';

        $descricao = new TDBCombo('descricao[]', 'bancodados', 'Material', 'id_item', '{id_item} - {descricao}', 'id_item');
        $descricao->setChangeAction(new TAction([$this, 'onChangeQuantidade']));
        $descricao->setTip('Digite a descrição do item desejado');
        $descricao->setSize('100%');
        $descricao->enableSearch();

        $quantidade = new TSpinner('quantidade[]');
        $quantidade->setTip('Digite a quantidade do item desejado');
        $quantidade->setSize('100%');

        $quantidadeDisponivel = new TCombo('quantidadeDisponivel');
        $quantidadeDisponivel->class = 'emprestimo';
        $quantidadeDisponivel->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';
        $quantidadeDisponivel->setSize('100%');
        $quantidadeDisponivel->setEditable(FALSE);

        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Codigo item</b><font color="red"> *</font>',  $id_item,  ['width' => '10%']);
        $this->fieldlist->addField('<b>Descrição</b><font color="red"> *</font>',  $descricao,  ['width' => '80%']);
        $this->fieldlist->addField('<b>Quantidade</b><font color="red"> *</font>',   $quantidade,   ['width' => '5%']);
        $this->fieldlist->addField('<b>Quantidade disponível</b><font color="red">*</font>',   $quantidadeDisponivel,   ['width' => '5%']);
        $this->subFormSecound->addField($id_item);
        $this->subFormSecound->addField($descricao);
        $this->subFormSecound->addField($quantidade);

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('<b>Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios</b>')],
        );

        $row = $this->form->addFields(
            [$label = new TLabel('<b>Id</b>')],
            [$id],
            [$label =  new TLabel('<b>Status</b>')],
            [$status],
        );

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

                $pivot = PivotPedidoMaterial::where('id_pedido_material', '=', $pedidoMaterial->id)->load();

                if ($pivot) {
                    $this->fieldlist->addHeader();
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        $obj->descricao = $value->id_item;
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
            TTransaction::setLoggerFunction(function($param)
            {
                  print $param.'<br/>';
            }); 
            $usuarioLogado = TSession::getValue('userid');

            $duplicates = $this->getDuplicates($param['descricao']);
            if ($param['descricao'] == [""]) {
                throw new Exception('Campo Descrição é obrigatorio não pode ser vazio');
            }
            if ($param['quantidade'] == ['0']) {
                throw new Exception('Campo Quantidade não pode ser vazio');
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
                
                //$object->fromArray($param);
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
                            throw new Exception('Item e repetido na linha ' . ($i + 1) . '. Uma ferramentas nao poder ser solicitada mais de uma vez');
                        }
                        $pivot = new PivotPedidoMaterial();
                        $pivot->id_pedido_material = $object->id;
                        $pivot->id_item = $param['descricao'][$i];
                        $pivot->quantidade  = $param['quantidade'][$i];
                        
                        $tools = Material::where('id_item', 'in', $param['descricao'])->load();
                        //var_dump($param);exit;
                        $qtdTools = [];
                        foreach ($tools as $key) {
                            $qtdTools[] = $key->quantidade_estoque;
                        }

                        //Verifica se a quantidade solicitada for maior que a do estoque 
                        if ($param['quantidade'][$i] >= $qtdTools[$i] or $param['quantidade'][$i] < 0) {
                            throw new Exception(
                                'A quantidade na ' . ($i + 1) . '° linha não pode ser maior que a disponível no estoque que é: ' . $qtdTools[$i]
                            );
                        } else {
                            $pivot->quantidade = $param['quantidade'][$i];
                            $result = $qtdTools[$i] - $param['quantidade'][$i]; //valor subtraido.
                            $this->updateQuantidade($pivot->descricao, $result);
                        }
                        $pivot->store();
                    }
                }
            }

            TTransaction::close(); // close the transaction
            $action = new TAction(array('PedidoList', 'onReload'));

            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $action);
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();

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
            PedidoMaterial::where('id_item', '=', $id)
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
        TTransaction::open('bancodados');
        $pedido = Material::where('descricao', 'in', $param)->load();
        foreach ($pedido as $itens => $value) {
            return $value->descricao;
        }
        // add field list to the form
        TTransaction::close();
    }
    public static function onChangeQuantidade($param)
    {
        TTransaction::open('bancodados');
        empty($param['descricao']) ? $id_item = $param : $id_item = $param['id_item'];
        $pedido = Material::where('descricao', 'in', $id_item)->load();
        $obj = new stdClass;
        $obj->quantidade_estoque = $pedido[0]->quantidade_estoque;
        TCombo::reload('my_form', 'quantidadeDisponivel', $obj);
        TTransaction::close();
    }
}
