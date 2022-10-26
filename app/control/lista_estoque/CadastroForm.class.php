<?php

use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;

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
class CadastroForm extends TStandardForm
{
    protected $form; //  FORMULÁRIO

    // CONSTRUTOR DE CLASSE
    // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

    function __construct()
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        $this->setDatabase('bancodados');              // DEFINE O BANCO DE DADOS
        $this->setActiveRecord('lista');               // DEFINE O REGISTRO ATIVO

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('form_cadastro');
        $this->form->setFormTitle('Cadastro Items DEPOSITO');

        // CRIE OS CAMPOS DO FORMULÁRIO
        $CODIGO = new TEntry('id_item');
        $DESCRICAO = new TEntry('descricao');
        $QUANTIDADE_ESTOQUE = new TEntry('quantidade_estoque');
        $colaborador_responsavel = new TEntry('id_usuario');


        // ADICIONE OS CAMPOS
        $this->form->addFields([new TLabel('CODIGO ITEM')], [$CODIGO]);
        $this->form->addFields([new TLabel('DESCRIÇÃO')], [$DESCRICAO]);
        $this->form->addFields([new TLabel('QUANTIDADE EM ESTOQUE')], [$QUANTIDADE_ESTOQUE]);
        $this->form->addFields([new TLabel('COLABORADOR RESPONSAVEL')], [$colaborador_responsavel]);

        $CODIGO->addValidation('CODIGO ITEM', new TRequiredValidator);
        $CODIGO->addValidation('CODIGO ITEM', new TRequiredValidator);



        $CODIGO->setSize('35%');
        $DESCRICAO->setSize('70%');
        $QUANTIDADE_ESTOQUE->setSize('35%');
        $colaborador_responsavel->setSize('35%');
        $colaborador_responsavel->setValue(TSession::getValue('userid'));
        $colaborador_responsavel->setEditable(FALSE);



        // CRIE AS AÇÕES DO FORMULÁRIO
        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('Clear'),  new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'), new TAction(array('CadastroList', 'onReload')), 'far:arrow-alt-circle-left blue');

        // RECIPIENTE DE CAIXA VERTICAL
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CadastroList'));
        $container->add($this->form);

        parent::add($container);
    }
}
   /* $data_cadastro->setEditable(FALSE);
        $nome->setSize('70%');
        $nome->addValidation(('NOME'), new TRequiredValidator);
        $nome->forceUpperCase();
        $telefone->setSize('30%');
        $telefone->setMask('+(99) 99999-9999');*/

        /*
        $numero->setSize('25%');
        $complemento->setSize('50%');
        $cep->setMask('99.999-999');
  */
     /*$this->form->addFields([new TLabel('DATA CADASTRO')], [$data_cadastro]);
        $this->form->addFields([new TLabel('NOME')], [$nome]);
        $this->form->addFields([new TLabel('SEXO')], [$sexo]);
        $this->form->addFields([new TLabel('EMAIL')], [$email]);
        $this->form->addFields([new TLabel('TELEFONE')], [$telefone]);
        $this->form->addFields([new TLabel('CANAL')], [$canal]);
        $this->form->addFields([new TLabel('DATA RECEBIMENTO')], [$data_recebimento]);
        $this->form->addFields([$fase_atual]);*/

        //$DATA->setDatabaseMask("Y-m-d H:i:s");

        /*  $CODIGO->setSize('15%');
        $CODIGO->setEditable(FALSE);
        $nome->setSize('70%');
        $data_cadastro->setSize('15%');
        $email->setSize('30%');
        $canal->setSize('30%');
        $data_recebimento->setSize('15%');
        $sexo->setSize('15%');*/
          /*$data_cadastro = new TEntry('data_cadastro');
        $nome = new TEntry('nome_cliente');
        $sexo = new TDBCombo('sexo', 'bancodados', 'sexo', 'nome_sexo', 'nome_sexo');
        $telefone = new TEntry('telefone');
        $email = new TEntry('email');
        $canal = new TEntry('canal');
        $data_recebimento = new TDate('data_recebimento');
        $data_cadastro->setValue(date("Y-m-d H:i:s"));
        $fase_atual = new THidden('fase_atual');
        $fase_atual->setValue('QUALIFICACAO');*/