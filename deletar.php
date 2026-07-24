<?php
// deletar.php
require_once "config/Database.php";
require_once "models/Contato.php";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $contato = new Contato($db);
    $contato->id = $_GET['id'];

    if ($contato->deletar()) {
        header("Location: index.php?msg=deletado");
        exit();
    } else {
        header("Location: index.php?msg=erro");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>