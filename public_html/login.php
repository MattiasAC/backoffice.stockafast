<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" href="https://admin.altahr.se/favicon.ico">
    <title>Altahr Consulting AB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <link href="/vendor2/bootstrap-5.3.2/css/bootstrap.css" rel="stylesheet">
</head>
<body style="background-color: #2d4373">
<div class="card mx-auto" style="width: 500px;margin-top:100px;">
  <div class="card-body">

<form class="form" role="form" action="/" autocomplete="off" id="formLogin" novalidate="" method="POST">
    <div class="form-group">
        <label for="username">Användarnamn</label>
        <input type="text" class="form-control form-control-lg" name="username" id="uname1" required="">
        <div class="invalid-feedback">Vänligen fyll i användarnamn.</div>
    </div>
    <div class="form-group">
        <label>Lösenord</label>
        <input type="password" name="password" class="form-control form-control-lg" id="pwd1" required="">
        <div class="invalid-feedback">Ange lösenord</div>
    </div>
<!--    <script src='https://www.google.com/recaptcha/api.js' async defer></script>-->
<!--    <div class="g-recaptcha" data-sitekey="6LdIduQoAAAAAFBP-xja861t3gKQPYZKcs_dj4-p"></div>-->
    <div class="form-group py-4">
        <button type="submit" name="login" class="btn btn-success btn-lg float-right" id="btnLogin">Login</button>
    </div>
</form></div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</html>
