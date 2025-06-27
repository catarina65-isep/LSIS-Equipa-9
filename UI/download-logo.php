<?php
// Criar diretório para imagens se não existir
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}

// Copiar a imagem local para o diretório images
$sourcePath = __DIR__ . '/logo-tlantic.png'; // Caminho da imagem local
$targetPath = __DIR__ . '/images/logo-tlantic.png';

if (file_exists($sourcePath)) {
    copy($sourcePath, $targetPath);
} else {
    // Se a imagem não existir, criar uma logo simples
    $width = 150;
    $height = 50;
    
    // Criar imagem
    $image = imagecreatetruecolor($width, $height);
    
    // Definir cores
    $background = imagecolorallocate($image, 0, 86, 179); // Azul Tlantic
    $text_color = imagecolorallocate($image, 255, 255, 255); // Branco
    
    // Preencher fundo
    imagefilledrectangle($image, 0, 0, $width, $height, $background);
    
    // Escrever texto "TLANTIC"
    $text = "TLANTIC";
    $font = 5;
    $x = 20;
    $y = 35;
    imagestring($image, $font, $x, $y, $text, $text_color);
    
    // Salvar como PNG
    imagepng($image, $targetPath);
    imagedestroy($image);
}

// Redirecionar para a página RH
header('Location: rh.php');
exit();
