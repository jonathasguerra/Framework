<?php
$config_Modulo = function (){
    return Array(
        'Nome'                      =>  'usuario_rede',
        'Descrição'                 =>  '',
        'System_Require'            =>  '2.21.1',
        'Version'                   =>  '0.0.1',
        'Dependencias'              =>  false,
    );
};
$config_Menu = function (){
    return Array(
        'Perfil'=>Array(
            'Filhos'                => Array('Rede'=>Array(
                'Nome'                  => 'Rede',
                'Link'                  => 'usuario_rede/Listar/Main',
                'Gravidade'             => 9,
                'Img'                   => 'menusuperior/rede.png',
                'Icon'                  => 'sitemap',
                'Filhos'                => false,
            ),),
        ),
    );
};
$config_Permissoes = function (){
    return Array(
        Array(
            'Nome'                  => 'Visualizar Rede',
            'Desc'                  => '',
            'Chave'                 => 'usuario_rede_Listar',
            'End'                   => 'usuario_rede/Listar', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'usuario_rede', // Modulo Referente
            'SubModulo'             => 'Listar',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
        )
    );
};
/**
 * Serve Para Personalizar o Modulo de Acordo com o gosto de cada "Servidor"
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$config_Funcional = function (){
    return Array();
};
?>
