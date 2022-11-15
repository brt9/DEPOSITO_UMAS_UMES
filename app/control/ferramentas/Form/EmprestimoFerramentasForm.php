<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;

use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;

use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;
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
    protected $subFormFirst;
    protected $subFormSecound;
    protected $fieldlist;

    protected $html;


    public function __construct()
    {
        TPage::include_css('app/resources/styles.css');
        parent::__construct();

        // create form and table container
        $this->form = new BootstrapFormBuilder('form_Emprestimo');
        $this->form->setFormTitle("Solicitação de emprestimo");

        $this->subFormFirst = new BootstrapFormBuilder('subFormFirst');
        $this->subFormSecound = new BootstrapFormBuilder('subFormSecound');

        $id             = new TEntry('id');
        $id->class = 'emprestimo';
        $id->setEditable(FALSE);
        $id->setSize('20%');

        $created             = new TDateTime('created_at');
        $created->class = 'emprestimo';

        $created->setEditable(FALSE);
        $created->setSize('40%');

        $ferramenta = new TDBCombo('ferramenta[]', 'bancodados', 'Ferramentas', 'id', '{id} - {nome}', 'id');
        $ferramenta->class = 'emprestimo';
        $ferramenta->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';

        $quantidade = new TEntry('quantidade[]');
        $quantidade->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';

        $quantidadeDisponivel = new TCombo('quantidadeDisponivel');
        $quantidadeDisponivel->class = 'emprestimo';
        $quantidadeDisponivel->style =
            'border-radius: 0.25rem;
            border-width: 1px;
            border-style: solid;';

        $ferramenta->placeholder = 'Pesquise pela ferramenta desejada';
        $ferramenta->enableSearch();
        $ferramenta->setSize('100%');
        $quantidade->setSize('100%');
        $quantidadeDisponivel->setSize('100%');
        $quantidadeDisponivel->setEditable(FALSE);
        $ferramenta->setChangeAction(new TAction(array($this, 'onChange')));

        //add field 
        $this->fieldlist = new TFieldList;
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField('<b>Ferramenta</b><font color="red">*</font>',  $ferramenta,  ['width' => '70%']);
        $this->fieldlist->addField('<b>Quantidade</b><font color="red">*</font>',   $quantidade,   ['width' => '10%']);
        $this->fieldlist->addField('<b>Quantidade disponível</b><font color="red">*</font>',   $quantidadeDisponivel,   ['width' => '10%']);
        $this->subFormSecound->addField($ferramenta);
        $this->subFormSecound->addField($quantidade);

        $row = $this->form->addFields(
            [$labelInfo = new TLabel('Campos com asterisco (<font color="red">*</font>) são considerados campos obrigatórios')],
        );

        $row = $this->subFormFirst->addFields(
            [new TLabel('id')],
            [$id],
            [new TLabel('Data')],
            [$created],
        );
        $this->subFormFirst->class = 'Emprestimo';
        
        $this->form->addContent([$this->subFormFirst]);


        // form actions
        $btnBack = $this->form->addActionLink(_t('Back'), new TAction(array('EmprestimoList', 'onReload')), 'far:arrow-alt-circle-left white');
        $btnBack->style = 'background-color:gray; color:white';
        $btnClear = $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser White');
        $btnClear->style = 'background-color:#c73927; color:white';
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save white');
        $btnSave->style = 'background-color:#218231; color:white';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%;';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    /**

     * Metodo identifica se criando ou editando e colocar itens no formulário.
     * @var param request
     * @return View forms 
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
        }
    }
    /**
     * Metodo para salvar solicitação
     * @var param
     * @return void 

     */
    public function onSave($param)
    {
        try {
            $this->form->validate();
            // open a transaction with database 'samples'
            TTransaction::open('bancodados');

            $usuarioLogado = TSession::getValue('userid');

            $duplicates = $this->getDuplicates($param['ferramenta']);
            if (($param['ferramenta'] == [""]) || ($param['quantidade'] == ['0'])) {
                throw new Exception('Campo obrigatorio não pode ser vazio');
            } else {

                //Verificando se é uma edição ou criação
                if (isset($param["id"]) && !empty($param["id"])) {
                    $emprestimo = new Emprestimo($param["id"]);
                    $emprestimo->id_usuario = $usuarioLogado;

                    $emprestimo->status = 'PENDENTE';
                } else {
                    $emprestimo = new Emprestimo();
                    $emprestimo->id_usuario = $usuarioLogado;
                    $emprestimo->status = 'PENDENTE';

                }
                $emprestimo->fromArray($param);
                $emprestimo->store();

                //Delete emprestimo se existe.
                PivotEmprestimoFerramentas::where('id_emprestimo', '=', $emprestimo->id)->delete();

                $ferramentas = array_map(function ($value) {
                    return (int)$value;
                }, $param['ferramenta']);

                //Salvando items na tela pivot. 
                if (isset($ferramentas)) {
                    for ($i = 0; $i < count($ferramentas); $i++) {


                        if (empty($param['quantidade'][$i])) {
                            throw new Exception('A quantidade está vazia na linha ' . ($i + 1));
                        }
                        if (empty($ferramentas[$i])) {
                            throw new Exception('A ferramenta está vazia na linha ' . ($i + 1));
                        }
                        if (!empty($duplicates[$i])) {
                            throw new Exception('Ferramenta repetida na linha ' . ($i + 1) . '. Uma ferramentas nao poder ser solicitada mais de uma vez');
                        }
                        $pivot =  new PivotEmprestimoFerramentas();
                        $pivot->id_emprestimo = $emprestimo->id;
                        $pivot->id_ferramenta = $param['ferramenta'][$i];
                        $tools = Ferramentas::where('id', 'in', $ferramentas)->load();
                        $qtdTools = [];
                        foreach ($tools as $key) {
                            $qtdTools[] = $key->quantidade;
                        }
                        //Verifica se a quantidade solicitada for maior que a do estoque 
                        if ($param['quantidade'][$i] <= $qtdTools[$i]) {
                            $pivot->quantidade = $param['quantidade'][$i];
                            $result = $qtdTools[$i] - $param['quantidade'][$i];//valor subtraido.
                            $this->updateQuantidade($pivot->id_ferramenta, $result);
                        } else {
                            throw new Exception(
                                'A quantidade na ' . ($i + 1) . '° linha não pode ser maior que a disponível no estoque que é: ' . $qtdTools[$i]

                            );
                        } else {
                            $pivot->quantidade = $param['quantidade'][$i];
                            $result = $qtdTools[$i] - $param['quantidade'][$i]; //valor subtraido.
                            $this->updateQuantidade($pivot->id_ferramenta, $result);
                        }
                        $pivot->store();
                    }
                }
            }

            TTransaction::close();
            $this->fireEvents($param);

            new TMessage('info', 'Salvo com sucesso');
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();

            $this->fireEvents($param);
        }
    }
    /**
     * Atualizar a quantidade de ferramentas
     * @var id id da ferramenta
     * @var value valor da ferramenta a ser atualizado
     */
    public function updateQuantidade($id, $value)
    {
        try {
            TTransaction::open('bancodados');
            Ferramentas::where('id', '=', $id)
                ->set('quantidade', $value)
                ->update();
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    /**
     * Limpar todos o formulário
     * @var param request 
     * @return View forms
     */
    public function onClear($param)
    {
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail(new stdClass);
        $this->fieldlist->addCloneAction();
        $this->form->addContent([$this->fieldlist]);
    }
    /**
     * Fire form events
     * @param $param Request
     */
    public function fireEvents($param)
    {
        if (!empty($param['id'])) {
            TTransaction::open('bancodados');
            $emprestimo = Emprestimo::find($param['id']);
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
    }
    /**
     * Verificar se existe ferramentas(ou qualquer outro parametro) duplicados no formulário
     * @var param 
     * @return Array
     */
    function getDuplicates($param)
    {
        return array_unique(array_diff_assoc($param, array_unique($param)));
    }
    /**
     * Gerar pdf das ferramentas solicitadas. 
     */
    public function onGenerate($param = null)
    {
        try {
            TTransaction::open('bancodados');

            /*             $model = model::find($param['id'])

                $replaces = $model->toArray();

                // verificar de tem campo vazio 
                foreach ($replaces as $k => $v){
                verificação 		
                }

                $replace['date'] = TData::date2br($model->data);
                $replace['campoTextArea'] = strip_tags($model->textArea); */
            // load all customers
            $this->html = new THtmlRenderer('app/resources/pdf.html');

            $emprestimo = new Emprestimo($param['id']);
            $pivot = new PivotEmprestimoFerramentas($emprestimo->id);

            $replace = $emprestimo->toArray();
            $replace['created_at'] = TDate::date2br($emprestimo->created_at);

            $this->html->enableSection('main', $replace);

            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            $contents = $this->html->getContents();

            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());

            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // write and open file
            file_put_contents('app/output/document.pdf', $dompdf->output());

            // open window to show pdf
            $window = TWindow::create(_t('Document HTML->PDF'), 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = 'app/output/document.pdf';
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }

    }
    public static function onChange($param)
    {
        TTransaction::open('bancodados');
        empty($param['ferramenta']) ? $ferramentaId = $param : $ferramentaId = $param['ferramenta'];
        $ferramenta = Ferramentas::where('id', 'in', $ferramentaId)->load();
        $obj = new stdClass;
        $obj->quantidadeDisponivel = $ferramenta[0]->quantidade;
        TCombo::reload('form_Emprestimo', 'quantidadeDisponivel', $obj);
        TTransaction::close();
    }
}
