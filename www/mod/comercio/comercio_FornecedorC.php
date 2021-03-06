<?php
class comercio_FornecedorControle extends comercio_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses comercio_rede_PerfilModelo::Carrega Rede Modelo
    * @uses comercio_rede_PerfilVisual::Carrega Rede Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    static function Campos_Deletar(&$campos){
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Fornecedor_Categoria')){
            self::DAO_Campos_Retira($campos, 'categoria');
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses comercio_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'comercio/Fornecedor/Fornecedores');
        return false;
    }
    static function Endereco_Fornecedor($true=true,$produto=false){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Fornecedores';
        $link = 'comercio/Fornecedor/Fornecedores';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores($export = false){
        self::Endereco_Fornecedor(false);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Fornecedor',
                'comercio/Fornecedor/Fornecedores_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Fornecedor/Fornecedores',
            )
        )));
        $i = 0;
        $fornecedor = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor');
        if($fornecedor!==false && !empty($fornecedor)){
            if(is_object($fornecedor)) $fornecedor = Array(0=>$fornecedor);
            reset($fornecedor);
            foreach ($fornecedor as $indice=>&$valor) {
                $nome = '';
                if($valor->nome!=''){
                    $nome .= $valor->nome;
                }
                if($valor->razao_social!=''){
                    $nome .= $valor->razao_social;
                }
                $tabela['Nome'][$i]                 = $nome;
                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Fornecedor_Categoria')){
                      $tabela['Tipo de Fornecimento'][$i] = $valor->categoria2;
                }
                $telefone = '';
                if($valor->telefone!=''){
                    $telefone .= $valor->telefone;
                }
                if($valor->telefone2!=''){
                    if($telefone!='') $telefone .= '<br>';
                    $telefone .= $valor->telefone2;
                }
                $tabela['Telefone'][$i]      =  $telefone;
                $email = '';
                if($valor->email!=''){
                    $email .= $valor->email;
                }
                if($valor->email2!=''){
                    if($email!='') $email .= '<br>';
                    $email .= $valor->email2;
                }
                $tabela['Email'][$i]      =  $email;
                $tabela['Funções'][$i]   =  /*$this->_Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar Fornecedor'        ,'comercio/Fornecedor/Fornecedores_Popup/'.$valor->id.'/'    ,'')).*/
                                            $this->_Visual->Tema_Elementos_Btn('Zoom'       ,Array('Visualizar Comentários do Fornecedor'       ,'comercio/Fornecedor/Fornecedores_View/'.$valor->id.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Fornecedor'                          ,'comercio/Fornecedor/Fornecedores_Edit/'.$valor->id.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Fornecedor'                         ,'comercio/Fornecedor/Fornecedores_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Fornecedor ? Isso irá afetar o sistema!'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Fornecedores');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }else{           
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Fornecedor</font></b></center>');
        }
        $titulo = 'Listagem de Fornecedores ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Fornecedores');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Add(){
        self::Endereco_Fornecedor(true);
        // Carrega Config
        $titulo1    = 'Adicionar Fornecedor';
        $titulo2    = 'Salvar Fornecedor';
        $formid     = 'form_Sistema_Admin_Fornecedores';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Fornecedor/Fornecedores_Add2/';
        $campos = Comercio_Fornecedor_DAO::Get_Colunas();
        self::Campos_Deletar($campos);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Add2(){
        $titulo     = 'Fornecedor Adicionado com Sucesso';
        $dao        = 'Comercio_Fornecedor';
        $funcao     = '$this->Fornecedores();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Fornecedor cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
     
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Edit($id){
        self::Endereco_Fornecedor(true);
        // Carrega Config
        $titulo1    = 'Editar Fornecedor (#'.$id.')';
        $titulo2    = 'Alteração de Fornecedor';
        $formid     = 'form_Sistema_AdminC_FornecedorEdit';
        $formbt     = 'Alterar Fornecedor';
        $formlink   = 'comercio/Fornecedor/Fornecedores_Edit2/'.$id;
        $editar     = Array('Comercio_Fornecedor',$id);
        $campos = Comercio_Fornecedor_DAO::Get_Colunas();
        self::Campos_Deletar($campos);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);  
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Edit2($id){
        $titulo     = 'Fornecedor Editado com Sucesso';
        $dao        = Array('Comercio_Fornecedor',$id);
        $funcao     = '$this->Fornecedores();';
        $sucesso1   = 'Fornecedor Alterado com Sucesso.';
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
    public function Fornecedores_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa fornecedor e deleta
        $fornecedor = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($fornecedor);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Fornecedor Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Fornecedores();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Fornecedor deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    public function Fornecedores_View($fornecedor_id = false){
        if($fornecedor_id===false || $fornecedor_id==0 || !isset($fornecedor_id)) throw new \Exception('Fornecedor não informado',404);
        // Inclui Endereco
        self::Endereco_Fornecedor(true);
        $this->Tema_Endereco('Visualizar Comentários do Fornecedor #'.$fornecedor_id);
        // Chama Popup e COmentarios
        $this->Fornecedores_Popup(      $fornecedor_id, false);
        $this->Fornecedores_Comentario( $fornecedor_id);
        $this->_Visual->Json_Info_Update('Titulo','Visualizar Comentários do Fornecedor');
    }
    public function Fornecedores_Popup($fornecedor_id = false, $popup = true){
        if($fornecedor_id===false || $fornecedor_id==0 || !isset($fornecedor_id)) throw new \Exception('Fornecedor não informado',404);
        // mostra todas as suas mensagens
        $where = Array(
            'id'    =>  $fornecedor_id,
        );
        $fornecedor = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor',$where, 1);
        $html  = '<div class="span4">';
        $html .= '<b>Razão Social:</b> '.           $fornecedor->nome.'<br>'; 
        $html .= '<b>CNPJ:</b> '.                   $fornecedor->cnpj.'<br>'; 
        $html .= '<b>Cpf:</b> '.                    $fornecedor->cpf.'<br>'; 
        $html .= '<b>Banco:</b> '.                  $fornecedor->banco.'<br>'; 
        $html .= '<b>Agencia:</b> '.                $fornecedor->agencia.'<br>';
        $html .= '<b>Conta:</b> '.                  $fornecedor->conta.'<br>';
        $html .= '<b>Site:</b> '.                   $fornecedor->site.'<br>'; 
        $html .= '<b>Ie:</b> '.                     $fornecedor->ie.'<br>';  
        $html .= '</div><div class="span4">';  
        $html .= '<b>Email Principal:</b> '.        $fornecedor->email.'<br>';  
        $html .= '<b>Fax:</b> '.                    $fornecedor->fax.'<br>';  
        $html .= '<b>Cep:</b> '.                    $fornecedor->cep.'<br>';  
        $html .= '<b>País:</b> '.                   $fornecedor->pais2.'<br>';  
        $html .= '<b>Estado:</b> '.                 $fornecedor->estado2.'<br>';  
        $html .= '<b>Bairro:</b> '.                 $fornecedor->bairro2.'<br>';  
        $html .= '<b>Endereço:</b> '.               $fornecedor->endereco.'<br>';  
        $html .= '<b>Número:</b> '.                 $fornecedor->numero.'<br>';    
        $html .= '<b>Complemento:</b> '.            $fornecedor->complemento.'<br>';    
        $html .= '</div><div class="span4">';
        $html .= '<b>Telefone de Contato 1:</b> '.  $fornecedor->telefone1.'<br>'; 
        $html .= '<b>Email de Contato 1:</b> '.     $fornecedor->email1.'<br>';  
        $html .= '<b>Celular de Contato 1:</b> '.   $fornecedor->celular1.'<br>'; 
        $html .= '<b>Telefone de Contato 2:</b> '.  $fornecedor->telefone2.'<br>';  
        $html .= '<b>Email de Contato 2:</b> '.     $fornecedor->email2.'<br>';  
        $html .= '<b>Celular de Contato 2:</b> '.   $fornecedor->celular2.'<br>';  
        $html .= '<b>Observação:</b> '.             $fornecedor->obs;
        $html .= '</div>';            
        $titulo = 'Informações do Fornecedor '.$fornecedor->nome.' ('.$fornecedor_id.')';
        if($popup){
            $conteudo = array(
                'id' => 'popup',
                'title' => 'Visualizar Fornecedor',
                'botoes' => array(
                    array(
                        'text' => 'Fechar',
                        'clique' => '$( this ).dialog( "close" );'
                    )
                ),
                'html' => $html
            );
            $this->_Visual->Json_IncluiTipo('Popup',$conteudo);
        }else{
            $this->_Visual->Blocar('<div class="row-fluid">'.$html.'</div>');
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',20);
            $this->_Visual->Json_Info_Update('Titulo','Visualizar Fornecedor');
        }
    }
    /**
     * Comentarios dos Fornecedores
     */
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario($fornecedor_id = false, $export = false){
        global $language;
        $erro = false;
        if($fornecedor_id===false){
            $where = Array();
        }else{
            $where = Array('fornecedor'=>$fornecedor_id);
        }
        $i = 0;
        // BOTAO IMPRIMIR / ADD
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Comentário de Fornecedor',
                'comercio/Fornecedor/Fornecedores_Comentario_Add/'.$fornecedor_id,
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Fornecedor/Fornecedores_Comentario/'.$fornecedor_id,
            )
        )));
        // CONEXAO
        $linhas = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor_Comentario',$where);
        if($linhas!==false && !empty($linhas)){
            if(is_object($linhas)) $linhas = Array(0=>$linhas);
            reset($linhas);
            foreach ($linhas as $indice=>&$valor) {
                //$tabela['#Id'][$i]        = '#'.$valor->id;
                $tabela['Comentário'][$i]   =   $valor->comentario;
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário de Fornecedor'        ,'comercio/Fornecedor/Fornecedores_Comentario_Edit/'.$fornecedor_id.'/'.$valor->id.'/'    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário de Fornecedor'       ,'comercio/Fornecedor/Fornecedores_Comentario_Del/'.$fornecedor_id.'/'.$valor->id.'/'     ,'Deseja realmente deletar esse Comentário desse Fornecedor ?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Comercio - Fornecedor (#'.$fornecedor_id.') Comentários');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }else{   
            if($export!==false){
                $erro = 'Nenhum Comentário desse Fornecedor para Exportar';
            }else {
                $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Comentário do Fornecedor</font></b></center>');
            }
        }
        if($erro===false){
            $titulo = 'Comentários do Fornecedor ('.$i.')';
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);

            //Carrega Json
            $this->_Visual->Json_Info_Update('Titulo','Administrar Comentários do Fornecedor');
        }else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $erro
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario_Add($fornecedor_id = false){
        if($fornecedor_id===false) throw new \Exception('Fornecedor não informado',404);
        // Carrega Config
        $titulo1    = 'Adicionar Comentário do Fornecedor';
        $titulo2    = 'Salvar Comentário do Fornecedor';
        $formid     = 'form_Sistema_Admin_Fornecedores_Comentario';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Fornecedor/Fornecedores_Comentario_Add2/'.$fornecedor_id.'/';
        $campos = Comercio_Fornecedor_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario_Add2($fornecedor_id = false){
        if($fornecedor_id===false) throw new \Exception('Fornecedor não informado',404);
        $titulo     = 'Comentário do Fornecedor Adicionado com Sucesso';
        $dao        = 'Comercio_Fornecedor_Comentario';
        $funcao     = '$this->Fornecedores_View('.$fornecedor_id.');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Comentário do Fornecedor cadastrado com sucesso.';
        $alterar    = Array('fornecedor'=>$fornecedor_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario_Edit($fornecedor_id = false,$id = 0){
        if($fornecedor_id===false) throw new \Exception('Fornecedor não informado',404);
        if($id            == 0   ) throw new \Exception('Comentário não informado',404);
        // Carrega Config
        $titulo1    = 'Editar Comentário do Fornecedor (#'.$id.')';
        $titulo2    = 'Alteração de Comentário do Fornecedor';
        $formid     = 'form_Sistema_AdminC_FornecedorEdit';
        $formbt     = 'Alterar Comentário do Fornecedor';
        $formlink   = 'comercio/Fornecedor/Fornecedores_Comentario_Edit2/'.$fornecedor_id.'/'.$id;
        $editar     = Array('Comercio_Fornecedor_Comentario',$id);
        $campos = Comercio_Fornecedor_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario_Edit2($fornecedor_id = false,$id = 0){
        if($fornecedor_id===false) throw new \Exception('Fornecedor não informado',404);
        if($id            == 0   ) throw new \Exception('Comentário não informado',404);
        $titulo     = 'Comentário de Fornecedor Editado com Sucesso';
        $dao        = Array('Comercio_Fornecedor_Comentario',$id);
        $funcao     = '$this->Fornecedores_View('.$fornecedor_id.');';
        $sucesso1   = 'Comentário de Fornecedor Alterado com Sucesso.';
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array('fornecedor'=>$fornecedor_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Fornecedores_Comentario_Del($fornecedor_id = false,$id = 0){
        if($fornecedor_id===false) throw new \Exception('Fornecedor não informado',404);
        if($id            == 0   ) throw new \Exception('Comentário não informado',404);
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Comentário do Fornecedor Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Fornecedores_View($fornecedor_id);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Comentário de Fornecedor deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
