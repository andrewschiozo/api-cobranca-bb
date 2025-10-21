<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

class RegistrarBoletoRapidoDTO
{
    public function __construct(
        public readonly string $numeroConvenio,
        public readonly string $dataVencimento,
        public readonly string $valorOriginal,
        public readonly string $numeroTituloCliente,
        public readonly array $pagador
    ) {}

    public static function fromArray(array $data): RegistrarBoletoRapidoDTO
    {
        try{
            return new RegistrarBoletoRapidoDTO(
                numeroConvenio: $data['numeroConvenio'],
                dataVencimento: $data['dataVencimento'],
                valorOriginal: $data['valorOriginal'],
                numeroTituloCliente: $data['numeroTituloCliente'],
                pagador: $data['pagador']
            );
        } catch(\Exception $e){
            throw $e;
        }
    }

}

// {
// 	"numeroConvenio": "3128557",
// 	"dataVencimento": "06.05.2026",
// 	"valorOriginal": "55.33",
// 	"numeroTituloCliente": "00031285572510210011",
// 	"pagador": {
// 		"tipoInscricao": "2",
// 		"numeroInscricao": "81676009000119",
// 		"cep": "1000000"
// 	}
// }