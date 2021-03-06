<?php
class categoria_AdminControle extends categoria_Controle
{
    public function __construct(){
        parent::__construct();
    }
    protected function Endereco_Categoria($true=true){
        if($true===true){
            $this->Tema_Endereco('Categorias','categoria/Admin/Categorias');
        }else{
            $this->Tema_Endereco('Categorias');
        }
    }
    /**
     * 
     */
    public function Main(){
        return false;
    }
    public function Categorias($modulo=''){
        $this->Categorias_ShowTab($modulo);
        //$this->Categorias_Add();
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo','Categorias');
    }
    /**
    * Mostra todas as Categorias
    * 
    * @name Categorias_ShowTab
    * @access public
    * 
    * @param string $tipo Carrega Tipo de Categoria
    * 
    * @uses Tabela Carrega uma Nova Tabela
    * @uses Tabela::$addcabecario Carrega o topo da Tabela
    * @uses \Framework\App\Modelo::$Categorias_Retorna
    * @uses \Framework\App\Visual::$Categorias_ShowTab
    * @uses \Framework\App\Visual::$blocar
    * @uses \Framework\App\Visual::$Bloco_Maior_CriaJanela
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Categorias_ShowTab($tipo=''){
        self::Endereco_Categoria(false);
        $tabela = Array();
        $array = $this->_Modelo->Categorias_Retorna($tipo);
        $tabela = new \Framework\Classes\Tabela();
        $tabela->addcabecario(array('Id','Nome', 'Acesso','Editar'));   
        
        // Botao Add
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Categoria',
                'categoria/Admin/Categorias_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'categoria/Admin/Categorias',
            )
        )));
        // Conexao
        $this->_Visual->Categorias_ShowTab($array,$tabela);
        $this->_Visual->Blocar($tabela->retornatabela());
        $this->_Visual->Bloco_Unico_CriaJanela('Categorias');
        unset($tabela);        
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Categorias_Add($modulo=false){
        self::Endereco_Categoria(true);
        // Carrega Config
        if($modulo===false){
            $titulo1    = 'Adicionar Categoria';
            $titulo2    = 'Salvar Categoria';
        }else{
            $dados_modulo = Categoria_Acesso_DAO::Mod_Acesso_Get($modulo);
            $titulo1    = 'Adicionar '.$dados_modulo['nome'];
            $titulo2    = 'Adicionar '.$dados_modulo['nome'];            
        }
        $formid     = 'form_categoria_Admin_Categorias';
        $formbt     = 'Salvar';
        $formlink   = 'categoria/Admin/Categorias_Add2/';
        $campos     = Categoria_DAO::Get_Colunas();
        if($modulo!==false){
            $formlink = $formlink.$modulo.'/';
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('categoria_parent_extra')==false){
                self::DAO_Campos_Retira($campos, 'parent');
            }
            self::DAO_Campos_Retira($campos, 'Modulos Liberados');
        }
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Categorias_Add2($modulo=false){
        $titulo     = 'Adicionado com Sucesso';
        $dao        = 'Categoria';
        $funcao     = '$this->Categorias();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
        // Cadastra o modulo
        if($modulo!==false){
            $identificador  = $this->_Modelo->db->Sql_Select ($dao, Array(),1,'ID DESC');
            $identificador  = $identificador->id;
            $objeto = new \Categoria_Acesso_DAO();
            $objeto->categoria  = $identificador;
            $objeto->mod_acc    = $modulo;
            $sucesso = $this->_Modelo->db->Sql_Inserir($objeto);
            // Gambiarra para Atualiza select denovo
            $select = \anti_injection($_GET['formselect']);
            $condicao = \anti_injection($_GET['condicao']);
            $opcoes = $this->_Modelo->db->Tabelas_CapturaExtrangeiras($condicao);   
            $html = '';
            if($opcoes!==false && !empty($opcoes)){
                if(is_object($opcoes)) $opcoes = Array(0=>$opcoes);
                reset($opcoes);
                foreach ($opcoes as $indice=>&$valor) {
                    if($identificador==$indice){
                        $selecionado=1;
                    }
                    else{
                        $selecionado=0;
                    }
                    $html .= \Framework\Classes\Form::Select_Opcao_Stat($valor,$indice,$selecionado);
                }
            }
            // Json
            $this->_Visual->Json_RetiraTipo('#'.$select);
            $conteudo = array(
                'location'  =>  '#'.$select,
                'js'        =>  '$("#'.$select.'").trigger("liszt:updated");',
                'html'      =>  $html
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            $this->_Visual->Json_Info_Update('Historico', false);  
        }
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Categorias_Edit($id,$modulo=false){
        self::Endereco_Categoria(true);
        // Carrega Config
        if($modulo===false){
            $titulo1    = 'Editar Categoria (#'.$id.')';
            $titulo2    = 'Alteração de Categoria (#'.$id.')';
        }else{
            $dados_modulo = Categoria_Acesso_DAO::Mod_Acesso_Get($modulo);
            $titulo1      = 'Editar '.$dados_modulo['nome'].' (#'.$id.')';    
            $titulo2      = 'Editar '.$dados_modulo['nome'].' (#'.$id.')';    
        }
        $formid     = 'form_categoria_Admin_Categorias_Edit';
        $formbt     = 'Alterar Categoria';
        $formlink   = 'categoria/Admin/Categorias_Edit2/';
        $editar     = Array('Categoria',$id);
        if($modulo!==false){
            $formlink .= $formlink.$modulo.'/';
            self::DAO_Campos_Retira($campos, 'Modulos Liberados');
        }
        // Add id ao Link
        $formlink = $formlink.$id;
        // Captura campos e Formata
        $campos = Categoria_DAO::Get_Colunas();
        if($modulo!==false){
            self::DAO_Campos_Retira($campos, 'parent');
            self::DAO_Campos_Retira($campos, 'Modulos Liberados');
        }
        // Gera Formulario
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Categorias_Edit2($id,$modulo=false){
        $titulo     = 'Editado com Sucesso';
        $dao        = Array('Categoria',$id);
        $funcao     = '$this->Categorias();';
        $sucesso1   = 'Alterado com Sucesso.';
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
    public function Categorias_Del($id){
        global $language;
    	$id = (int) $id;
        // Puxa Categoria e Acessos
        $categorias = $this->_Modelo->db->Sql_Select('Categoria', Array('id'=>$id));
        $acesso = $this->_Modelo->db->Sql_Select('Categoria_Acesso', Array('categoria'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($categorias);
        // Mensagem
    	if($sucesso===true){
            $sucesso =  $this->_Modelo->db->Sql_Delete($acesso);
            if($sucesso===true){
                $mensagens = array(
                    "tipo" => 'sucesso',
                    "mgs_principal" => 'Deletado',
                    "mgs_secundaria" => 'Deletado com sucesso'
                );
            }else{
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => $language['mens_erro']['erro']
                );
            }
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Categorias();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
