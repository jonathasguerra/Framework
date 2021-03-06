<?php
class locais_locaisControle extends locais_Controle
{

    public function __construct(){
        parent::__construct();
    }
    public function Main(){
        return false;   
    }
    static function Endereco_Local($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($true===true){
            $_Controle->Tema_Endereco('Locais','locais/locais/Locais');
        }else{
            $_Controle->Tema_Endereco('Locais');
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Locais($export=false){
        self::Endereco_Local(false);
        $i = 0;
        // Add BOtao
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Local',
                'locais/locais/Locais_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'locais/locais/Locais',
            )
        )));
        // Query
        $setores = $this->_Modelo->db->Sql_Select('Local');
        if($setores!==false && !empty($setores)){
            if(is_object($setores)) $setores = Array(0=>$setores);
            reset($setores);
            foreach ($setores as $indice=>&$valor) {
                $tabela['Tipo de Local'][$i]             = $valor->categoria2;
                $tabela['Nome'][$i]             = $valor->nome;
                $tabela['Funções'][$i]          = $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Local'        ,'locais/locais/Locais_Edit/'.$valor->id.'/'    ,'')).
                                                  $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Local'       ,'locais/locais/Locais_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Local ?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Locais');
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
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Local</font></b></center>');
        }
        $titulo = 'Listagem de Locais ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Locais');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Locais_Add(){
        self::Endereco_Local(true);
        // Carrega Config
        $titulo1    = 'Adicionar Local';
        $titulo2    = 'Salvar Local';
        $formid     = 'form_Sistema_Admin_Locais';
        $formbt     = 'Salvar';
        $formlink   = 'locais/locais/Locais_Add2/';
        $campos = Local_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Locais_Add2(){
        $titulo     = 'Local Adicionado com Sucesso';
        $dao        = 'Local';
        $funcao     = '$this->Locais();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Local cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Locais_Edit($id){
        self::Endereco_Local(true);
        // Carrega Config
        $titulo1    = 'Editar Local (#'.$id.')';
        $titulo2    = 'Alteração de Local';
        $formid     = 'form_Sistema_AdminC_LocalEdit';
        $formbt     = 'Alterar Local';
        $formlink   = 'locais/locais/Locais_Edit2/'.$id;
        $editar     = Array('Local',$id);
        $campos = Local_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Locais_Edit2($id){
        $titulo     = 'Local Editado com Sucesso';
        $dao        = Array('Local',$id);
        $funcao     = '$this->Locais();';
        $sucesso1   = 'Local Alterado com Sucesso.';
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
    public function Locais_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa setor e deleta
        $setor = $this->_Modelo->db->Sql_Select('Local', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($setor);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Local deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Locais();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Local deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
