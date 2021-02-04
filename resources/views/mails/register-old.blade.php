<html>
<head>
  <title>Seja Bem-Vindo !!</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="http://novaintranet.alterdata.com.br/app_Themes/NewIntranet/style.css" rel="stylesheet" type="text/css">
  <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900' rel='stylesheet'>
</head>
<body>
<div style="background-color: #e6e1d8; font-family: 'Montserrat', sans-serif;">
  <div style="width:600px; max-width:100%; margin: auto; background-color: #fff;font-family: Roboto, sans-serif;">
    <div style="padding: 15px;">
      <h1>
        Seja Bem-vindo!!
      </h1>

      <p style="margin-top: 15px; font-size: 14px;">
        Olá {{ $name }}, seja bem-vindo ao <strong>MeuBarbeiro</strong>!!
      </p>

      <p style="margin-top: 15px; font-size: 14px;">
        Seguem abaixo os seus dados de acesso:
      </p>
      
      <p style="margin-top: 15px; font-size: 14px;">
        <strong>E-mail:</strong>
        <span style="font-size:16px;color:#333333;">
          {{ $email }}
        </span>
      </p>

      <p style="margin-top: 5px; font-size: 14px;">
        <strong>Sua senha:</strong>
        <span style="font-size:16px;color:#333333;">
          {{ $password }}
        </span>
      </p>

      <p style="margin-top: 15px; font-size: 14px;">
        <a
          href="{{ $confirm_link }}"
          style="display: inline-block; background: #FFAB00; color: #333; padding: 10px 15px; text-decoration: none;"
        >
          Clique aqui para confirmar seu cadastro
        </a>
      </p>

      <p style="margin-top: 15px; font-size: 14px;">
        Caso não tenha sido você que se cadastrou,
        <a href="{{ $remove_link }}" style="font-weigth: 800; color:#333333; text-decoration: underline;">
          Clique aqui
        </a>
      </p>
    </div>

    <div style="background-color: #423a38; color: #fff; text-align: center; padding: 15px; font-size: 14px; margin-top: 30px;">
      Meu Barbeiro - &copy; Todos os direitos reservados
    </div>
  </div>
</body>
</html>
