<?php

use Adianti\Base\TStandardForm;
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
class PedidoMaterial extends TPage
{
    protected $form; //  FORMULÁRIO
    protected $descricao; //  FORMULÁRIO
    // CONSTRUTOR DE CLASSE
    // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

    function __construct()
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        //  $this->setDatabase('bancodados');              // DEFINE O BANCO DE DADOS
        //  $this->setActiveRecord('pedido');               // DEFINE O REGISTRO ATIVO

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle('<b>FORMULARIO DE PEDIDO DE MATERIAL</b>');


        $id = new THidden('id');
        $id_item = new TQRCodeInputReader('id_item[]');
        $this->descricao = new TDBCombo('descricao[]', 'bancodados', 'lista', 'descricao', 'descricao');
        $quantidade = new TSpinner('quantidade[]');
        $quantidadeDisponivel = new TCombo('quantidadeDisponivel');
        $quantidadeDisponivel->class = 'emprestimo';
        $quantidadeDisponivel->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';
      
      
        $id->setEditable(FALSE);
        $this->descricao->setSize('100%');
        $quantidade->setSize('100%');
        
        $id_item->setSize('100%');
        $id_item->setChangeAction(new TAction(array($this, 'onChange')));
        $id_item->setTip('Digite o codigo do item desejado');
        $id_item->placeholder = '00000';
        $id_item->setMask('99999');
        $id_item->maxlength = 5;

        $quantidadeDisponivel->setSize('100%');
        $quantidadeDisponivel->setEditable(FALSE);
        $this->descricao->setTip('Digite a descrição do item desejado');
        $quantidade->setTip('Digite a quantidade do item desejado');

        $this->descricao->enableSearch();

        $id_item->placeholder = '00000';


        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';

        $this->fieldlist->addField('<b>CODIGO ITEM</b><font color="red"> *</font>',  $id_item,  ['width' => '20%']);
        $this->fieldlist->addField('<b>DESCRIÇÃO</b><font color="red"> *</font>',  $this->descricao,  ['width' => '60%']);
        $this->fieldlist->addField('<b>QUANTIDADE</b><font color="red"> *</font>',   $quantidade,   ['width' => '20%']);
        $this->fieldlist->addField('<b>Quantidade disponível</b><font color="red">*</font>',   $quantidadeDisponivel,   ['width' => '10%']);
        $this->form->addFields([$id]);

        $this->form->addField($id_item);
        $this->form->addField($this->descricao);
        $this->form->addField($quantidade);




        $row = $this->form->addFields(
            [$labelInfo = new TLabel('<b>Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios</b>')],
        );


        $row =  $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addHeader();
        $this->fieldlist->addCloneAction();

        //$id_status->setEditable(FALSE);
        //$id_usuario->setEditable(FALSE);
        // add field list to the form
        $this->form->addContent([$this->fieldlist]);


       

        //////////////////



        //$id_status->setValue('1');
        // form actions
        $btnSave = $this->form->addAction('SALVAR', new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';
        $btnClear = $this->form->addAction('LIMPAR', new TAction([$this, 'onClear']), 'fa:eraser white');
        $btnClear->style = 'background-color:#c73927; color:white';
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);


