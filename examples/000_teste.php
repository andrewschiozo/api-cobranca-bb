<?php

use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\DataEmissaoVO;

require_once __DIR__ . '/../vendor/autoload.php';
$emissao = new DateTimeImmutable('2024-08-06');
$dataAtual = new DateTimeImmutable('today');

try {
    $d = new DataEmissaoVO($emissao, $dataAtual, $dataAtual->modify('-2 year'));
    echo 'Data de emissão válida: ' . $d->data->format('d.m.Y') . PHP_EOL;
} catch (Throwable $e) {
    echo "Data de emissão inválida: " . $emissao->format('d.m.Y') . PHP_EOL;
    echo "Code: " . $e->getCode() . PHP_EOL;
    echo "Message: " . $e->getMessage() . PHP_EOL;
    exit(1);
}