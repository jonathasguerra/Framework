<?php
class usuario_AdminControle extends usuario_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses usuarios_ListarModelo Carrega usuarios Modelo
    * @uses usuarios_ListarVisual Carrega usuarios Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        // construct
        parent::__construct();
    }
    /**
    * Função Main, Principal
    * 
    * @name Main
    * @access public
    * 
    * @uses usuarios_AdminControle::$usuarios_lista
    * @uses usuarios_AdminControle::$marcas_carregajanelaadd
    * @uses \Framework\App\Visual::$Json_Start
    * 
    * @return void 
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main($export=false){
        $this->usuariolistar(false,false,0,false,$export);
        if(\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_veiculo')){
            $this->usuarios_pendentes('cnh');
            $this->usuarios_pendentes('res');
        }
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo','Todos os Usuários');         
    }
    static function usuarios_carregaAlterarSenha($id=false,$tipo=false){
        if($id===false){
            $id = \Framework\App\Acl::Usuario_GetID_Static();
            $link = '';
        }else{
            $link = '/'.$id.'/'.$tipo;
        }
        // Carrega Config
        $titulo1    = 'Alterar Senha (#'.$id.')';
        $titulo2    = 'Alterar Senha';
        $formid     = 'form_perfil_senha';
        $formbt     = 'Alterar Senha';
        $formlink   = 'usuario/Admin/usuarios_carregaAlterarSenha2'.$link;
        $editar     = Array('Usuario',$id);
        $campos = Usuario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'senha',1);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar,'right',false); 
    }
    public function usuarios_carregaAlterarSenha2($id=false,$tipo=false){
        if($id===false) $id = (int) $this->_Acl->Usuario_GetID();
        $titulo     = 'Senha editada com Sucesso';
        $dao        = Array('Usuario',$id);
        if($tipo==='cliente' || $tipo==='Cliente'){
            $funcao     = '$this->ListarCliente();';
        }else if($tipo==='funcionario' || $tipo==='Funcionario' || $tipo==='Funcionário' || $tipo==='Funcionrio'){
            $funcao     = '$this->ListarFuncionarios();';
        }else{
            $funcao     = '$this->ListarOutros();';
        }
        $sucesso1   = 'Senha Alterada com Sucesso.';
        $sucesso2   = 'Guarde sua senha com carinho.';
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    public function ListarCliente($export=false){
        $this->usuariolistar(Array(CFG_TEC_CAT_ID_CLIENTES,\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome')),false,20,false,$export);
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo',\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome'));  
    }
    public function ListarFuncionarios($export=false){
        $this->usuariolistar(Array(CFG_TEC_CAT_ID_FUNCIONARIOS,\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome')),false,10,false,$export);
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo',\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome'));
    }
    public function ListarOutros($export=false){
        $this->usuariolistar(Array(CFG_TEC_CAT_ID_ADMIN,'Usuários'),false,10,false,$export);
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo','Usuários');  
    }
    public function Usuarios_Edit($id = 0,$tipo=false){
        if($id==0 || !isset($id)){
            $id = (int) $this->_Acl->Usuario_GetID();
        }else{
            $id = (int) $id;
        }
        // CARREGA USUARIO
        $usuario = $this->_Modelo->db->Sql_Select('Usuario', Array('id'=>$id),1);
        // Carrega campos e retira os que nao precisam
        $campos = Usuario_DAO::Get_Colunas();
        // Verifica TIPO
        if($tipo===false){
            $sql_grupo = $this->_Modelo->db->Sql_Select('Sistema_Grupo', Array('id'=>$usuario->grupo),1);
            if($sql_grupo===false){
                $usuario->grupo = CFG_TEC_IDFUNCIONARIO;
                $this->_Modelo->db->Sql_Update($usuario);
                $tipo   = 'Funcionário';
                $tipo2  = 'funcionario';
            }else if($sql_grupo->categoria==CFG_TEC_CAT_ID_CLIENTES){
                $tipo   = 'Cliente';
                $tipo2  = 'cliente';
            }else if($sql_grupo->categoria==CFG_TEC_CAT_ID_FUNCIONARIOS){
                $tipo   = 'Funcionário';
                $tipo2  = 'funcionario';
            }
        }else{
            // Primeira Letra Maiuscula
            $tipo = ucfirst($tipo);
        }
        
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo==='Funcionrio' || $tipo==="Funcionario") $tipo = "Funcionário";
        if($tipo==="Usurio" || $tipo==="Usuario")         $tipo = 'Usuário';
        // Cria Tipo 2:
        if($tipo==='Cliente'){
            $tipo_pass    = CFG_TEC_CAT_ID_CLIENTES;
            $tipo2  = 'cliente';
            // Troca grupo
            self::DAO_Ext_ADD($campos,'grupo','SG.categoria='.CFG_TEC_CAT_ID_CLIENTES);
             //Aparece na Tela
            $tipo   = Framework\Classes\Texto::Transformar_Plural_Singular(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome'));
            $this->Tema_Endereco('Clientes','usuario/Admin/ListarCliente');
            $metodo = 'Cliente_Edit2'.'/'.$id.'/';
        }else if($tipo==='Funcionário' || $tipo===\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome')){
            $tipo_pass  = CFG_TEC_CAT_ID_FUNCIONARIOS;
            $tipo2  = 'funcionario'; //id do tipo
            $tipo   = Framework\Classes\Texto::Transformar_Plural_Singular(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome'));
            // Troca grupo
            self::DAO_Ext_ADD($campos,'grupo','SG.categoria='.CFG_TEC_CAT_ID_FUNCIONARIOS);
            $this->Tema_Endereco('Funcionários','usuario/Admin/ListarFuncionarios');
            $metodo = 'Funcionario_Edit2'.'/'.$id.'/';
        }else{
            $tipo_pass  = CFG_TEC_CAT_ID_ADMIN;
            $tipo2  = 'usuario'; //id do tipo
            // Troca grupo
            self::DAO_Ext_ADD($campos,'grupo','SG.categoria='.CFG_TEC_CAT_ID_ADMIN);
            $this->Tema_Endereco('Usuários','usuario/Admin/ListarOutros');
            $metodo = 'Usuarios_Edit2'.'/'.$id.'/'.$tipo2.'/';
        }
        
        // Alterar Senha
        $encolher = false;
        $usuario_Login = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Login');
        if(is_array($usuario_Login)){
            if(in_array($tipo_pass, $usuario_Login)){
                // Carrega Alterar SEnha
                self::usuarios_carregaAlterarSenha($id,$tipo2);
                $encolher = true;
            }
        }else{
            if($usuario_Login===true){
                // Carrega Alterar SEnha
                self::usuarios_carregaAlterarSenha($id,$tipo2);
                $encolher = true;
            }
        }
        
        
        
        
        
        // Bota Endereço
        $this->Tema_Endereco('Editar');
        // cadastro de usuario
        $form = new \Framework\Classes\Form('form_Sistema_AdminC_UsuarioEdit','usuario/Admin/'.$metodo,'formajax');
        // Deleta Campos Responsaveis
        self::Campos_Deletar($tipo_pass,$campos,$usuario);
        // Retira sempre a senha
        self::DAO_Campos_Retira($campos, 'senha');
        if($usuario===false) throw new \Exception('Usuário não existe', 8010);
        
        // Atualiza Valores
        self::mysql_AtualizaValores($campos, $usuario);
        // Gera Formulário
        \Framework\App\Controle::Gerador_Formulario($campos, $form);
        $formulario = $form->retorna_form('Alterar');
        $this->_Visual->Blocar($formulario);
        if($tipo===false){
            if($encolher===true)$this->_Visual->Bloco_Maior_CriaJanela('Alteração de Usuário');
            else                $this->_Visual->Bloco_Unico_CriaJanela('Alteração de Usuário');
            $this->_Visual->Json_Info_Update('Titulo', 'Editar Usuário (#'.$id.')');   
        }else{
            if($encolher===true)$this->_Visual->Bloco_Maior_CriaJanela('Alteração de '.$tipo.'');
            else                $this->_Visual->Bloco_Unico_CriaJanela('Alteração de '.$tipo.'');
            
            $this->_Visual->Json_Info_Update('Titulo', 'Editar '.$tipo.' (#'.$id.')');   
        }
    }
    /**
     * 
     * @global Array $language
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Edit2($id,$tipo=false){
        global $language;
        
        
        $id = (int) $id;
        // Primeira Letra Maiuscula
        $tipo = ucfirst($tipo);
        // Puxa o usuario, e altera seus valores, depois salva novamente
        $usuario = $this->_Modelo->db->Sql_Select('Usuario', Array('id'=>$id));
        self::mysql_AtualizaValores($usuario);
        $sucesso =  $this->_Modelo->db->Sql_Update($usuario);
        
        
        if($sucesso===true){
        
            if($tipo==='cliente' || $tipo==='Cliente'){
                $this->ListarCliente();
            }else if($tipo==='funcionario' || $tipo==='Funcionario' || $tipo==='Funcionário' || $tipo==='Funcionrio'){
                $this->ListarFuncionarios();
            }else{
                $this->ListarOutros();
            }
            
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Alteração bem sucedida',
                "mgs_secundaria" => ''.$_POST["nome"].' foi alterado com sucesso.'
            );
            $this->_Visual->Json_Info_Update('Titulo',''.$_POST["nome"].' foi alterado com sucesso.');
        }else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
            $this->_Visual->Json_Info_Update('Titulo',$language['mens_erro']['erro']);
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);   
        $this->_Visual->Json_Info_Update('Historico', false); 
    }
    public function Status($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Usuario', Array('id'=>$id),1);
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Essa registro não existe:'. $raiz, 404);
        }
        if($resultado->ativado==1 || $resultado->ativado=='1'){
            $resultado->ativado='0';
        }else{
            $resultado->ativado='1';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            if($resultado->ativado==1 || $resultado->ativado=='1'){
                $texto = 'Em Andamento';
            }else{
                $texto = 'Finalizado';
            }
            $conteudo = array(
                'location' => '#status'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->ativado     ,Array($texto        ,'usuario/Admin/Status/'.$resultado->id.'/'    ,''))
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => 'Sucesso',
                "mgs_secundaria"    => 'Status Alterado com Sucesso.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Erro',
                "mgs_secundaria"    => 'Ocorreu um Erro.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
    * Cria Janela Do Formulario de Cadastro de usuarios
    * 
    * @name usuarios_carregajanelaadd
    * @access public
    * 
    * @param Class &$controle Classe Controle Atual passada por Ponteiro
    * @param Class &$modelo Modelo Passado por Ponteiro
    * @param Class &$Visual Visual Passado por Ponteiro
    * 
    * @uses usuario_social_Modelo Carrega Persona Modelo
    * @uses usuario_social_Modelo::$retorna_usuario_social Retorna Pessoas
    * @uses financeiroControle::$usuarios_formcadastro retorna Formulario de Cadastro de usuarios
    * @uses \Framework\App\Visual::$blocar Add html ao bloco de conteudo
    * @uses \Framework\App\Visual::$Bloco_Unico_CriaJanela Add html do bloco a uma Janela Lateral Direita
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Usuarios_Add($categoria = 0,$tipo=false){
        $usuario = new \Usuario_DAO();
        // Carrega campos e retira os que nao precisam
        $campos = Usuario_DAO::Get_Colunas();
        // Troca grupo
        if($categoria!==0){
            self::DAO_Ext_ADD($campos,'grupo','SG.categoria='.$categoria);
        }
        
        // Retira os de clientes
        $linkextra = '';
        if($tipo!==false){
            $linkextra = $tipo.'/';
        }
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo==='Funcionrio'){
            $tipo = "Funcionário";
        }
        
        if($tipo==='cliente'){
            self::Campos_Deletar(CFG_TEC_CAT_ID_CLIENTES,$campos, $usuario);
            $func_nome_plural = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome');
            $func_nome = Framework\Classes\Texto::Transformar_Plural_Singular($func_nome_plural);
            $form = new \Framework\Classes\Form('form_Sistema_Admin_Usuarios','usuario/Admin/Cliente_Add2/','formajax');
            \Framework\App\Controle::Gerador_Formulario($campos, $form);
            $formulario = $form->retorna_form('Cadastrar '.$func_nome);
            $this->_Visual->Blocar($formulario);
            $this->_Visual->Bloco_Unico_CriaJanela('Cadastro de '.$func_nome);
            $this->_Visual->Json_Info_Update('Titulo','Adicionar '.$func_nome);
            
            $this->Tema_Endereco($func_nome_plural,'usuario/Admin/ListarCliente');
        }else if($tipo==='funcionario'){
            self::Campos_Deletar(CFG_TEC_CAT_ID_FUNCIONARIOS,$campos, $usuario);
            $form = new \Framework\Classes\Form('form_Sistema_Admin_Usuarios','usuario/Admin/Funcionario_Add2','formajax');
            \Framework\App\Controle::Gerador_Formulario($campos, $form);
            $formulario = $form->retorna_form('Cadastrar Funcionário');
            $this->_Visual->Blocar($formulario);
            $this->_Visual->Bloco_Unico_CriaJanela('Cadastro de Funcionário');
            $this->_Visual->Json_Info_Update('Titulo','Adicionar Funcionário');
            $this->Tema_Endereco('Funcionários','usuario/Admin/ListarFuncionarios');
        }else{
            self::Campos_Deletar(CFG_TEC_CAT_ID_ADMIN,$campos, $usuario);
            // Carrega formulario
            $form = new \Framework\Classes\Form('form_Sistema_Admin_Usuarios','usuario/Admin/Usuarios_Add2/'.$linkextra,'formajax');
            \Framework\App\Controle::Gerador_Formulario($campos, $form);
            $formulario = $form->retorna_form('Cadastrar Usuário');
            $this->_Visual->Blocar($formulario);
            $this->_Visual->Bloco_Unico_CriaJanela('Cadastro de Usuário');
            $this->_Visual->Json_Info_Update('Titulo','Adicionar Usuário');
            $this->Tema_Endereco('Usuários','usuario/Admin/ListarOutros');
        }
        $this->Tema_Endereco('Adicionar');
    }
    /**
     * Inseri usuarios no Banco de dados
     * 
     * @name usuarios_inserir
     * @access public
     * 
     * @global Array $language
     * 
     * @post $_POST["categoria"]
     * @post int $_POST["ano"]
     * @post $_POST["modelo"]
     * @post int $_POST["marca"]
     * @post $_POST["cc"]
     * @post $_POST["valor1"]
     * @post $_POST["valor2"]
     * @post $_POST["valor3"]
     * @post $_POST["franquia"]
     * 
     * @uses usuarios_AdminControl::$usuarios_inserir
     * 
     * @return void
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Add2($tipo=false){
        global $language;
        $this->_Visual->Json_Info_Update('Titulo','Usuário.');
        
        if(isset($_POST['email'])){
            $email = \anti_injection($_POST['email']);
        }else{
            $email = '';
        }
        if(isset($_POST['login'])){
            $login = \anti_injection($_POST['login']);
        }else{
            $login = '';
        }
        $existeemail = usuario_Modelo::VerificaExtEmail($this->_Modelo,$email);
        $existelogin = usuario_Modelo::VerificaExtLogin($this->_Modelo,$login);
        if(\Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email($email)===false && $tipo!='cliente' && \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Admin_EmailUnico')){
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => 'Email Inválido'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
            $this->layoult_zerar = false;
            $this->_Visual->Javascript_Executar('$("#email").css(\'border\', \'2px solid #FFAEB0\').focus();');
         }else if($existeemail===true && ($tipo!=='cliente' || $email!='') && \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Admin_EmailUnico')){
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => 'Email Ja Existe'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
            $this->layoult_zerar = false; 
            $this->_Visual->Javascript_Executar('$("#email").css(\'border\', \'2px solid #FFAEB0\').focus();');
        }else if($existelogin===true && $tipo!=='cliente'){
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => 'Login ja existe'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
            $this->layoult_zerar = false;
            $this->_Visual->Javascript_Executar('$("#login").css(\'border\', \'2px solid #FFAEB0\').focus();');
        }else{
            $tipousuario = \anti_injection($tipo);

            // atualiza todos os valores por get, retirando o nivel admin
            //self::mysql_AtualizaValores($usuario);

            global $language;

            // Cria novo Usuario
            $usuario = new Usuario_DAO;
            self::mysql_AtualizaValores($usuario);
        
            // confere senha
            if(isset($_POST['senha'])){
                if($_POST['senha']==''){
                    $mensagens = array(
                        "tipo" => 'erro',
                        "mgs_principal" => $language['mens_erro']['erro'],
                        "mgs_secundaria" => 'Senha Inválida'
                    );
                    $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                    $this->layoult_zerar = false;
                    return;
                }
            }

            // captura indicado;
            if(!isset($_COOKIE['indicativo_id'])) $_COOKIE['indicativo_id'] = 0;
            self::mysql_AtualizaValor($usuario,'indicado_por', $_COOKIE['indicativo_id']);

            // Atualiza
            // Recarrega ListarOutros
            if($tipo===false AND !(\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_mensagem'))){
                $sucesso =  $this->_Modelo->db->Sql_Inserir($usuario);
                $executar = 'ListarOutros';
            }else if(\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_mensagem')){
                $sucesso =  $this->_Modelo->db->Sql_Inserir($usuario);
                if($sucesso){$identificador  = $this->_Modelo->db->Sql_Select('Usuario', Array(),1,'id DESC');
                $identificador  = $identificador->id;
                usuario_mensagem_Controle::Mensagem_formulario_Static($identificador);}
                $executar = false;
                //\Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'usuario_mensagem/Suporte/Mensagem_formulario');
            }else if($tipo==='cliente'){
                if(!isset($_GET['grupo'])){
                    $usuario->grupo = CFG_TEC_IDCLIENTE;
                }
                $sucesso =  $this->_Modelo->db->Sql_Inserir($usuario);
                $executar = 'ListarCliente';
            }else if($tipo==='funcionario'){
                if(!isset($_GET['grupo'])){
                    $usuario->grupo = CFG_TEC_IDFUNCIONARIO;
                }
                $sucesso =  $this->_Modelo->db->Sql_Inserir($usuario);
                $executar = 'ListarFuncionarios';
            }else{
                if(!isset($_GET['grupo'])){
                    $usuario->grupo = CFG_TEC_IDADMIN;
                }
                $sucesso =  $this->_Modelo->db->Sql_Inserir($usuario);
                $executar = 'ListarOutros';
            }
            // Caso seja Inserido mostra Mensagem
            if($sucesso===true){
                $mensagens = array(
                    "tipo" => 'sucesso',
                    "mgs_principal" => 'Inserção bem sucedida',
                    "mgs_secundaria" => 'Voce foi cadastrado com sucesso.'
                );
                // loga usuario
                
                if($tipo!='cliente' && !$this->_Acl->Usuario_GetLogado()){
                    $this->_Modelo->Usuario_Logar($login, \Framework\App\Sistema_Funcoes::Form_Senha_Blindar($_POST['senha']));  
                }
                $this->_Visual->Json_Info_Update('Titulo','Adicionado com Sucesso.');
                
                if($executar){
                    $this->$executar();
                }
            }else{
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => $language['mens_erro']['erro']
                );
            }
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
        }
        $this->_Visual->Json_Info_Update('Historico', false);
    }
    /**
    * Deleta usuario
    * 
    * @name usuarios_Del
    * @access public
    * 
    * @uses usuarios_AdminModelo::$usuarios_Del
    * @uses \Framework\App\Visual::$Json_IncluiTipo
    * @uses usuarios_AdminControle::$usuarios_lista
    * 
    * @return void 
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Usuarios_Del($id,$tipo=false){
        global $language;
    	$id = (int) $id;
        // Regula Tipo
        if($tipo==='falso'){
            $tipo  = false;
        }
        // Puxa usuario e deleta
        $usuario    = $this->_Modelo->db->Sql_Select(  'Usuario',            Array('id'=>$id));
        
        // caso usuario exista
        if($usuario!==false){
            if($tipo===false){
                if($usuario->grupo==CFG_TEC_IDCLIENTE){
                    $tipo = 'cliente';
                    $tipo2 = 'Cliente';
                }else if($usuario->grupo==CFG_TEC_IDFUNCIONARIO){
                    $tipo = 'funcionario';
                    $tipo2 = 'Funcionário';
                }
            }else{
                if($tipo==='cliente'){
                    $tipo2 = 'Cliente';
                }else if($tipo==='funcionario'){
                    $tipo2 = 'Funcionário';
                }
            }

            $sucesso    =  $this->_Modelo->db->Sql_Delete($usuario);
            if(\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_mensagem')){
                $mensagens  = $this->_Modelo->db->Sql_Select('Usuario_Mensagem',    Array('cliente'=>$id));
                $sucesso2   =  $this->_Modelo->db->Sql_Delete($mensagens);
            }
            if($sucesso===true){
                $mensagens = array(
                    "tipo" => 'sucesso',
                    "mgs_principal" => $tipo2.' Deletado',
                    "mgs_secundaria" => $tipo2.' Deletado com sucesso'
                );
            }else{
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => $language['mens_erro']['erro']
                );
            }
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }
        if($tipo==='cliente'){
            $this->ListarCliente();
        }else if($tipo==='funcionario'){
            $this->ListarFuncionarios();
        }else{
            $this->ListarOutros();
        }
    }
    
    
    public function Cliente_Add($categoria = 0){
        self::Usuarios_Add($categoria,'cliente');
    }
    public function Cliente_Add2(){
        self::Usuarios_Add2('cliente');
    }
    public function Cliente_Edit($id = 0){
        self::Usuarios_Edit($id,'cliente');
    }
    public function Cliente_Edit2($id = 0){
        self::Usuarios_Edit2($id,'cliente');
    }
    public function Cliente_Del($id){
        self::usuarios_Del($id,'cliente');
    }
    public function Funcionario_Add($categoria = 0){
        self::Usuarios_Add($categoria,'funcionario');
    }
    public function Funcionario_Add2(){
        self::Usuarios_Add2('funcionario');
    }
    public function Funcionario_Edit($id = 0){
        self::Usuarios_Edit($id,'funcionario');
    }
    public function Funcionario_Edit2($id = 0){
        self::Usuarios_Edit2($id,'funcionario');
    }
    public function Funcionario_Del($id){
        self::usuarios_Del($id,'funcionario');
    }
    
    public function usuarios_pendentes($tipo='cnh'){
        $usuarios = Array();
        if($tipo=='cnh') $this->_Modelo->usuario_cnh_pendente($usuarios);
        else             $this->_Modelo->usuario_res_pendente($usuarios);
        if(!empty($usuarios)){
            reset($usuarios);
            $i = 0;
            foreach ($usuarios as $indice=>&$valor) {
                if($usuarios[$indice]['nivel_usuario']==1)     $niveluser = CONFIG_CLI_1_NOME;
                elseif($usuarios[$indice]['nivel_usuario']==2) $niveluser = CONFIG_CLI_2_NOME;
                elseif($usuarios[$indice]['nivel_usuario']==3) $niveluser = CONFIG_CLI_3_NOME;
                elseif($usuarios[$indice]['nivel_usuario']==4) $niveluser = CONFIG_CLI_4_NOME;
                elseif($usuarios[$indice]['nivel_usuario']==5) $niveluser = CONFIG_CLI_5_NOME;
                else                                           $niveluser = CONFIG_CLI_0_NOME;
                
                if($usuarios[$indice]['nivel_admin']==0)     $niveladmin = 'Usuario';
                elseif($usuarios[$indice]['nivel_admin']==1) $niveladmin = 'Admin';
                else                                         $niveladmin = 'Admin GOD';
                
                
                $tabela['Id'][$i] = $usuarios[$indice]['id'];
                $tabela['Nome'][$i] = $usuarios[$indice]['nome'];
                $tabela['Email'][$i] = $usuarios[$indice]['email'];
                $tabela['Nivel de Usuário'][$i] = $niveluser;
                $tabela['Nivel de Admin'][$i] = $niveladmin;
                $tabela['Saldo'][$i] = $usuarios[$indice]['saldo'];
                $tabela['Funções'][$i] = '<a confirma="Deseja Realmente Aprovar?" title="Aprovar" class="lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario/Admin/usuarios_pendente_aprovar/'.$usuarios[$indice]['id'].'/'.$tipo.'/sim/"><img border="0" src="'.WEB_URL.'img/icons/status1.png"></a>'.
                '<a confirma="Deseja Realmente Desaprovar?" title="Desaprovar" class="lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario/Admin/usuarios_pendente_aprovar/'.$usuarios[$indice]['id'].'/'.$tipo.'/nao/"><img border="0" src="'.WEB_URL.'img/icons/status2.png"></a>'.
                '<a title="Editar Usuário" class="lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario/Admin/Usuarios_Edit/'.$usuarios[$indice]['id'].'/"><img border="0" src="'.WEB_URL.'img/icons/icon_edit.png"></a> '.
                '<a confirma="Deseja realmente deletar esse usuário?" title="Deletar Usuário" class="lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario/Admin/usuarios_Del/'.$usuarios[$indice]['id'].'/"><img border="0" src="'.WEB_URL.'img/icons/icon_bad.png"></a>';
                ++$i;
            }
            $this->_Visual->Show_Tabela_DataTable($tabela);
            if($tipo=='cnh') $titulo = 'CNH Pendentes ('.$i.')';
            else             $titulo = 'Comprovantes de Residencia Pendentes ('.$i.')';
            $this->_Visual->Bloco_Maior_CriaJanela($titulo);
            unset($tabela);
        }
    }
    public function usuarios_pendente_aprovar($id, $tipo='cnh',$aprovar='sim'){
        GLOBAL $language;
        
        $id = (int) $id;
        
        if($tipo=='cnh') $sucesso = $this->_Modelo->usuario_cnh_aprovar($id,$aprovar);
        else             $sucesso = $this->_Modelo->usuario_res_aprovar($id,$aprovar);
        
        $this->Main();
        
        if($sucesso===true){
            if($aprovar=='sim'){
                $mensagens = array(
                    "tipo" => 'sucesso',
                    "mgs_principal" => 'Aprovado com sucesso',
                    "mgs_secundaria" => '#'.$id.' foi modificado.'
                );
            }else {
                $mensagens = array(
                    "tipo" => 'sucesso',
                    "mgs_principal" => 'Negado com sucesso',
                    "mgs_secundaria" => '#'.$id.' foi modificado.'
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
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario($usuario_id = false,$tipo='usuario'){
        if($usuario_id===false){
            $where = Array();
        }else{
            $where = Array('usuario'=>$usuario_id);
        }
        
        
        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo=='Funcionrio' || $tipo=='Funcionário' || $tipo=='funcionrio' || $tipo=='funcionário' || $tipo=='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo=='Usuário' || $tipo=='usuário' || $tipo=='Usurio' || $tipo=='usurio'){
            $tipo = "usuario";
        }
        if($tipo=='Cliente'){
            $tipo = "cliente";
        }
        if($tipo=='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
            $this->Tema_Endereco('Clientes','usuario/Admin/ListarCliente');
        }else if($tipo=='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
            $this->Tema_Endereco('Funcionários','usuario/Admin/ListarFuncionarios');
        }else{
            $nomedisplay        = 'Usuários ';
            $nomedisplay_sing   = 'Usuário ';
            $tipo               = 'usuario';
            $this->Tema_Endereco('Usuários','usuario/Admin/ListarOutros');
        }
        $this->Tema_Endereco('Comentários');
        
        
        $i = 0;
        $this->_Visual->Blocar('<a title="Adicionar Comentário ao '.$nomedisplay_sing.'" class="btn btn-success lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario/Admin/Usuarios_Comentario_Add/'.$usuario_id.'/'.$tipo.'">Adicionar novo Comentário nesse '.$nomedisplay_sing.'</a><div class="space15"></div>');
        $comentario = $this->_Modelo->db->Sql_Select('Usuario_Comentario',$where);
        if($comentario!==false && !empty($comentario)){
            if(is_object($comentario)) $comentario = Array(0=>$comentario);
            reset($comentario);
            foreach ($comentario as $indice=>&$valor) {
                $tabela['#Id'][$i]          =   '#'.$valor->id;
                $tabela['Comentário'][$i]   =   nl2br($valor->comentario);
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário de Usuario'        ,'usuario/Admin/Usuarios_Comentario_Edit/'.$usuario_id.'/'.$valor->id.'/'.$tipo    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário de Usuario'       ,'usuario/Admin/Usuarios_Comentario_Del/'.$usuario_id.'/'.$valor->id.'/'.$tipo     ,'Deseja realmente deletar esse Comentário desse '.$nomedisplay_sing.' ?'));
                ++$i;
            }
            $this->_Visual->Show_Tabela_DataTable($tabela,'', true, false, Array(Array(0,'desc')));
            unset($tabela);
        }else{          
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Comentário do '.$nomedisplay_sing.'</font></b></center>');
        }
        $titulo = 'Comentários do '.$nomedisplay_sing.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Comentários do '.$nomedisplay_sing.'');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario_Add($usuario_id = false,$tipo='usuario'){
        // Começo
        $usuario_id = (int) $usuario_id;
        
        // Proteção E chama Endereço
        if($usuario_id===false) throw new \Exception('Usuario não informado',404);
        $usuario = $this->_Modelo->db->Sql_Select('Usuario',Array('id'=>$usuario_id), 1);
        if($usuario===false) throw new \Exception('Usuario não existe:'.$usuario_id,404);
        
        
        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo=='Funcionrio' || $tipo=='Funcionário' || $tipo=='funcionrio' || $tipo=='funcionário' || $tipo=='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo=='Usuário' || $tipo=='usuário' || $tipo=='Usurio' || $tipo=='usurio'){
            $tipo = "usuario";
        }
        if($tipo=='Cliente'){
            $tipo = "cliente";
        }
        if($tipo=='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
            $this->Tema_Endereco('Clientes','usuario/Admin/ListarCliente');
        }else if($tipo=='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
            $this->Tema_Endereco('Funcionários','usuario/Admin/ListarFuncionarios');
        }else{
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'usuario';
            $this->Tema_Endereco('Usuários','usuario/Admin/ListarOutros');
        }
        $this->Tema_Endereco('Comentários', 'usuario/Admin/Usuarios_Comentario/'.$usuario_id.'/'.$tipo);
        
        
        
        // Carrega Config
        $titulo1    = 'Adicionar Comentário de '.$nomedisplay_sing;
        $titulo2    = 'Salvar Comentário de '.$nomedisplay_sing;
        $formid     = 'form_Sistema_Admin_'.$tipo.'_Comentario_'.$usuario_id;
        $formbt     = 'Salvar '.$nomedisplay_sing;
        $formlink   = 'usuario/Admin/Usuarios_Comentario_Add2/'.$usuario_id.'/'.$tipo;
        $campos = Usuario_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'usuario');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario_Add2($usuario_id = false,$tipo='usuario'){
        if($usuario_id===false) throw new \Exception('Usuario não informado',404);
        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo=='Funcionrio' || $tipo=='Funcionário' || $tipo=='funcionrio' || $tipo=='funcionário' || $tipo=='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo=='Usuário' || $tipo=='usuário' || $tipo=='Usurio' || $tipo=='usurio'){
            $tipo = "usuario";
        }
        if($tipo=='Cliente'){
            $tipo = "cliente";
        }
        if($tipo=='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
        }else if($tipo=='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
        }else{
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'usuario';
        }
        
        $titulo     = 'Comentário do '.$nomedisplay_sing.' Adicionado com Sucesso';
        $dao        = 'Usuario_Comentario';
        $funcao     = '$this->Usuarios_Comentario('.$usuario_id.',\''.$tipo.'\');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Comentário de '.$nomedisplay_sing.' Cadastrado com sucesso.';
        $alterar    = Array('usuario'=>$usuario_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario_Edit($usuario_id = false,$id = 0,$tipo='usuario'){
        if($usuario_id===false) throw new \Exception('Usuario não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        // Proteção E chama Endereço
        $usuario = $this->_Modelo->db->Sql_Select('Usuario',Array('id'=>$usuario_id), 1);
        if($usuario===false) throw new \Exception('Usuario não existe:'.$usuario_id,404);

        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo==='Funcionrio' || $tipo==='Funcionário' || $tipo==='funcionrio' || $tipo==='funcionário' || $tipo==='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo==='Usuário' || $tipo==='usuário' || $tipo==='Usurio' || $tipo==='usurio'){
            $tipo = "usuario";
        }
        if($tipo==='Cliente'){
            $tipo = "cliente";
        }
        if($tipo==='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
            $this->Tema_Endereco('Clientes','usuario/Admin/ListarCliente');
        }else if($tipo==='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
            $this->Tema_Endereco('Funcionários','usuario/Admin/ListarFuncionarios');
        }else{
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'usuario';
            $this->Tema_Endereco('Usuários','usuario/Admin/ListarOutros');
        }
        $this->Tema_Endereco('Comentários', 'usuario/Admin/Usuarios_Comentario/'.$usuario_id.'/'.$tipo);
        
        // Começo
        // Carrega Config
        $titulo1    = 'Editar Comentário do Usuario (#'.$id.')';
        $titulo2    = 'Alteração de Comentário do Usuario';
        $formid     = 'form_Sistema_AdminC_'.$nomedisplay_sing.'Edit_Comentario_'.$id;
        $formbt     = 'Alterar Comentário de '.$nomedisplay_sing;
        $formlink   = 'usuario/Admin/Usuarios_Comentario_Edit2/'.$usuario_id.'/'.$id.'/'.$tipo;
        $editar     = Array('Usuario_Comentario',$id);
        $campos = Usuario_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'usuario');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario_Edit2($usuario_id = false,$id = 0,$tipo='usuario'){
        if($usuario_id===false) throw new \Exception('Usuario não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        
        
        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo==='Funcionrio' || $tipo==='Funcionário' || $tipo==='funcionrio' || $tipo==='funcionário' || $tipo==='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo==='Usuário' || $tipo==='usuário' || $tipo==='Usurio' || $tipo==='usurio'){
            $tipo = "usuario";
        }
        if($tipo==='Cliente'){
            $tipo = "cliente";
        }
        if($tipo==='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
        }else if($tipo==='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
        }else{
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'usuario';
        }
        // Puxa Formulario Padrao do Sistema
        $titulo     = 'Comentário de '.$nomedisplay_sing.' Editado com Sucesso';
        $dao        = Array('Usuario_Comentario',$id);
        $funcao     = '$this->Usuarios_Comentario('.$usuario_id.',\''.$tipo.'\');';
        $sucesso1   = 'Comentário de '.$nomedisplay_sing.' Alterado com Sucesso.';
        $sucesso2   = 'Comentário teve a alteração bem sucedida';
        $alterar    = Array('usuario'=>$usuario_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Usuarios_Comentario_Del($usuario_id = false,$id = 0,$tipo='usuario'){
    	$id = (int) $id;
        if($usuario_id===false) throw new \Exception('Usuario não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        global $language;
        
        /**
         * Endereço, Diferenciacao etc..
         */
        // GAmbiarra Para Consertar erro de acento em url
        if($tipo==='Funcionrio' || $tipo==='Funcionário' || $tipo==='funcionrio' || $tipo==='funcionário' || $tipo==='Funcionario'){
            $tipo = "funcionario";
        }
        if($tipo==='Usuário' || $tipo==='usuário' || $tipo==='Usurio' || $tipo==='usurio'){
            $tipo = "usuario";
        }
        if($tipo==='Cliente'){
            $tipo = "cliente";
        }
        if($tipo==='cliente'){
            $nomedisplay        = 'Clientes';
            $nomedisplay_sing   = 'Cliente';
            $tipo               = 'cliente';
        }else if($tipo==='funcionario'){
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'funcionario';
        }else{
            $nomedisplay        = 'Usuários';
            $nomedisplay_sing   = 'Usuário';
            $tipo               = 'usuario';
        }
        
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Usuario_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Comentário do '.$nomedisplay_sing.' Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Usuarios_Comentario($usuario_id,$tipo);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Comentário de '.$nomedisplay_sing.' deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
