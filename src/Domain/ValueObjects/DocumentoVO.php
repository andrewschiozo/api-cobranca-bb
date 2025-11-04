<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Enums\DocumentoTipoEnum;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\DocumentoInvalidoException;

class DocumentoVO
{
    
    public readonly DocumentoTipoEnum $tipo;
    public readonly string $valor;

    public function __construct(string $documento)
    {
        $isCpf = self::isValidCpf($documento);
        if ($isCpf) {
            $this->tipo = DocumentoTipoEnum::CPF;
        }

        $isCnpj = self::isValidCnpj($documento);
        if ($isCnpj) {
            $this->tipo = DocumentoTipoEnum::CNPJ;
        }

        if(!$isCpf && !$isCnpj) {
            throw new DocumentoInvalidoException("O documento {$documento} deve ser um CPF ou CNPJ.");
        }

        $this->valor = $documento;
    }

    /**
     * Retorna uma string apenas com números e letras maiusculas
     * 
     * @param string $documento Documento a ser limpo
     * @return string
     */
    public static function limparCpfCnpj(string $documento): string
    {
        return trim(strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $documento)));
    }

    /**
     * Verifica se um CPF é válido
     * 
     * @param string $cpf CPF a ser validado
     * @return bool
     */
    public static function isValidCpf(string $cpf): bool
    {
        $cpf = self::limparCpfCnpj($cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        if(strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (count(array_unique(str_split($cpf))) === 1) {
            return false;
        }

        for ($i = 9; $i < 11; $i++) {
            for ($j = 0, $k = 0; $k < $i; $k++) {
                $j += $cpf[$k] * (($i + 1) - $k);
            }
            $j = ((10 * $j) % 11) % 10;
            if ($cpf[$k] != $j) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica se um CNPJ é válido
     * 
     * @param string $cnpj CNPJ a ser validado
     * @return bool
     */
    public static function isValidCnpj(string $cnpj): bool
    {
        $cnpj = self::limparCpfCnpj($cnpj);
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (count(array_unique(str_split($cnpj))) === 1) {
            return false;
        }

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

	    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}