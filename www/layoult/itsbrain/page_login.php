<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo SISTEMA_NOME; ?> - <?php if($params['site_titulo']==''){ echo 'Sem Titulo'; }else{ echo $params['site_titulo']; } ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CONFIG_PADRAO_TECLADO; ?>"/>   
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="description"    content="" />
    <meta name="author"         content="" />
    <link href="<?php echo $params['url_css']; ?>main.css" rel="stylesheet" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Cuprum' rel='stylesheet' type='text/css' />
</head>     
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body style="background: url(<?php echo ARQ_URL; ?>_Sistema/fundo.jpg)no-repeat center center fixed;">

    <!-- Login form area -->
    <div class="loginWrapper">
        <div class="loginLogo"><img alt="logo" style="max-height:120px; margin-top:-50px; margin-left:40px;" src="<?php echo ARQ_URL; ?>_Sistema/logo_login.<?php echo TEMA_LOGO; ?>" /></div>
        <div class="loginPanel">
            <div class="head"><h5 class="iUser">Login</h5></div>
            <form action="<?php echo URL_PATH.SISTEMA_DIR_INT; ?>" method="POST" id="valid" class="mainForm">
                <fieldset>
                    <div class="loginRow noborder">
                        <label for="req1">Usuário:</label>
                        <div class="loginInput"><input type="text" class="obrigatorio" id="sistema_login" name="sistema_login" placeholder="Usuário" autofocus /></div>
                    </div>

                    <div class="loginRow">
                        <label for="req2">Senha:</label>
                        <div class="loginInput"><input type="password" class="obrigatorio" id="sistema_senha" name="sistema_senha" placeholder="Senha" /></div>
                    </div>

                    <div class="loginRow">
                        <div class="rememberMe"><input type="checkbox" id="check2" name="chbox" /><label for="check2">Lembrar-me</label></div>
                        <div class="submitForm"><input type="submit" value="Entrar" class="redBtn" /></div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div id="footer">
        <div class="wrapper">
            <span>&copy; Copyright 2014. Todos os Direitos Reservados.</span>
        </div>
    </div>
</body>
<!-- END BODY -->
</html>