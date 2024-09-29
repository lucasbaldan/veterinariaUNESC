<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Laboratorios
{

    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdLaboratorio = !empty($Formulario['cdLaboratorio']) ? $Formulario['cdLaboratorio'] : '';
            $nmLaboratorio = !empty($Formulario['nmLaboratorio']) ? $Formulario['nmLaboratorio'] : '';
            $ativo = !empty($Formulario['ativo']) ? $Formulario['ativo'] : '';

            if (empty($nmLaboratorio)) {
                throw new Exception("Preencha o campo <b>Nome</b> para concluir o cadastro.");
            }


            $laboratorio = new \App\Models\Laboratorios($nmLaboratorio, $ativo, $cdLaboratorio);
            if (empty($cdLaboratorio)) {
                $laboratorio->Insert();
            } else {
                $laboratorio->Update();
            }

            if (!$laboratorio->GetResult()) {
                throw new Exception($laboratorio->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarDadosLaboratorios(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();
            $cdLaboratorio = !empty($Formulario['cdLaboratorio']) ? $Formulario['cdLaboratorio'] : '';

            $laboratorio = \App\Models\Laboratorios::findById($cdLaboratorio);

            if (empty($laboratorio->getCodigo())) {
                throw new Exception("<b>Erro ao tentar localizar os dados da pessoa</b><br><br> Por favor, tente novamente.", 400);
            }

            $retorno = [
                'nm_laboratorio' => $laboratorio->getNome(),
                'ativo' => $laboratorio->getAtivo(),
            ];

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function retornaLaboratorios(Request $request, Response $response)
    {

        try {
            $retorno = \App\Models\Laboratorios::GeneralSearch('');

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function Excluir(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();
            $cdLaboratorio = !empty($Formulario['cdLaboratorio']) ? $Formulario['cdLaboratorio'] : '';

            $laboratorio = new \App\Models\Laboratorios('', '', $cdLaboratorio);
            $laboratorio->Delete();

            if (!$laboratorio->getResult()) {
                throw new Exception("<b>Erro ao tentar processar requisição</b><br><br> " . $laboratorio->getMessage(), 500);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "LABORATORIOS.CD_LABORATORIO";
            if ($orderBy == 1) $orderBy = "LABORATORIOS.NM_LABORATORIOS";
            if ($orderBy == 2) $orderBy = "LABORATORIOS.FL_ATIVO";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Laboratorios::SelectGrid($parametrosBusca);
            $dados = [
                "draw" => (int)$grid['draw'],
                "recordsTotal" => isset($dadosSelect[0]['total_table']) ? $dadosSelect[0]['total_table'] : 0,
                "recordsFiltered" => isset($dadosSelect[0]['total_filtered']) ? $dadosSelect[0]['total_filtered'] : 0,
                "data" => $dadosSelect
            ];


            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $dados];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function UploadLogo(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();
            // Captura os arquivos enviados
            $arquivos = $request->getUploadedFiles();
            $logo = isset($arquivos['logoLaboratorio']) ? $arquivos['logoLaboratorio'] : null;
            $cdLaboratorio = !empty($dadosForm['cdLaboratorio']) ? $dadosForm['cdLaboratorio'] : '';

            // Verificar se já tem logo
            $retorno = \App\Models\Laboratorios::RetornaDadosLaboratorio($cdLaboratorio);
            if (!empty($retorno['ID_LOGO'])) {
                throw new \Exception("Só é permitido o upload de uma logo por laboratório.");
            }

            if (!$logo || $logo->getError() !== UPLOAD_ERR_OK || empty($cdLaboratorio)) {
                throw new \Exception("Houve um erro ao processar a requisição. Tente novamente mais tarde.");
            }

            // Diretório de upload
            // $uploadDirectory = __DIR__ . '/../Assets/imagens/logos_laboratorios';
            $uploadDirectory = __DIR__ . '/../img/logos_laboratorios';

            // Gera um nome de arquivo único
            $filename = self::moveUploadedFile($uploadDirectory, $logo);

            if (!file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename)) throw new Exception("Erro ao fazer upload da logo");

            $laboratorio = \App\Models\Laboratorios::findById($cdLaboratorio);
            $laboratorio->setLogo($filename);
            $laboratorio->InserirLogo();

            if (!$laboratorio->getResult()) {
                throw new Exception($laboratorio->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            // Resposta de erro
            $respostaServidor = json_encode(["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => '']);
            $codigoHTTP = 500;
        }

        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    private static function moveUploadedFile($directory, $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // Gera um nome de arquivo único
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public static function excluirLogo(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();
            $filename = !empty($dadosForm['idImagem']) ? $dadosForm['idImagem'] : '';

            if (empty($filename)) {
                throw new \Exception("Houve um erro ao processar a requisição. Tente novamente mais tarde.");
            }

            // Diretório de upload
            $uploadDirectory = __DIR__ . '/../Assets/imagens/logos_laboratorios';

            if (!file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename)) throw new Exception("Erro ao fazer exclusão da imagem");

            unlink($uploadDirectory . DIRECTORY_SEPARATOR . $filename);

            $Imagem = \App\Models\Laboratorios::deleteImageById($filename);

            if (!$Imagem) {
                throw new Exception("Erro ao excluir Imagem");
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }

        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