        parent::add($vbox);
    }
   
    public function onEdit($param)
    {
    /*    try {
            if (isset($param['key'])) {
                TTransaction::open('bancodados');
                $emprestimo = Emprestimo::find($param['key']);
                $this->form->setData($emprestimo); //inserindo dados no formulario. 

                $pivot = PivotEmprestimoFerramentas::where('id_emprestimo', '=', $emprestimo->id)->load();

                if ($pivot) {
                    $this->fieldlist->addHeader();
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        $obj->ferramenta = $value->id_ferramenta;
                        $obj->quantidade = $value->quantidade;

                        $this->fieldlist->addDetail($obj);
                    }
                    $this->fieldlist->addCloneAction();
                }
                $this->onChange(array($pivot[0]->id_ferramenta));
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
        }*/
    }
    public function onSave($param)
    { 
    try {
             $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');

            $usuarioLogado = TSession::getValue('userid');

            $duplicates = $this->getDuplicates($param['id_item']);
            if ($param['id_item'] == [""]) {
                throw new Exception('Campo Codigo Item é obrigatorio não pode ser vazio');
                  } 
            if ($param['descricao'] == [""]) {
                throw new Exception('Campo Descrição é obrigatorio não pode ser vazio');
            }   
             if ($param['quantidade'] == ['0']) {
                throw new Exception('Campo Quantidade não pode ser vazio');
            }else {

                if (isset($param["id"]) && !empty($param["id"])) {
                    $object = new pedido($param["id"]);
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                } else {
                    $object = new pedido();
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                }
               
                $object->fromArray($param);
                $object->store();

                pivot::where('id_pedido_material', '=', $object->id)->delete();


                $id_item = array_map(function ($value) {
                    return (int)$value;
                }, $param['id_item']);


                if (isset($id_item)) {
                    for ($i = 0; $i < count($id_item); $i++) {

                        if (empty($param['quantidade'][$i])) {
                            throw new Exception('A quantidade está vazia na linha ' . ($i + 1));
                        }
                       if (empty($id_item[$i])) {
                            throw new Exception('A ferramenta está vazia na linha ' . ($i + 1));
                        }
                        if (!empty($duplicates[$i])) {
                            throw new Exception('Ferramenta repetida na linha ' . ($i + 1) . '. Uma ferramentas nao poder ser solicitada mais de uma vez');
                        }

                        $pivot = new pivot();
                        $pivot->id_pedido_material = $object->id;
                        $pivot->id_item  = $id_item[$i];
                        $pivot->quantidade  = $param['quantidade'][$i];

                        $tools = lista::where('id_item', 'in', $id_item)->load();
                        $qtdTools = [];
                        foreach ($tools as $key) {
                            $qtdTools[] = $key->quantidade_estoque;
                        }   
                        
                        //Verifica se a quantidade solicitada for maior que a do estoque 
                        if ($param['quantidade'][$i] >= $qtdTools[$i] or $param['quantidade'][$i] < 0) {
                            throw new Exception(
                                'A quantidade na ' . ($i + 1) . '° linha não pode ser maior que a disponível no estoque que é: ' . $qtdTools[$i]);
                        } 
                        else {
                            $pivot->quantidade_estoque = $param['quantidade'][$i];
                            $result = $qtdTools[$i] - $param['quantidade'][$i]; //valor subtraido.
                            $this->updateQuantidade($pivot->id_item, $result);
                        }
                        $pivot->store();
                    }
                }
            }
          
            TTransaction::close(); // close the transaction
            $action = new TAction(array(PeididoList, 'onReload'));
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),$action);
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();

            $this->fireEvents($param);
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
            lista::where('id_item', '=', $id)
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
        var_dump($param);
        if (!empty($param['id_item'])) {
            TTransaction::open('bancodados');
            $emprestimo = pedido::where ('id', '=', $param['id']);
            $this->form->setData($emprestimo); //inserindo dados no formulario. 

            $pivot = pivot::where('id_pedido_material', '=', $emprestimo->id)->load();

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
    public function onChange($param)
    {

        TTransaction::open('bancodados');
        empty($param['id_item']) ? $id_item = $param : $id_item = $param['id_item'];
        $id_item = lista::where('id_item', 'in', $id_item)->load();
        $obj = new stdClass;
        $obj->quantidade_estoque = $id_item[0]->quantidade_estoque;
        TCombo::reload('my_form', 'quantidadeDisponivel', $obj);
        TTransaction::close();

        /////////////// PRENCHER CAMPO ID COM VALOR DA DESCRICAO
        /*try {
            TTransaction::open('bancodados'); // abre uma transação
            $list = lista::where('id_item', '=', $param['id_item'])->load();
            var_dump($list[0]->descricao);

            TTransaction::close(); // fecha a transação.
            $obj = new stdClass();
            $obj->descricao = $list[0]->descricao;

            $this->descricao = $list[0]->descricao;
            TForm::sendData('my_form', $this->descricao);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }*/
    }
}
