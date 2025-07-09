<?php
/**
 * Função para obter conexão com o banco de dados
 * @return PDO
 */
function getConnection() {
    try {
        $host = 'localhost';
        $db = 'ficha_colaboradores';
        $user = 'root';
        $pass = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

/**
 * Função para validar e formatar data
 * @param string $data Data no formato YYYY-MM-DD
 * @return string Data formatada ou null se inválida
 */
function validarData($data) {
    if (empty($data)) return null;
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data ? $data : null;
}

/**
 * Função para validar e formatar telefone
 * @param string $telefone Número de telefone
 * @return string Telefone formatado ou null se inválido
 */
function validarTelefone($telefone) {
    if (empty($telefone)) return null;
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    return strlen($telefone) === 9 ? $telefone : null;
}

/**
 * Função para validar e formatar email
 * @param string $email Endereço de email
 * @return string Email formatado ou null se inválido
 */
function validarEmail($email) {
    if (empty($email)) return null;
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

/**
 * Função para validar NIF
 * @param string $nif Número de identificação fiscal
 * @return bool Retorna true se válido, false se inválido
 */
function validarNIF($nif) {
    if (empty($nif)) return false;
    $nif = preg_replace('/[^0-9]/', '', $nif);
    if (strlen($nif) !== 9) return false;
    
    $soma = 0;
    $pesos = [9, 8, 7, 6, 5, 4, 3, 2];
    
    for ($i = 0; $i < 8; $i++) {
        $soma += $nif[$i] * $pesos[$i];
    }
    
    $resto = $soma % 11;
    $digito = 11 - $resto;
    $digito = $digito === 10 ? 0 : $digito;
    
    return $digito === (int)$nif[8];
}
