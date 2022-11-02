<?php

use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Sabberworm\CSS\Value\Value;

/**
 * FormNestedBuilderView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AprovacaoSolicitacaoForm extends TPage
{
    protected $form;
    protected $fieldlist;
    protected $datagrid;
    protected $pageNavigation;

    /**
     * Class constructor
     * Creates the page
     */
    function __construct($param = null)
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_SaleMultiValue');
        $this->form->setFormTitle('Aprovar solicitação de material');

        // create the form fields
        $id             = new TEntry('id');
        $created             = new TDateTime('created_at');
        $status             = new TCombo('status');
        $status->addItems(['Pendente' => 'Pendente', 'Efetuado' => 'Efetuado', 'Devolvido' => 'Devolvido', 'Não devolvido' => 'Não devolvido']);
        $ferramenta = new TEntry('ferramenta[]');
        $quantidade = new TEntry('quantidade[]');
        $qtdEmprestada = new TEntry('qtd_emprestada[]');

        //Config dos campos
        $id->setSize('20%');
        $id->setEditable(FALSE);

        $created->setSize('60%');
        $created->setEditable(FALSE);

        $status->setSize('100%');
        $status->setDefaultOption(false);

        $ferramenta->setSize('90%');
        $ferramenta->setEditable(FALSE);

        $quantidade->setSize('100%');
        $quantidade->setEditable(FALSE);

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '90%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Qtd solicitada</b><font color="red">*</font>',   $quantidade,   ['width' => '100%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Qtd emprestada</b><font color="red">*</font>',   $qtdEmprestada,   ['width' => '10%'], new TRequiredValidator);

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios')],
        );

        $row = $this->form->addFields(
            [new TLabel('Id da solicitação')],
            [$id],
            [new TLabel('Data da solicitação')],
            [$created],
            [new TLabel('Status da solicitação <font color="red">*</font>')],
            [$status],
        );
        $row->style = 'margin-top:3rem;';
        //$status->setValue('Efetuado');

        //add itens ao field list
        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);
        $this->form->addField($qtdEmprestada);
        $this->fieldlist->disableRemoveButton();

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%; margin:40px';
        $container->add($this->form);

        parent::add($container);
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('bancodados');
                $emprestimo = Emprestimo::find($param['key']);
                $this->form->setData($emprestimo); //inserindo dados no formulario. 

                $pivot = PivotEmprestimoFerramentas::where('id_emprestimo', '=', $emprestimo->id)->load();

                if ($pivot) {
                    $this->fieldlist->addHeader();
                    foreach ($pivot as $itens => $value) {
                        $obj = new stdClass;
                        $obj->ferramenta = intval($value->id_ferramenta);
                        $obj->quantidade = $value->quantidade;
                        $obj->qtd_emprestada = $value->quantidade;

                        $this->fieldlist->addDetail($obj);
                    }
                }
                // add field list to the form
                $this->form->addContent([$this->fieldlist]);
                TTransaction::close();
            } else {
                $this->onClear($param);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onSave($param)
    {
        //echo "<pre>";var_dump($param['status'] == 'Pendente');exit;
        try {
            $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');
            $usuarioLogado = TSession::getValue('userid');
            if ($param['status'] == "Pendente") {
                throw new Exception('Não pode aprovar uma solicitação com status "pendente"');
            } else {
                //Verificando se é uma edição ou criação
                if (isset($param["id"]) && !empty($param["id"])) {
                    $emprestimo = new Emprestimo($param["id"]);
                    $emprestimo->id_usuario = $emprestimo->id_usuario;
                    $emprestimo->id_admin = $usuarioLogado;
                    $emprestimo->status = $param['status'];
                }
                $emprestimo->fromArray($param);
                $emprestimo->store();

                //Delete emprestimo se existe.
                PivotEmprestimoFerramentas::where('id_emprestimo', '=', $emprestimo->id)->delete();

                $ferramentas = $param['ferramenta'];
                $tool = new Ferramentas($param['ferramenta']);
                $count = count($ferramentas);
                //Salvando items na tela pivot. 
                if (isset($ferramentas)) {
                    for ($i = 0; $i < $count; $i++) {
                        $pivot =  new PivotEmprestimoFerramentas();
                        $pivot->id_emprestimo = $emprestimo->id;
                        $pivot->id_ferramenta = $param['ferramenta'][$i];
                        $pivot->quantidade = $param['quantidade'][$i];
                        if ($tool->quantidade < array_values($param['qtd_emprestada'])) {
                            throw new Exception(
                                'A quantidade na linha ' . ($i + 1) . ' não pode ser maior que a disponível no estoque que é: ' . $tool->quantidade
                            );
                        } elseif ($param['quantidade'] < $param['qtd_emprestada']) {
                            throw new Exception(
                                'A quantidade empretada na linha ' . ($i + 1) . ' não pode ser maior que a quantidade solicitada'
                            );
                        } else {
                            $pivot->qtd_emprestada = $param['qtd_emprestada'][$i];
                        }
                        $pivot->store();
                    }
                }
            }
            TTransaction::close();
            new TMessage('info', 'Salvo com sucesso');
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
