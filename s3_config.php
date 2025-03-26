<?php
require 'vendor/autoload.php'; // Asegúrate de cargar la librería de AWS

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

try {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => 'us-east-1' // Cambia a la región de tu bucket
    ]);

    // Prueba la conexión con S3 (opcional)
    $result = $s3->listBuckets();
    echo "Conexión exitosa a S3";
} catch (AwsException $e) {
    die("Error al conectar con S3: " . $e->getMessage());
}
?>
