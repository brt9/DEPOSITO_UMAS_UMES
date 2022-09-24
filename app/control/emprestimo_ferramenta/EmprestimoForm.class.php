<?php

use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\THidden;

/**
 * FORMULÁRIO DE CADASTRO
 *
 * @version    1.0
 * @package    model
 * @subpackage DEPOSITO DE MATERIAS UMAS E UMES
 * @author     PEDRO FELIPE FREIRE DE MEDEIROS
 * @copyright  Copyright (c) 2021 Barata
 * @license    http://www.adianti.com.br/framework-license
 */
class EmprestimoForm extends TStandardForm
{
    protected $form; // FORMULÁRIO

    // CONSTRUTOR DE CLASSE
    // CRIA A PÁGINA E O FORMULÁRIO DE INSCRIÇÃO

    function __construct()
    {
        parent::__construct();

        $ini  = AdiantiApplicationConfig::get();

        // DEFINE O BANCO DE DADOS
        $this->setDatabase('bancodados');

        // DEFINE O REGISTRO ATIVO           
        $this->setActiveRecord('emprestimo');

        // CRIA O FORMULÁRIO
        $this->form = new BootstrapFormBuilder('form_Emprestimo_Ferramentas');
        $this->form->setFormTitle('Emprestimo de Ferramentas');

        // CRIE OS CAMPOS DO FORMULÁRIO
        $ID_EMPRESTIMO = new TEntry('ID_EMPRESTIMO');
        $FERRAMENTA = new TEntry('FERRAMENTA');
        $DATA_EMPRESTIMO = new TEntry('DATA_EMPRESTIMO');
        $DATA_DEVOLUCAO = new TEntry('DATA_DEVOLUCAO');
        $MATRICULA = new TEntry('MATRICULA');
        $COLABORADOR_RESPONSAVEL_EMPRESTIMO = new TEntry('COLABORADOR_RESPONSAVEL_EMPRESTIMO');


        //DEFINE A HORA ATUAL PARA VARIAVEL DATA EMPRESTIMO.
        $DATA_EMPRESTIMO->setValue(date("Y-m-d H:i:s"));


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

        // ADICIONE OS CAMPOS
        $this->form->addFields([new TLabel('ID_EMPRESTIMO')], [$ID_EMPRESTIMO]);
        $this->form->addFields([new TLabel('FERRAMENTA')], [$FERRAMENTA]);
        $this->form->addFields([new TLabel('DATA_EMPRESTIMO')], [$DATA_EMPRESTIMO]);
        $this->form->addFields([new TLabel('DATA_DEVOLUCAO')], [$DATA_DEVOLUCAO]);
        $this->form->addFields([new TLabel('COLABORADOR RESPONSAVEL')], [$COLABORADOR_RESPONSAVEL_EMPRESTIMO]);
        $this->form->addFields([new TLabel('MATRICULA')], [$MATRICULA]);
        /*$this->form->addFields([new TLabel('DATA CADASTRO')], [$data_cadastro]);
        $this->form->addFields([new TLabel('NOME')], [$nome]);
        $this->form->addFields([new TLabel('SEXO')], [$sexo]);
        $this->form->addFields([new TLabel('EMAIL')], [$email]);
        $this->form->addFields([new TLabel('TELEFONE')], [$telefone]);
        $this->form->addFields([new TLabel('CANAL')], [$canal]);
        $this->form->addFields([new TLabel('DATA RECEBIMENTO')], [$data_recebimento]);
        $this->form->addFields([$fase_atual]);*/

        //$DATA->setDatabaseMask("Y-m-d H:i:s");

        /*  $ID_EMPRESTIMO->setSize('15%');
        $ID_EMPRESTIMO->setEditable(FALSE);
        $nome->setSize('70%');
        $data_cadastro->setSize('15%');
        $email->setSize('30%');
        $canal->setSize('30%');
        $data_recebimento->setSize('15%');
        $sexo->setSize('15%');*/
        $COLABORADOR_RESPONSAVEL_EMPRESTIMO->setSize('70%');
        $COLABORADOR_RESPONSAVEL_EMPRESTIMO->setValue(TSession::getValue('username'));
        $COLABORADOR_RESPONSAVEL_EMPRESTIMO->setEditable(FALSE);
        $ID_EMPRESTIMO->setEditable(FALSE);
        $DATA_EMPRESTIMO->setEditable(FALSE);
        $DATA_DEVOLUCAO->setEditable(FALSE);
        $MATRICULA->setEditable(FALSE);
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
