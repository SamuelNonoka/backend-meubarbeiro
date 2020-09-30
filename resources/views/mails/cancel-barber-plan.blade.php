<html>
<head>
  <title>Seu plano foi alterado !!</title>
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
        Seu plano foi cancelado!!
      </h1>

      <p style="margin-top: 15px; font-size: 14px;">
        Olá {{ $barber_name }}, seu plano foi cancelado com sucesso!
      </p>

      <p style="margin-top: 15px; font-size: 14px;">
        Seguem abaixo os seus dados:
      </p>
      
      <p style="margin-top: 15px; font-size: 14px;">
        <strong>Nome:</strong>
        <span style="font-size:16px;color:#333333;">
          {{ $plan_name }}
        </span>
      </p>

      <p style="margin-top: 5px; font-size: 14px;">
        <strong>Valor:</strong>
        <span style="font-size:16px;color:#333333;">
          {{ $plan_value }}
        </span>
      </p>
    </div>

    <div style="background-color: #423a38; color: #fff; text-align: center; padding: 15px; font-size: 14px; margin-top: 30px;">
      Meu Barbeiro - &copy; Todos os direitos reservados
    </div>
  </div>
</body>
</html>
