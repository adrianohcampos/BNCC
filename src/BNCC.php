<?php

namespace AdrianoHCampos\BNCC;

class BNCC
{

    /**
     * URL Base da BNCC API
     *
     * @var string
     */
    private $urlBase = "http://bnccapi.mec.gov.br";

    /**
     * Dados dos ensinos
     *
     * @var string
     */
    public function dadosEnsinos()
    {
        $array = [
            "infantil" => [
                "idAnos" => [1, 2, 3],
                "idCampos" => [1, 2, 3, 4, 5]
            ],
            "fundamental" => [
                "idAnos" => [1, 2, 3, 4, 5, 6, 7, 8, 9],
                "idCampos" => [1, 2, 3, 4, 5, 6, 7, 8, 9]
            ],
            "medio" => [
                "idAnos" => [1, 2, 3],
                "idCampos" => [36, 37, 38, 39, 40]
            ],
        ];

        return $array;
    }

    /**
     * Método responsável por buscar os conteúdos
     *
     * @param string $ensino
     * @param array $idAnos
     * @param array $idCampos
     * @return void
     */
    public function buscaDados(string $ensino, array $idAnos, array $idCampos)
    {

        switch ($ensino) {
            case 'infantil':
                $rota = 'extrairinfantil';
                $consulta = [
                    'idAnos' => $idAnos,
                    'idComponentes' => $idCampos
                ];

                break;
            case 'fundamental':
                $rota = 'extrairfundamental';
                $consulta = [
                    'idAnos' => $idAnos,
                    'idComponentes' => $idCampos
                ];

                break;
            case 'medio':
                $rota = 'extrair-medio';
                $consulta = [
                    'idAnos' => $idAnos,
                    'idAreas' => $idCampos
                ];

                break;
            default:
                $rota = false;
                break;
        }

        if (!$rota) {
            return false;
        }

        $urlExtracao = $this->urlBase . '/extracao/' . $rota;

        $path = __DIR__ . '/bncc' . $rota . time() . '.xlsx';

        $client = new \GuzzleHttp\Client();
        $resource = \GuzzleHttp\Psr7\Utils::tryFopen($path, 'w');
        $response = $client->request('POST', $urlExtracao, [
            'form_params' => $consulta,
            'sink' => $resource
        ]);

        if ($response->getStatusCode() == 200) {
            $contentType = explode(';', $response->getHeaderLine('content-type'))[0];
            if ($contentType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $this->extrairDados($path);
            }
        }

        return false;
    }

    private function extrairDados($arquivo)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        // $reader->setLoadSheetsOnly(["Arte", "Portugues"]);
        $spreadsheet = $reader->load($arquivo);

        $sheet_count = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheet_count; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $title = $sheet->getTitle();
            if (!str_contains($title, 'Comp')) {
                echo '<pre>';
                print_r($sheet->getTitle());
                echo '</pre>';
            }
            // processa os dados da planilh
        }

        exit;

        echo '<pre>';
        print_r($spreadsheet);
        echo '</pre>';
        exit;

        unlink($arquivo);
    }
}
