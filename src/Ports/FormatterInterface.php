<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

/**
 * Interface que define o contrato para a formatação dos dados 
 * para enviar para a API.
 */
interface FormatterInterface
{
    /**
     * Formata os dados para enviar para a API.
     * 
     * @param DTOInterface $dto Dados a serem formatados
     * @return array Dados formatados
     */
    public function format(DTOInterface $dto): array;
}