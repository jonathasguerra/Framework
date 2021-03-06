<?php
$config_Modulo = function (){
    return Array(
        'Nome'                      =>  'usuario',
        'Descrição'                 =>  '',
        'System_Require'            =>  '2.21.1',
        'Version'                   =>  '0.0.1',
        'Dependencias'              =>  false,
    );
};
$config_Menu = function (){
    return Array(
        'Administrar'=>Array(
            'Filhos'                => Array('Usuários'=>Array(
                'Nome'                  => 'Usuários',
                'Link'                  => 'usuario/Admin/ListarOutros',
                'Gravidade'             => 9996,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'user',
                'Filhos'                => false,
            ),\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome')=>Array(
                'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome'),
                'Link'                  => 'usuario/Admin/ListarFuncionarios',
                'Gravidade'             => 9994,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'group',
                'Permissao_Func'        => Array(// Permissoes NEcessarias
                    'usuario_Admin_Funcionario' => true
                ),
                'Filhos'                => false,
            ),\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome')=>Array(
                'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome'),
                'Link'                  => 'usuario/Admin/ListarCliente',
                'Gravidade'             => 9992,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'user',
                'Permissao_Func'        => Array(// Permissoes NEcessarias
                    'usuario_Admin_Cliente' => true
                ),
                'Filhos'                => false,
            )),
        ),'Acesso' => Array(
            'Nome'                  => 'Acesso',
            'Link'                  => '#',
            'Gravidade'             => 100,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'user',
            'Filhos'                => Array('Usuários'=>Array(
                'Nome'                  => 'Usuários',
                'Link'                  => 'usuario/Acesso/Listar_Clientesnao',
                'Gravidade'             => 1,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'dashboard',
                'Filhos'                => false,
            ),),
        ),
    );
};
$config_Permissoes = function (){
    return Array(
        
        
        // Grupos
        Array(
            'Nome'                  => 'Sistema - Controle Grupos - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'usuario_grupo', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/grupo', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'grupo',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_grupo' => true
            ),
        ),
        // Funcionario
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome').' - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_ListarFuncionarios', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/ListarFuncionarios', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'ListarFuncionarios',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Funcionario' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome').' - Add',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Funcionario_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Funcionario_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Funcionario_Add,Funcionario_Add2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Funcionario' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome').' - Editar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Funcionario_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Funcionario_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Funcionario_Edit,Funcionario_Edit2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Funcionario' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Funcionario_nome').' - Deletar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Funcionario_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Funcionario_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Funcionario_Del',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Funcionario' => true
            ),
        ),
        
        
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome').' - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_ListarCliente', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/ListarCliente', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'ListarCliente',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Cliente' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome').' - Add',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Cliente_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Cliente_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Cliente_Add,Cliente_Add2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Cliente' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome').' - Editar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Cliente_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Cliente_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Cliente_Edit,Cliente_Edit2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Cliente' => true
            ),
        ),
        Array(
            'Nome'                  => \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome').' - Deletar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Cliente_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Cliente_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Cliente_Del',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'usuario_Admin_Cliente' => true
            ),
        ),
        
        
        Array(
            'Nome'                  => 'Usuario - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_ListarOutros', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/ListarOutros', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'ListarOutros',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => 'Usuario - Add',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Usuarios_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Usuarios_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Usuarios_Add,Usuarios_Add2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => 'Usuario - Editar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Usuarios_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Usuarios_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Usuarios_Edit,Usuarios_Edit2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => 'Usuario - Deletar',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Admin_Usuarios_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Admin/Usuarios_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Usuarios_Del',  // Metodos referentes separados por virgula
        ),
        
        // PERMISSOES DE USUARIO
        Array(
            'Nome'                  => 'Permissões (Usuário) - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'usuario_Acesso', // CHave unica nunca repete, chave primaria
            'End'                   => 'usuario/Acesso', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario', // Modulo Referente
            'SubModulo'             => 'Acesso',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
        ),
        
    );
};
/**
 * Serve Para Personalizar o Modulo de Acordo com o gosto de cada "Servidor"
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$config_Funcional = function (){
    return Array(
        'usuario_grupo'  => Array(
            'Nome'                  => 'Grupos -> Se Mostra Opções de Grupos',
            'Desc'                  => 'Se Mostra Opções de Grupos',
            'chave'                 => 'usuario_grupo',
            'Valor'                 => false,  // false, true, ou array com os grupos que pode
        ),
        'usuario_usuarios_showconfig'  => Array(
            'Nome'                  => 'Usuarios -> Ocultar Grupos',
            'Desc'                  => 'Se verdadeiro mostra so os clientes ativos',
            'chave'                 => 'usuario_usuarios_showconfig',
            'Valor'                 => false,  // false, true, ou array com os grupos que pode
        ),
        'usuario_Grupo_Mostrar'  => Array(
            'Nome'                  => 'Usuarios -> Ocultar Grupos',
            'Desc'                  => 'Aonde mostra adição de grupos',
            'chave'                 => 'usuario_Grupo_Mostrar',
            'Valor'                 => true,  // false, true, ou array com os grupos que pode
        ),
        // Grupos que tem login/senha
        'usuario_Login'  => Array(
            'Nome'                  => 'Usuarios -> Login',
            'Desc'                  => 'Se possue login em Usuarios',
            'chave'                 => 'usuario_Login',
            'Valor'                 => true,  // false, true, ou array com os grupos que pode
        ),
        'usuario_Anexo'  => Array(
            'Nome'                  => 'Usuarios -> Anexo',
            'Desc'                  => 'Se possue Anexo em Usuarios',
            'chave'                 => 'usuario_Anexo',
            'Valor'                 => false,
        ),
        'usuario_Admin_Site'  => Array(
            'Nome'                  => 'Usuarios -> Site',
            'Desc'                  => 'Se possue Site em Usuarios',
            'chave'                 => 'usuario_Admin_Site',
            'Valor'                 => true,
        ),
        'usuario_Admin_Ativado'  => Array(
            'Nome'                  => 'Usuarios -> Status',
            'Desc'                  => 'Se possue Status em Usuarios (Ativado)',
            'chave'                 => 'usuario_Admin_Ativado',
            'Valor'                 => true,
        ),
        'usuario_Admin_Ativado_Listar'  => Array(
            'Nome'                  => 'Usuarios -> Status Listagem',
            'Desc'                  => 'Se mostra na listagem o Status',
            'chave'                 => 'usuario_Admin_Ativado_Listar',
            'Valor'                 => false,
        ),
        'usuario_Admin_Foto'  => Array(
            'Nome'                  => 'Usuarios -> Foto',
            'Desc'                  => 'Se possue Foto em Usuarios',
            'chave'                 => 'usuario_Admin_Foto',
            'Valor'                 => true,  // false, true, ou array com os grupos que pode
        ),
        'usuario_Admin_Email'  => Array(
            'Nome'                  => 'Usuarios -> Email',
            'Desc'                  => 'Enviar Email Direto Entre Usuarios',
            'chave'                 => 'usuario_Admin_Email',
            'Valor'                 => false,
        ),
        'usuario_Admin_EmailUnico'  => Array(
            'Nome'                  => 'Usuarios -> Email Unico',
            'Desc'                  => 'Se email nao puder repetir, vem como true',
            'chave'                 => 'usuario_Admin_EmailUnico',
            'Valor'                 => true,
        ),
        'usuario_Principal_Widgets'  => Array(
            'Nome'                  => 'Usuarios -> Se mostra widget',
            'Desc'                  => 'Se mostra widgetna pagina inicial',
            'chave'                 => 'usuario_Principal_Widgets',
            'Valor'                 => true,
        ),
        
        // Funcionario
        'usuario_Admin_Funcionario'  => Array(
            'Nome'                  => 'Usuarios -> Funcionarios',
            'Desc'                  => 'Se possui Funcionarios',
            'chave'                 => 'usuario_Admin_Funcionario',
            'Valor'                 => true,
        ),
        'usuario_Funcionario_nome'  => Array(
            'Nome'                  => 'Usuarios -> Funcionarios',
            'Desc'                  => 'Nome Funcionarios',
            'chave'                 => 'usuario_Funcionario_nome',
            'Valor'                 => 'Funcionários',
        ),
        
        // Cliente
        'usuario_Admin_Cliente'  => Array(
            'Nome'                  => 'Usuarios -> Funcionarios',
            'Desc'                  => 'Se possui Clientes',
            'chave'                 => 'usuario_Admin_Cliente',
            'Valor'                 => true,
        ),
        'usuario_Cliente_nome'  => Array(
            'Nome'                  => 'Usuarios -> Nome do Cliente',
            'Desc'                  => 'Nome do Cliente',
            'chave'                 => 'usuario_Cliente_nome',
            'Valor'                 => 'Clientes',
        ),
        'usuario_Cliente_PrecoDiferenciado'  => Array(
            'Nome'                  => 'Usuarios Cliente -> PrecoDiferenciado',
            'Desc'                  => 'Opcao preço normal ou diferenciado',
            'chave'                 => 'usuario_Cliente_PrecoDiferenciado',
            'Valor'                 => false,
        ),
        'usuario_Comentarios'  => Array(
            'Nome'                  => 'Usuarios -> Comentarios',
            'Desc'                  => 'Se tem historico de comentarios em usuarios',
            'chave'                 => 'usuario_Comentarios',
            'Valor'                 => false,
        ),
    );
};
?>
