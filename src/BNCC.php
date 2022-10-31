<?php

namespace AdrianoHCampos\BNCC;

class BNCC
{
    public $ensino, $idAnos;
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
        $this->ensino = $ensino;
        $this->idAnos = $idAnos;
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

        try {
            $client = new \GuzzleHttp\Client();
            $resource = \GuzzleHttp\Psr7\Utils::tryFopen($path, 'w');
            $response = $client->request('POST', $urlExtracao, [
                'form_params' => $consulta,
                'sink' => $resource
            ]);
        } catch (\RuntimeException $e) {
            unlink($path);
            return $e->getMessage();
            die;
        }

        if ($response->getStatusCode() == 200) {
            $contentType = explode(';', $response->getHeaderLine('content-type'))[0];
            if ($contentType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                return $this->extrairDados($path);
            }
        } else {
            unlink($path);
        }

        return false;
    }

    private function extrairDados($arquivo)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($arquivo);

        $sheet_count = $spreadsheet->getSheetCount();

        $res = [];
        for ($i = 0; $i < $sheet_count; $i++) {

            $sheet = $spreadsheet->getSheet($i);
            $title = $sheet->getTitle();

            if (
                !str_contains($title, 'Com')
                && !str_contains($title, 'Cam')
                && !str_contains($title, 'Direitos')
            ) {

                $array = [];
                $x = 0;

                foreach ($sheet->toArray() as $value) {

                    ++$x;
                    if ($x <= 3) {
                        continue;
                    }
                    if ($value[0] == '') {
                        continue;
                    }

                    if ($this->ensino == 'infantil') {
                        $data = $this->infantil($value);
                    } else if ($this->ensino == 'fundamental') {
                        $data = $this->fundamental($value);
                    } else if ($this->ensino == 'medio') {
                        $data = $this->medio($value);
                    } else {
                        $data = false;
                    }

                    if ($data) {
                        $array[] = $data;
                    }
                }

                $res[] = [
                    'conteudo' => $title,
                    'lista' => $array
                ];
            }
        }

        unlink($arquivo);

        return $res;
    }

    public function infantil($value)
    {
        $codigo = $this->extraiCodigo($value[2]);
        $ano = $value[1];
        $habilidades = $value[2];

        if ($codigo == null) {
            return false;
        }
        return [
            'ensino' => $this->ensino,
            'codigo' => $codigo,
            'ano' => $ano,
            'unidadeTematica' => null,
            'objetoConhecimento' => null,
            'habilidades' => $habilidades,
        ];
    }

    public function fundamental($value)
    {
        $codigo = $this->extraiCodigo($value[4]);
        $ano = $value[1];
        $unidadeTematica = $value[2];
        $objetoConhecimento = $value[3];
        $habilidades = $value[4];

        if ($codigo == null) {
            $codigo = $this->extraiCodigo($value[5]);
            $ano = $value[1];
            $unidadeTematica = $value[3];
            $objetoConhecimento = $value[4];
            $habilidades = $value[5];
        }

        return [
            'ensino' => $this->ensino,
            'codigo' => $codigo,
            'ano' => $ano,
            'unidadeTematica' => $unidadeTematica,
            'objetoConhecimento' => $objetoConhecimento,
            'habilidades' => $habilidades,
        ];
    }

    public function medio($value)
    {
        $codigo = $value[1];

        return [
            'ensino' => $this->ensino,
            'codigo' => $codigo,
            'ano' => $value[0],
            'unidadeTematica' => null,
            'objetoConhecimento' => null,
            'habilidades' => $value[2],
        ];
    }

    public function extraiCodigo($string)
    {
        // extrai o codigo da habilidade
        preg_match('#\((.*?)\)#', $string, $match);
        return isset($match[1]) ? $match[1] : null;
    }
}
