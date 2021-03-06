<?php
class projeto_TarefaControle extends projeto_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses projeto_ListarModelo Carrega projeto Modelo
    * @uses projeto_ListarVisual Carrega projeto Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    protected function Endereco_Tarefa($true=true){
        if($true===true){
            $this->Tema_Endereco('Tarefas','projeto/Tarefa/Tarefas');
        }else{
            $this->Tema_Endereco('Tarefas');
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses projeto_Controle::$projetoPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        return false;
    }
    static function Tarefas_Tabela($projetos){
        $registro   = \Framework\App\Registro::getInstacia();
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($projetos)) $projetos = Array(0=>$projetos);
        reset($projetos);
        foreach ($projetos as $indice=>&$valor) {
            $tabela['#Id'][$i]          =   '#'.$valor->id;
            $tabela['Categoria'][$i]    =   $valor->categoria2;
            $tabela['Projeto'][$i]      =   $valor->projeto2;
            $tabela['k'][$i]            =   $valor->framework;
            $tabela['Fk - Mód'][$i]     =   $valor->framework_modulo;
            $tabela['Fk - SubMód'][$i]  =   $valor->framework_submodulo;
            $tabela['Fk - Mét'][$i]     =   $valor->framework_metodo;
            $tabela['Descrição'][$i]    =   $valor->descricao;
            $tabela['Funções'][$i]      =   $Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Tarefa'        ,'projeto/Tarefa/Tarefas_Edit/'.$valor->id.'/'    ,'')).
                                            $Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Tarefa'       ,'projeto/Tarefa/Tarefas_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Tarefa ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas($export=false){
        $this->Endereco_Tarefa(false);
        $i = 0;
        // Add BOtao
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Tarefa',
                'projeto/Tarefa/Tarefas_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'projeto/Tarefa/Tarefas',
            )
        )));
        // Query
        $projetos = $this->_Modelo->db->Sql_Select('Projeto_Tarefa');
        if($projetos!==false && !empty($projetos)){
            list($tabela,$i) = self::Tarefas_Tabela($projetos);
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Tarefas');
            }else{
                $Visual->Show_Tabela_DataTable(
                    $tabela,     // Array Com a Tabela
                    '',          // style extra
                    true,        // true -> Add ao Bloco, false => Retorna html
                    true,        // Apagar primeira coluna ?
                    Array(       // Ordenacao
                        Array(
                            0,'desc'
                        )
                    )
                );
            }
            unset($tabela);
        }else{    
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Tarefa</font></b></center>');
        }
        $titulo = 'Listagem de Tarefas ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Tarefas');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas_Add(){
        $this->Endereco_Tarefa();
        // Carrega Config
        $titulo1    = 'Adicionar Tarefa';
        $titulo2    = 'Salvar Tarefa';
        $formid     = 'form_Sistema_Admin_Tarefas';
        $formbt     = 'Salvar';
        $formlink   = 'projeto/Tarefa/Tarefas_Add2/';
        $campos = Projeto_Tarefa_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas_Add2(){
        $titulo     = 'Tarefa Adicionada com Sucesso';
        $dao        = 'Projeto_Tarefa';
        $funcao     = '$this->Tarefas();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Tarefa cadastrada com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas_Edit($id){
        $this->Endereco_Tarefa();
        // Carrega Config
        $titulo1    = 'Editar Tarefa (#'.$id.')';
        $titulo2    = 'Alteração de Tarefa';
        $formid     = 'form_Sistema_AdminC_TarefaEdit';
        $formbt     = 'Alterar Tarefa';
        $formlink   = 'projeto/Tarefa/Tarefas_Edit2/'.$id;
        $editar     = Array('Projeto_Tarefa',$id);
        $campos = Projeto_Tarefa_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas_Edit2($id){
        $titulo     = 'Tarefa Editada com Sucesso';
        $dao        = Array('Projeto_Tarefa',$id);
        $funcao     = '$this->Tarefas();';
        $sucesso1   = 'Tarefa Alterada com Sucesso.';
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Tarefas_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $linha = $this->_Modelo->db->Sql_Select('Projeto_Tarefa', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($linha);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Tarefa Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Tarefas();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Tarefa Deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
