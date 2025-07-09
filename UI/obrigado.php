<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obrigado pela sua inscrição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="guest-container mt-5">
            <div class="text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                <h2 class="mt-4">Obrigado pela sua inscrição!</h2>
                <p class="lead">O seu pedido de convite foi recebido com sucesso.</p>
                <p>Em breve entraremos em contacto consigo.</p>
                <a href="convidado.php" class="btn btn-primary mt-3">Voltar ao formulário</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
