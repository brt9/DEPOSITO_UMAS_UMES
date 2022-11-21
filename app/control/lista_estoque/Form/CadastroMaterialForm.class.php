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
class CadastroMaterialForm extends TStandardForm
{
  protected $form; //  FORMULÁRIO
  protected $subform; //  FORMULÁRIO

  // CONSTRUTOR DE CLASSE
  // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

  function __construct()
  {
    TStandardForm::include_css('app/resources/styles.css');
    parent::__construct();

    $ini  = AdiantiApplicationConfig::get();

    $this->setDatabase('bancodados');              // DEFINE O BANCO DE DADOS
    $this->setActiveRecord('Material');               // DEFINE O REGISTRO ATIVO

    // CRIA O FORMULÁRIO
    $this->form = new BootstrapFormBuilder('form_material');
    $this->subform = new BootstrapFormBuilder('subform_material');
    $this->form->setFormTitle('<b>CADASTRO ITENS DEPOSITO</b>');

    // CRIE OS CAMPOS DO FORMULÁRIO
    $codigo = new TEntry('id_item');
    $codigo->id = "input-form";
    $descricao = new TEntry('descricao');
    $quantidadeEstoque = new TEntry('quantidade_estoque');
    $colaborador_responsavel = new THidden('id_usuario');

    $row = $this->form->addFields(
      [$labelInfo = new TLabel('<b>Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios</b>')],
    );

    // ADICIONE OS CAMPOS
    $row = $this->form->addFields([new TLabel('Codigo do item <font color="red">*</font>')], [$codigo]);
    $this->form->addFields([new TLabel('Descrição <font color="red">*</font>')], [$descricao]);
    $this->form->addFields([new TLabel('Quantidade <font color="red">*</font>')], [$quantidadeEstoque]);
    $this->form->addFields([new TLabel('colaborador')], [$colaborador_responsavel]);

    $codigo->addValidation('Codigo do item <font color="red">*</font>', new TRequiredValidator);
    $descricao->addValidation('Descrição <font color="red">*</font>', new TRequiredValidator);
    $quantidadeEstoque->addValidation('Quantidade <font color="red">*</font>', new TRequiredValidator);
    $colaborador_responsavel->addValidation('COLABORADOR RESPONSAVEL <font color="red">*</font>', new TRequiredValidator);

    $codigo->setTip('Digite o codigo do item que deseja cadastrar');
    $codigo->placeholder = '00000';
    $codigo->setSize('25%');
    $codigo->setMask('99999');
    $codigo->maxlength = 5;

    $descricao->setTip('Digite a descrição do item desejado');
    $descricao->setSize('70%');
    $descricao->placeholder = 'Descrição do Item';

    $quantidadeEstoque->setTip('Digite a quantidade do item desejado');
    $quantidadeEstoque->setSize('70%');
    $quantidadeEstoque->placeholder = 'Descrição do Item';
    $quantidadeEstoque->setMask('99999');
    $quantidadeEstoque->placeholder = '00000';

    $colaborador_responsavel->setSize('70%');
    $colaborador_responsavel->setValue(TSession::getValue('userid'));
    $colaborador_responsavel->setEditable(FALSE);

    // CRIE AS AÇÕES DO FORMULÁRIO
    $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
    $btn->class = 'btn btn-sm btn-primary';
    $this->form->addActionLink(_t('Clear'),  new TAction(array($this, 'onEdit')), 'fa:eraser red');
    $this->form->addActionLink(_t('Back'), new TAction(array('MaterialList', 'onReload')), 'far:arrow-alt-circle-left blue');

    // RECIPIENTE DE CAIXA VERTICAL
    $container = new TVBox;
    $container->style = 'width: 100%';
    $container->add(new TXMLBreadCrumb('menu.xml', 'MaterialList'));
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

        /*  $codigo->setSize('15%');
        $codigo->setEditable(FALSE);
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