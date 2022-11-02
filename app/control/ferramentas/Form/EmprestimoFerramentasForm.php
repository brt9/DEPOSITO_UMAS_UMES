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
 * FormNestedBuilderView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class EmprestimoFerramentasForm extends TPage
{
    protected $form;
    protected $fieldlist;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();

        // create form and table container
        $this->form = new BootstrapFormBuilder('form_SaleMultiValue');
        $this->form->setFormTitle("Solicitação de emprestimo");

        $id             = new TEntry('id');
        $created             = new TDateTime('created_at');
        $ferramenta = new TDBCombo('ferramenta[]', 'bancodados', 'Ferramentas', 'id', '{id} - {nome}', 'id');
        $quantidade = new TSpinner('quantidade[]');

        $ferramenta->placeholder = 'Pesquise pela ferramenta desejada';
        $ferramenta->enableSearch();
        $ferramenta->setSize('100%');
        $quantidade->setSize('100%');
        $created->setEditable(FALSE);
        $id->setEditable(FALSE);
        $id->setSize('20%');
        $created->setSize('70%');

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '70%'], new TRequiredValidator);
        $this->fieldlist->addField('<b>Quantidade</b><font color="red">*</font>',   $quantidade,   ['width' => '10%'], new TRequiredValidator);

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios')],
        );

        $this->form->addFields(
            [new TLabel('id')],
            [$id],
            [new TLabel('Data')],
            [$created],
        );
        $this->form->addField($ferramenta);
        $this->form->addField($quantidade);

        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%;';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    /**
     * Metodo para salvar solicitação
     */
    public function onSave($param)
    {
        try {
            $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');

            $usuarioLogado = TSession::getValue('userid');
            $status = array('Pendente', 'Efetuado', 'Devolvido', 'Não devolvido');

            if (($param['ferramenta'] == [""]) || ($param['quantidade'] == ['0'])) {
                throw new Exception('Campo obrigatorio não pode ser vazio');
            } else {

                //Verificando se é uma edição ou criação
                if (isset($param["id"]) && !empty($param["id"])) {
                    $emprestimo = new Emprestimo($param["id"]);
                    $emprestimo->id_usuario = $usuarioLogado;
                    $emprestimo->status = $status[0];
                } else {
                    $emprestimo = new Emprestimo();
                    $emprestimo->id_usuario = $usuarioLogado;
                    $emprestimo->status = $status[0];
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
                        //Verifica se a quantidade solicitada for maior que a do estoque 
                        if ($tool->quantidade < array_values($param['quantidade'])) {
                            throw new Exception(
                                'A quantidade na linha '. ($i+1) .' não pode ser maior que a disponível no estoque que é: '. $tool->quantidade
                            );
                        }else{
                            $pivot->quantidade = $param['quantidade'][$i];
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
    /**
     * Metodo identifica se criando ou editando e colocar itens no formulário.
     */
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
                        $obj->ferramenta = $value->id_ferramenta;
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
        }
    }
    public function onClear($param)
    {
    }
}
