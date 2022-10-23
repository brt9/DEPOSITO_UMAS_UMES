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
        $this->form->setFormTitle('FORMULARIO DE PEDIDO DE MATERIAL');

        $id             = new TEntry('id');
        $id->setEditable(FALSE);
        $id->setSize('20%');

        $id_item = new TQRCodeInputReader('id_item[]');
        $id_item->setSize('100%');

        $descricao = new TDBCombo('descricao[]', 'bancodados', 'lista', 'descricao', 'descricao');
        $descricao->enableSearch();
        $descricao->setSize('100%');


        $quantidade = new TSpinner('quantidade[]');
        $quantidade->setSize('100%');

        /* $id_status = new Thidden('id_status');
        $id_status->setSize('100%');


        $id_usuario = new Thidden('     ');
        $id_usuario->setSize('100%');*/



        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        // $this->fieldlist->addField('<b>STATUS</b>',   $id_status,   ['width' => '0%']);
        //$this->fieldlist->addField('<b>USUARIO</b>', $id_usuario, ['width' => '0%']);

        $this->fieldlist->addField('<b>CODIGO ITEM</b>',  $id_item,  ['width' => '20%']);
        $this->fieldlist->addField('<b>DESCRIÇÂO</b>',  $descricao,  ['width' => '60%']);
        $this->fieldlist->addField('<b>QUANTIDADE</b>',   $quantidade,   ['width' => '20%']);
        $this->form->addFields([new TLabel('id')], [$id]);
        // $this->form->addField($id_usuario);
        //  $this->form->addField($id_status);

        $this->form->addField($id_item);
        $this->form->addField($descricao);
        $this->form->addField($quantidade);

        // STATUS DO PEDIDO E USUARIO SOLICITANTE
        //$id_status->addValidation('STATUS', new TRequiredValidator);
        // $id_usuario->addValidation('USUARIO', new TRequiredValidator);


        $id_item->addValidation('CODIGO ITEM', new TRequiredValidator);
        $descricao->addValidation('DESCRIÇÂO', new TRequiredValidator);
        $quantidade->addValidation('QUANTIDADE', new TRequiredValidator);

        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addHeader();
        $this->fieldlist->addCloneAction();


        //$id_status->setEditable(FALSE);
        //$id_usuario->setEditable(FALSE);
        // add field list to the form
        $this->form->addContent([$this->fieldlist]);

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
    public function onSave($param)
    {
        try {
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');
            $usuarioLogado = TSession::getValue('userid');
            if (isset($param["id"]) && !empty($param["id"])) {
                $object = new pedido($param["id"]);
                $object->id_usuario = $usuarioLogado;
                $object->id_status = 1;
            }  else {
                    $object = new pedido();
                    $object->id_usuario = $usuarioLogado;
                    $object->id_status = 1;
            }
            $object->fromArray($param);
            $object->store();

            pivot::where('id_pedido_material','=',$object->id)->delete();

            $id_item = $param['id_item'];
            $quantidade = $param['quantidade'];
            $count = count($id_item);
            
     


         if (isset($id_item)) {
            for ($i = 0; $i < $count; $i++) {
            $pivot = new pivot();
            $pivot->id_pedido_material = $object->id;
            $pivot->codigo_item  = $id_item[$i];
            $pivot->quantidade  = $quantidade[$i];
            $pivot->store(); 
         
        }
    }

/*
           
          
           
        



          */
            TTransaction::close(); // close the transaction
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onClear($param)
    {
  
    }
    function onEdit($param)
    {
    }
}
