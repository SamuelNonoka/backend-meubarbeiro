<html>
<head>
  <title>Recuperar senha !!</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="http://novaintranet.alterdata.com.br/app_Themes/NewIntranet/style.css" rel="stylesheet" type="text/css">
  <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900' rel='stylesheet'>
</head>
<body style="padding: 0; margin: 0;">
<div style="background-color: #e6e1d8;">
  <div style="width:600px; max-width:100%; margin: auto; background-color: #fff;font-family: Roboto, sans-serif;">
    <div style="padding: 15px;">
      <h1>
        Recuperar senha!!
      </h1>

      <p style="margin-top: 15px; font-size: 14px;">
        Olá {{ $name }},
        <br><br>
        Segue o código a ser utilizado para recuperação da senha:
      </p>

      <p style="margin-top: 15px; font-size: 22px; font-weight: 900;">
        {{ $code }}
      </p>
      
      <p style="margin-top: 15px; font-size: 14px;">
        <a
          href="{{ $link }}"
          style="display: inline-block; background: #FFAB00; color: #333; padding: 10px 15px; text-decoration: none;"
        >
          Clique aqui para recuperar sua senha
        </a>
      </p>

      <p style="margin-top: 15px; font-size: 10px; font-weight: 300;">
        * Caso não tenha sido você que solicitou, desconsidere este email.
      </p>
    </div>

    <div style="background-color: #423a38; color: #fff; text-align: center; padding: 15px; font-size: 14px; margin-top: 30px;">
      Meu Barbeiro - &copy; Todos os direitos reservados
    </div>
  </div>
</body>
</html>
