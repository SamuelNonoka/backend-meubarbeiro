<html>
<head>
  <title>Seja Bem-Vindo !</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="http://novaintranet.alterdata.com.br/app_Themes/NewIntranet/style.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
  <div style="background: #000; width: 800px; max-width: 100%; margin: auto;">
  <div style="font-family: 'Montserrat', sans-serif; color: #3B3B3B; max-width: 100%; width: 800px; margin: auto; position: relative; display: block; background-image: url(https://appmeubarbeiro.com.br/tesoura.png); background-size: 150px; background-repeat: no-repeat; background-position: bottom right;">
    <div style="padding: 30px 15px; width: 600px; max-width: 100%; margin: auto; box-sizing: border-box; overflow: hidden;">
      <div style="width: 49%; display: inline-block; min-width: 250px; margin-bottom: 60px;">
        <img src="https://appmeubarbeiro.com.br/logo.png" style="width: 180px; max-width: 100%;" />
      </div>
      <div style="width: 49%; display: inline-block; min-width: 250px; transform: translateY(-100%);">
        <h1 style="color: #fff;">
          Seja Bem-vindo!
        </h1>
      </div>
      <div style="background-color: #DCB975; border-radius: 10px; padding: 30px; font-size: 16px;">
        <p>
          Olá {{ $name }}, seja bem-vindo(a) ao <strong style="white-space: nowrap;">Meu Barbeiro</strong>
        </p>
        <p style="margin-top: 16px;">
          Seguem os dados de acesso:
        </p>
        <p style="margin-top: 16px;">
          <strong>E-mail:</strong>
          <br>
          {{ $email }}
        </p>
      </div>
    </div>
    <div style="background-color: #6cd4cacc; text-align: center; padding: 15px 0; font-size: 18px; margin: auto;">
      Meu Barbeiro - &copy; Todos os direitos reservados
    </div>
  </div>
  </div>
</body>
</html>