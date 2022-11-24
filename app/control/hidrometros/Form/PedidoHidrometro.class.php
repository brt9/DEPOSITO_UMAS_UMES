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
use Adianti\Widget\Form\TLabel;

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
class PedidoHidrometro extends TPage
{
    protected $form; //  FORMULÁRIO
    //  FORMULÁRIO
    // CONSTRUTOR DE CLASSE
    // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

    function __construct($param)
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        //  $this->setDatabase('bancodados');              // DEFINE O BANCO DE DADOS
        //  $this->setActiveRecord('pedido');               // DEFINE O REGISTRO ATIVO

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('my_form');
        $this->form->setFormTitle('<b>FORMULARIO DE PEDIDO DE HIDROMETROS</b>');


        $id = new TEntry('id');
        $hidrometro = new TBarCodeInputReader('hidrometro[]');



        $id->setEditable(FALSE);

        $id->setSize('100%');
        $hidrometro->setSize('100%');
        $hidrometro->setTip('Digite o codigo do Hidrometro');
        $hidrometro->placeholder = 'Y22ZZZZZZ';
        $hidrometro->maxlength = 10;







        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->form->addFields([$id]);
        var_dump(($param['id'] == null));
        if ($param['id'] == null) {
            $this->fieldlist->addField('<b>HIDROMETRO</b><font color="red"> *</font>',  $hidrometro,  ['width' => '100%']);


            $row = $this->form->addFields(
                [$labelInfo = new TLabel('<b>Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios</b>')],
            );
    
            $row =  $this->fieldlist->addDetail(new stdClass);
            $this->fieldlist->addHeader();
    
    
            $this->form->addField($hidrometro);
            $this->fieldlist->addCloneAction();
        } else { $this->fieldlist->addField('<b>HIDROMETRO</b><font color="red"> *</font>',  $hidrometro,  ['width' => '100%']);
             $this->fieldlist->addHeader();
    
            $hidrometro->setEditable(FALSE);
            $this->fieldlist->disableRemoveButton();
        }


   


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
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);


        parent::add($vbox);
    }


    public function onSave($param)
    {
        try {
            $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');

            $usuarioLogado = TSession::getValue('userid');

            $duplicates = $this->getDuplicates($param['hidrometro']);
            if ($param['hidrometro'] == [""]) {
                throw new Exception('Campo Hidrometro é obrigatorio não pode ser vazio');
            }
            if ($param['descricao'] == [""]) {
                throw new Exception('Campo Descrição é obrigatorio não pode ser vazio');
            }
            if ($param['quantidade'] == ['0']) {
                throw new Exception('Campo Quantidade não pode ser vazio');
            } else {

                if (isset($param["id"]) && !empty($param["id"])) {
                    $object = new pedidohd($param["id"]);
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                } else {
                    $object = new pedidohd();
                    $object->id_usuario = $usuarioLogado;
                    $object->status = 'PENDENTE';
                }

                $object->fromArray($param);
                $object->store();

                pivothd::where('id_pedido_hidrometro', '=', $object->id)->delete();


                $hidrometro = array_map(function ($value) {
                    return (string)$value;
                }, $param['hidrometro']);


                if (isset($hidrometro)) {
                    for ($i = 0; $i < count($hidrometro); $i++) {



                        if (!empty($duplicates[$i])) {
                            throw new Exception('Item e repetido na linha ' . ($i + 1) . '. Uma ferramentas nao poder ser solicitada mais de uma vez');
                        }

                        $pivot = new pivothd();
                        $pivot->id_pedido_hidrometro = $object->id;
                        $pivot->hidrometro  = $hidrometro[$i];



                        //Verifica se a quantidade solicitada for maior que a do estoque 

                        $pivot->store();
                    }
                }
            }

            TTransaction::close(); // close the transaction


            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
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
    }
    public function onClear($param)
    {
    }
    public function fireEvents($param)
    { /*
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
        }*/
    }
    function getDuplicates($param)
    {
        return array_unique(array_diff_assoc($param, array_unique($param)));
    }
    public function onChange($param)
    {
        /*
        TTransaction::open('bancodados');
        empty($param['id_item']) ? $hidrometro = $param : $hidrometro = $param['id_item'];
        $hidrometro = lista::where('id_item', 'in', $hidrometro)->load();
        $obj = new stdClass;
        $obj->quantidade_estoque = $hidrometro[0]->quantidade_estoque;
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
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('bancodados');
                $emprestimo = pedidohd::find($param['key']);
                $this->form->setData($emprestimo); //inserindo dados no formulario. 

                $pivot = pivothd::where('id_pedido_hidrometro', '=', $emprestimo->id)->load();

                if ($pivot) {
                    // $this->fieldlist->addHeader();
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        //$obj->id_pedido_hidrometro = $value->id_pedido_hidrometro;
                        $obj->hidrometro = $value->hidrometro;

                        $this->fieldlist->addDetail($obj);
                        $this->fieldlist->disableRemoveButton();
                    }
                }
                // $this->onChange(array($pivot[0]->id_pedido_hidrometro));
                // add field list to the form
                //  $this->form->addContent([$this->fieldlist]);
                TTransaction::close();
            } else {
                //  $this->fieldlist->addHeader();
                //   $this->fieldlist->addDetail(new stdClass);
                // $this->fieldlist->addCloneAction();
                // $this->form->addContent([$this->fieldlist]);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->fireEvents($param);
        }
    }
}
