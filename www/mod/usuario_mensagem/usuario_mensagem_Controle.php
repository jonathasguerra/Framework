<?php
class usuario_mensagem_Controle extends \Framework\App\Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    *
    * @uses \Framework\App\Visual::$menu
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    public function Mensagens_Mostrar($tipodemensagem = false){
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo',$this->Mensagenslistar(0, $tipodemensagem));
    } 
   /**
     * lISTA OS SUPORTES
     * @param type $admin
     */
    protected function Mensagenslistar($admin = 0, $tipodemensagem = false){
        $this->_Visual->Blocar('<a title="Adicionar Mensagem de Suporte" class="btn btn-success lajax explicar-titulo" acao="" href="'.URL_PATH.'usuario_mensagem/Suporte/Mensagem_formulario">Adicionar novo Suporte</a><div class="space15"></div>');
        $i = 0;
        $mensagens = Array();
        if($admin==0 || $admin=='0'){
            $proprietario = $this->_Acl->Usuario_GetID();
        }else{
            $proprietario = 0;
        }
        $this->_Modelo->Mensagens_Retorna($mensagens,$proprietario,1,$tipodemensagem);
        if(is_object($mensagens)) $mensagens = Array(0=>$mensagens);
        if(!empty($mensagens) && $mensagens!==false){
            $i = usuario_mensagem_Controle::Mensagens_TabelaMostrar($this->_Visual, $mensagens,$admin);
        }else{
            if($admin==0){
                if($tipodemensagem===false){
                    $texto_vazio = 'Você não possui nenhum chamado.';
                }else if($tipodemensagem=='nov'){
                    $texto_vazio = 'Você não possui chamados novos.';
                }else if($tipodemensagem=='fin'){
                    $texto_vazio = 'Você não possui nenhum chamados finalizados.';
                }else if($tipodemensagem=='lim'){
                    $texto_vazio = 'Você não possui nenhum chamados em tempo limite.';
                }else if($tipodemensagem=='esg'){
                    $texto_vazio = 'Você não possui nenhum chamados Esgotado.';
                }
            }else{
                if($tipodemensagem===false){
                    $texto_vazio = 'O sistema não possui nenhum chamado.';
                }else if($tipodemensagem=='nov'){
                    $texto_vazio = 'O sistema não possui nenhum chamados novos.';
                }else if($tipodemensagem=='fin'){
                    $texto_vazio = 'O sistema não possui nenhum chamados finalizados.';
                }else if($tipodemensagem=='lim'){
                    $texto_vazio = 'O sistema não possui nenhum chamados em tempo limite.';
                }else if($tipodemensagem=='esg'){
                    $texto_vazio = 'O sistema não possui nenhum chamados Esgotado.';
                }
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$texto_vazio.'</font></b></center>');
        }
        
        // Titulos
        if($admin==0){
            if($tipodemensagem===false){
                $titulo  = 'Todos os seus Chamados';
            }else if($tipodemensagem=='nov'){
                $titulo  = 'Todos seus novos Chamados';
            }else if($tipodemensagem=='fin'){
                $titulo  = 'Todos seus Chamados Finalizados';
            }else if($tipodemensagem=='lim'){
                $titulo  = 'Todos seus Chamados em tempo limite';
            }else if($tipodemensagem=='esg'){
                $titulo  = 'Todos seus Chamados em tempo Esgotado';
            }
        }else{
            if($tipodemensagem===false){
                $titulo  = 'Todos os Chamados do Sistema';
            }else if($tipodemensagem=='nov'){
                $titulo  = 'Todos novos Chamados do Sistema';
            }else if($tipodemensagem=='fin'){
                $titulo  = 'Todos Chamados Finalizados do Sistema';
            }else if($tipodemensagem=='lim'){
                $titulo  = 'Todos Chamados em Tempo lLimite do Sistema';
            }else if($tipodemensagem=='esg'){
                $titulo  = 'Todos Chamados em Tempo Esgotado do Sistema';
            }
        }
        $titulo  = $titulo.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        return $titulo;
    }
    static function Mensagenslistar_naolidas(&$modelo,&$Visual,$usuarioid,$admin = 0){
        $i = 0;
        $mensagens = Array();
        if($admin==0) $usuario = $usuarioid;
        else          $usuario = 0;
        usuario_mensagem_Modelo::Mensagens_Retornanaolidas($modelo,$mensagens,$usuario,1);
        if(!empty($mensagens)){
            $i = usuario_mensagem_Controle::Mensagens_TabelaMostrar($Visual, $mensagens,$admin);
            $Visual->Bloco_Unico_CriaJanela('Chamados não lidos ('.$i.')','',100);
        }
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo','Chamados não lidos');  
    }
    static function Mensagens_TabelaMostrar(&$Visual,&$mensagens,$admin=0){
        $label = function($nometipo){
            if($nometipo=='Chamado Novo')       $tipo = 'success';
            else if($nometipo=='Esgotado')      $tipo = 'important';
            else if($nometipo=='Finalizado')    $tipo = 'inverse';
            else                                $tipo = 'warning';
            return '<span class="badge badge-'.$tipo.'">'.$nometipo.'</span>';
        };
        reset($mensagens);
        $i = 0;
        foreach ($mensagens as &$valor) {
            if($valor->lido===false){
                $valor->assunto2                = '<b>'.$valor->assunto2.'</b>';
            }
            $tabela['Protocolo'][$i]            = '#'.$valor->id;
            $tabela['Cliente'][$i]              = $valor->cliente2;
            if($admin==1) $tabela['De'][$i]     = $valor->escritor_nome;
            $tabela['Assunto'][$i]              = $valor->assunto2;
            $tabela['Tipo'][$i]                 = $label($valor->tipo);
            if($valor->datapassada==1){
                $tabela['Últ. Alteração'][$i]   = $valor->datapassada.' hora atrás';
            }else{
                $tabela['Últ. Alteração'][$i]   = $valor->datapassada.' horas atrás';
            }
            
            $tabela['Data de Criação'][$i]      = $valor->log_date_add; //date_replace($valor->log_date_add, "d/m/y | H:i");
            $tabela['Ultima Modificação'][$i]   = $valor->log_date_edit; //date_replace($valor->log_date_edit, "d/m/y | H:i");
            if($valor->tipo!='Finalizado'){
                $tabela['Visualizar Mensagem'][$i]  = $Visual->Tema_Elementos_Btn('Personalizado' ,    Array('Finalizar Mensagem'         ,'usuario_mensagem/Suporte/Finalizar/'.$valor->id.'/'    ,'','download','inverse'));
            }
            $tabela['Visualizar Mensagem'][$i]  .=$Visual->Tema_Elementos_Btn('Visualizar' ,    Array('Visualizar Mensagem'         ,'usuario_mensagem/Suporte/VisualizadordeMensagem/'.$valor->id.'/'    ,'')).
                                                  $Visual->Tema_Elementos_Btn('Editar'     ,    Array('Editar Mensagem'             ,'usuario_mensagem/Admin/Mensagem_Editar/'.$valor->id.'/'    ,'')).
                                                  $Visual->Tema_Elementos_Btn('Deletar'    ,    Array('Deletar Mensagem'            ,'usuario_mensagem/Admin/Mensagem_Del/'.$valor->id.'/'     ,'Deseja realmente deletar essa Mensagem ?'));
            ++$i;
        }
        $Visual->Show_Tabela_DataTable($tabela,'',true,false,Array(Array(1,'asc')));
        unset($tabela);
        return $i;
    }
    public function MensagemExibir($mensagem){
        $id = (int) $mensagem;
        $mensagens = Array();
        $tabela = Array();
            $i = 0;
        $amensagem = $this->_Modelo->Mensagem_Retorna($mensagens, $id, 1);
        usuario_mensagem_SuporteControle::Endereco_Suporte_Listar(false,$id);
        $titulo = $amensagem->assunto2;
        if($titulo=='' || $titulo==NULL) $titulo = 'Conversa sem Assunto';
        // Se tiver config maluco da skafe mostra
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_mensagem_Obs')){
            $where = Array('id'=>$amensagem->cliente);
            $usuario = $this->_Modelo->db->Sql_Select('Usuario',$where,1);
            $tabela['Mensagem'][$i] = '<b>'.$amensagem->cliente2.':</b> '.$usuario->obs;
            $tabela['Data'][$i] = $usuario->log_date_add;
            ++$i;
        }
        // Continua
        if($mensagens->is_empty==NULL){
            $mensagens->rewind();
            while ($mensagens->valid){
                $tabela['Mensagem'][$i] = '<b>'.$mensagens->current->escritor_nome.':</b> '.$mensagens->current->resposta;
                $tabela['Data'][$i] = $mensagens->current->log_date_add;
                ++$i;
                $mensagens->next();
            }
            $info = '<b>Setor:</b> '.$amensagem->setor2.
            '<br><b>Assunto:</b> '.$titulo;
            if((\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio') && file_exists(MOD_PATH.'comercio'.DS.'comercio_Controle.php'))){
                $info .= '<br><b>Cliente:</b> '.$amensagem->cliente2.
                '<br><b>Origem:</b> '.$amensagem->origem2.
                '<br><b>Marca:</b> '.$amensagem->marca2.
                '<br><b>Linha:</b> '.$amensagem->linha2.
                '<br><b>Produto:</b> '.$amensagem->produto2.
                '<br><b>Numero do Protocolo:</b> '.$amensagem->id.
                '<br><b>Lote:</b> '.$amensagem->lote.
                '<br><b>Validade:</b> '.$amensagem->validade.
                '<br><b>Fabricacao:</b> '.$amensagem->fabricacao;
            }
            $this->_Visual->Show_Tabela_DataTable($tabela,'',true,false,Array(Array(1,'asc')));
            $this->_Visual->Blocar('<h3>Informações Adicionais:</h3>'.$info);
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',60);
            unset($tabela);
        }
    }
    static function Mensagem_formulario_Static($cliente=0){
        GLOBAL $language;
        $registro = &\Framework\App\Registro::getInstacia();
        $Visual = $registro->_Visual;
        // Carrega campos e retira os que nao precisam
        $campos = usuario_mensagem_DAO::Get_Colunas();
        self::Campos_deletar($campos);
        if($cliente!=0) self::mysql_AtualizaValor($campos, 'cliente',$cliente);
        // Carrega formulario
        $form = new \Framework\Classes\Form('form_Usuario_Mensagem_Suporte','usuario_mensagem/Suporte/Mensagem_inserir/','formajax');
        \Framework\App\Controle::Gerador_Formulario($campos, $form);
        $formulario = $form->retorna_form('Enviar');
        $Visual->Blocar($formulario);
        // Mostra Conteudo
        $Visual->Bloco_Unico_CriaJanela('Cadastro de Ticket');
        // Pagina Config
        $Visual->Json_Info_Update('Titulo','Enviar Ticket');
    }
    public function Mensagem_formulario($cliente=0){
        usuario_mensagem_SuporteControle::Endereco_Suporte(true);
        $this->Tema_Endereco('Abrir Chamado');
        
        self::Mensagem_formulario_Static($cliente);
    }
    public function Resposta_formulario($mensagemid){
        global $language;
        $mensagemid = (int)$mensagemid;
        
        $form = new \Framework\Classes\Form('mensagem_resposta',SISTEMA_MODULO.'/'.SISTEMA_SUB.'/Resposta_inserir/'.$mensagemid.'/','formajax','full');
        // CADASTRA
        $form->Input_Novo('Mensagem','mensagem',$mensagemid,'hidden', false, 'obrigatorio');
        $form->TextArea_Novo('Responder','resposta','','','text', 10000, 'obrigatorio');
        $this->_Visual->Blocar($form->retorna_form($language['formularios']['enviar']));
        $this->_Visual->Bloco_Unico_CriaJanela('Responder Ticket');
    }
    /**
     * Formulario Suporter - Inserir Mensagem de FOrmulario
     * @global type $language
     */
    public function Mensagem_inserir(){
        global $language;
        $paranome = \anti_injection($_POST["paranome"]);
        $paraid = (int) $_POST["paraid"];
        $assunto = \anti_injection($_POST["assunto"]);
        $mensagem = \anti_injection($_POST["mensagem"]);
        // Grava E Recupera
        $sucesso    =  $this->_Modelo->Mensagem_inserir($paraid,$paranome,$assunto,$mensagem);
        $identificador  = $this->_Modelo->db->Sql_Select('Usuario_Mensagem', Array(),1,'id DESC');
        // REcria Mensagem
        $mensagem = '<b>Cliente: </b>'.     $identificador->cliente2.
                    '<br><b>Setor: </b>'.       $identificador->setor2.
                    '<br><b>Assunto: </b>'.     $identificador->assunto2.
                    '<br><b>Origem: </b>'.      $identificador->origem2.
                    '<br><b>Marca: </b>'.       $identificador->marca2.
                    '<br><b>Linha: </b>'.       $identificador->linha2.
                    '<br><b>Produto: </b>'.     $identificador->produto2.
                    '<br><b>Lote: </b>'.        $identificador->lote.
                    '<br><b>Validade: </b>'.    $identificador->validade.
                    '<br><b>Fabricação: </b>'.  $identificador->fabricacao.
                    '<br><b>Mensagem: </b>' . nl2br($mensagem);
        // Recupera Email e Dados do Setor
        $assunto    = $this->_Modelo->db->Sql_Select('Usuario_Mensagem_Assunto',Array('id'=>$assunto));
        $setor      = $this->_Modelo->db->Sql_Select('Usuario_Mensagem_Setor',Array('id'=>$assunto->setor));
        // Enviar Email
        $mailer = new \Framework\Classes\Email();
        $enviar = '';
        $emaildosetor = $setor->email;
        if(strpos($emaildosetor, ',')===false){
            $enviar .= '->setTo(\''.$emaildosetor.'\', \''.$setor->nome.'\')';
        }else{
            $emaildosetor = explode(',', $emaildosetor);
            foreach($emaildosetor as &$valor){
                $enviar .= '->setTo(\''.$valor.'\', \''.$setor->nome.'\')';
            }
        }
        eval('$send	= $mailer'.$enviar.'->setSubject(\'NOVA MENSAGEM SAQUE - '.SISTEMA_NOME.'\')'.
        '->setFrom(SISTEMA_EMAIL, SISTEMA_NOME)'.
        '->addGenericHeader(\'X-Mailer\', \'PHP/\' . phpversion())'.
        '->addGenericHeader(\'Content-Type\', \'text/html; charset="utf-8"\')'.
        '->setMessage(\'<strong><b>Mensagem:</b> \'.$mensagem.\'</strong>\')'.
        '->setWrap(78)->send();');
        if($sucesso===true && $send){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Mensagem inserida com Sucesso',
                "mgs_secundaria" => $mensagem
            );
        }else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        //atualiza
        $this->VisualizadordeMensagem($identificador->id);
    }
    public function Resposta_inserir(){
        global $language;
        $mensagem = (int) $_POST["mensagem"];
        $resposta = \anti_injection($_POST['resposta']);
        if(!is_int($mensagem) || $mensagem==0) return false;
        $resposta = \anti_injection($_POST["resposta"]);
        $sucesso =  $this->_Modelo->Mensagem_Resp_Inserir($mensagem,$resposta);
        $amensagem =  $this->_Modelo->db->Sql_Select('usuario_Mensagem',Array('id'=>$mensagem));
        // Recupera Email e Dados do Setor
        $assunto    = $this->_Modelo->db->Sql_Select('Usuario_Mensagem_Assunto',Array('id'=>$amensagem->assunto));
        $setor      = $this->_Modelo->db->Sql_Select('Usuario_Mensagem_Setor',Array('id'=>$assunto->setor));
        
        // REcria Mensagem
        $resposta = '<b>Cliente: </b>'.     $amensagem->cliente2.
                    '<br><b>Setor: </b>'.       $amensagem->setor2.
                    '<br><b>Assunto: </b>'.     $amensagem->assunto2.
                    '<br><b>Origem: </b>'.      $amensagem->origem2.
                    '<br><b>Marca: </b>'.       $amensagem->marca2.
                    '<br><b>Linha: </b>'.       $amensagem->linha2.
                    '<br><b>Produto: </b>'.     $amensagem->produto2.
                    '<br><b>Lote: </b>'.        $amensagem->lote.
                    '<br><b>Validade: </b>'.    $amensagem->validade.
                    '<br><b>Fabricação: </b>'.  $amensagem->fabricacao.
                    '<br><b>Mensagem: </b>'.    $amensagem->mensagem.
                    '<br><b>Resposta: </b>'.    $resposta;
        // Envia Email
        $mailer = new \Framework\Classes\Email();
        $enviar = '';
        $emaildosetor = $setor->email;
        if(strpos($emaildosetor, ',')===false){
            $enviar = '->setTo(\''.$emaildosetor.'\', \''.$setor->nome.'\')';
        }else{
            $emaildosetor = explode(',', $emaildosetor);
            foreach($emaildosetor as &$valor){
                $enviar .= '->setTo(\''.$valor.'\', \''.$setor->nome.'\')';
            }
        }
        eval('$send	= $mailer'.$enviar.'->setSubject(\'NOVA RESPOSTA SAQUE - '.SISTEMA_NOME.'\')'.
        '->setFrom(SISTEMA_EMAIL, SISTEMA_NOME)'.
        '->addGenericHeader(\'X-Mailer\', \'PHP/\' . phpversion())'.
        '->addGenericHeader(\'Content-Type\', \'text/html; charset="utf-8"\')'.
        '->setMessage(\'<strong><b>Resposta:</b> \'.$resposta.\'</strong>\')'.
        '->setWrap(78)->send();');
        if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Resposta inserida com Sucesso',
                "mgs_secundaria" => $resposta
            );
        }else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        $this->VisualizadordeMensagem($mensagem);
    }
    public static function MensagensWidgets(){
        $Registro = &\Framework\App\Registro::getInstacia();
        $modelo = $Registro->_Modelo;
        $Visual = $Registro->_Visual;
        $total = 0; $novos = 0; $espera = 0; $esgotado = 0; $finalizado = 0;
        
        // PEga só as deles 
        $where = false;
        $where = Array('escritor'=>$Registro->_Acl->Usuario_GetID(), 'para'=>0);
        
        
        $array = $modelo->db->Sql_Select('Usuario_Mensagem',$where,0, '','assunto.tempocli,log_date_edit,log_date_add,finalizado');
        if(is_object($array)) $array = Array(0=>$array);
        if($array!==false && !empty($array)){
            reset($array);
            foreach($array as $valor){
                ++$total;
                list($tipo,$tempopassado) = usuario_mensagem_Modelo::Mensagem_TipoChamado($valor);
                if($tipo=='fin'){
                    ++$finalizado;
                }else if($tipo=='nov'){
                    ++$novos;
                }else if($tipo=='lim'){
                    ++$espera;
                }else if($tipo=='esg'){
                    ++$esgotado;
                }else{
                    ++$finalizado;
                }
            }
        }
        // Quantidades de Setores e Assuntos
        
        $setor_qnt = $modelo->db->Sql_Contar('Usuario_Mensagem_Setor');
        
        $assunto = $modelo->db->Sql_Select('Usuario_Mensagem_Assunto',false,0, '','id');
        if(is_object($assunto)) $assunto = Array(0=>$assunto);
        if($assunto!==false && !empty($assunto)){reset($assunto);$assunto_qnt = count($assunto);}else{$assunto_qnt = 0;}
        // Adiciona Widget a Pagina Inicial
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Chamados Abertos', 
            'usuario_mensagem/Suporte/Mensagens_Mostrar/nov', 
            'envelope', 
            '+'.$novos, 
            'block-yellow', 
            false, 
            191
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Chamados à Vencer', 
            'usuario_mensagem/Suporte/Mensagens_Mostrar/lim', 
            'thumbs-down', 
            '+'.$espera, 
            'block-vinho', 
            true, 
            181
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Chamados Vencidos', 
            'usuario_mensagem/Suporte/Mensagens_Mostrar/esg', 
            'time', 
            '+'.$esgotado, 
            'block-green', 
            false,
            161
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Chamados Finalizados', 
            'usuario_mensagem/Suporte/Mensagens_Mostrar/fin', 
            'thumbs-up', 
            '+'.$esgotado, 
            'block-orange', 
            false,
            151
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Todos os Chamados',
            'usuario_mensagem/Suporte/Main/',
            'ticket',
            $total,
            'light-blue',
            true,
            150
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Setores',
            'usuario_mensagem/Setor/Main/',
            'smile',
            $setor_qnt,
            'block-grey',
            false,
            131
        );
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Assuntos',
            'usuario_mensagem/Assunto/Main/',
            'comments-alt',
            $assunto_qnt,
            'block-red',
            false,
            121
        );
    }
    static function Campos_deletar(&$campos){
        // SE nao tiver acesso ao comercio bloqueia
        if(!(\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio'))){
            self::DAO_Campos_Retira($campos, 'cliente');
            self::DAO_Campos_Retira($campos, 'marca');
            self::DAO_Campos_Retira($campos, 'linha');
            self::DAO_Campos_Retira($campos, 'produto');
            self::DAO_Campos_Retira($campos, 'lote');
            self::DAO_Campos_Retira($campos, 'validade');
            self::DAO_Campos_Retira($campos, 'fabricacao');
        }
    }
}
?>
