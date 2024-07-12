<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;

class Atendimentos
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = " ficha_lpv.CD_FICHA_LPV";
            if ($orderBy == 1) $orderBy = "ficha_lpv.DT_FICHA";
            if ($orderBy == 2) $orderBy = "animais.nm_animal";
            if ($orderBy == 3) $orderBy = "tipo_animal.descricao";
            if ($orderBy == 4) $orderBy = "especies.descricao";
            if ($orderBy == 5) $orderBy = "racas.descricao";
            if ($orderBy == 6) $orderBy = "animais.sexo";
            if ($orderBy == 7) $orderBy = "dono.nm_pessoa";
            if ($orderBy == 8) $orderBy = "veterinario.nm_pessoa";
            if ($orderBy == 9) $orderBy = "cidades.nome";
            if ($orderBy == 10) $orderBy = "ficha_lpv.DS_MATERIAL_RECEBIDO";
            if ($orderBy == 11) $orderBy = "ficha_lpv.DS_DIAGNOSTICO_PRESUNTIVO";
            if ($orderBy == 12) $orderBy = "ficha_lpv.FL_AVALIACAO_TUMORAL_COM_MARGEM";
            if ($orderBy == 13) $orderBy = "ficha_lpv.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA";
            if ($orderBy == 14) $orderBy = "ficha_lpv.DS_LESOES_MACROSCOPICAS";
            if ($orderBy == 15) $orderBy = "ficha_lpv.DS_LESOES_HISTOLOGICAS";
            if ($orderBy == 16) $orderBy = "ficha_lpv.DS_DIAGNOSTICO";
            if ($orderBy == 17) $orderBy = "ficha_lpv.DS_RELATORIO";

            $datas = explode('|', !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '');
            $dataInicio = '';
            $dataFim = '';

            if (isset($datas[0]) && !empty($datas[0])) $dataInicio = $datas[0];

            if (isset($datas[1]) && !empty($datas[1])) $dataFim = $datas[1];

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDataInicio" => $dataInicio,
                "pesquisaDataFim" => $dataFim,
                "pesquisaNomeAnimal" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "pesquisaTipoAnimal" => !empty($grid['columns'][3]['search']['value']) ? $grid['columns'][3]['search']['value'] : '',
                "pesquisaEspecieAnimal" => !empty($grid['columns'][4]['search']['value']) ? $grid['columns'][4]['search']['value'] : '',
                "pesquisaRacaAnimal" => !empty($grid['columns'][5]['search']['value']) ? $grid['columns'][5]['search']['value'] : '',
                "pesquisaSexoAnimal" => !empty($grid['columns'][6]['search']['value']) ? $grid['columns'][6]['search']['value'] : '',
                "pesquisaTutor" => !empty($grid['columns'][7]['search']['value']) ? $grid['columns'][7]['search']['value'] : '',
                "pesquisaVeterinario" => !empty($grid['columns'][8]['search']['value']) ? $grid['columns'][8]['search']['value'] : '',
                "pesquisaMunicipio" => !empty($grid['columns'][9]['search']['value']) ? $grid['columns'][9]['search']['value'] : '',
                "pesquisaMaterial" => !empty($grid['columns'][10]['search']['value']) ? $grid['columns'][10]['search']['value'] : '',
                "pesquisaDiagnosticoPresuntivo" => !empty($grid['columns'][11]['search']['value']) ? $grid['columns'][11]['search']['value'] : '',
                "pesquisaAvaliacaoTumor" => !empty($grid['columns'][12]['search']['value']) ? $grid['columns'][12]['search']['value'] : '',
                "pesquisaEpidemiologia" => !empty($grid['columns'][13]['search']['value']) ? $grid['columns'][13]['search']['value'] : '',
                "pesquisaLessaoMacro" => !empty($grid['columns'][14]['search']['value']) ? $grid['columns'][14]['search']['value'] : '',
                "pesquisaLessaoHisto" => !empty($grid['columns'][15]['search']['value']) ? $grid['columns'][15]['search']['value'] : '',
                "pesquisaDiagnostico" => !empty($grid['columns'][16]['search']['value']) ? $grid['columns'][16]['search']['value'] : '',
                "pesquisaRelatorio" => !empty($grid['columns'][17]['search']['value']) ? $grid['columns'][17]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Atendimentos::SelectGrid($parametrosBusca);
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

    public static function controlar(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();
            //$diretorioImagens = $this->get('upload_directory');

            //INPUTUS DADOS FICHA ANIMAL
            $codigo = !empty($dadosForm['cdFichaLPV']) ? $dadosForm['cdFichaLPV'] : '';
            $data = !empty($dadosForm['dtFicha']) ? $dadosForm['dtFicha'] : '';
            $materialRecebido = !empty($dadosForm['dsMaterialRecebido']) ? $dadosForm['dsMaterialRecebido'] : '';
            $dsDiagnosticoPresuntivo = !empty($dadosForm['dsDiagnosticoPresuntivo']) ? $dadosForm['dsDiagnosticoPresuntivo'] : '';
            $flAvaliacaoTumoralComMargem = !empty($dadosForm['flAvaliacaoTumoralComMargem']) ? $dadosForm['flAvaliacaoTumoralComMargem'] : '';
            $dsEpidemiologia = !empty($dadosForm['dsEpidemiologiaHistoriaClinica']) ? $dadosForm['dsEpidemiologiaHistoriaClinica'] : '';
            $dsLesoesMacroscopicas = !empty($dadosForm['dsLesoesMacroscopicas']) ? $dadosForm['dsLesoesMacroscopicas'] : '';
            $dsLesoesHistologicas = !empty($dadosForm['dsLesoesHistologicas']) ? $dadosForm['dsLesoesHistologicas'] : '';
            $dsDiagnostico = !empty($dadosForm['dsDiagnostico']) ? $dadosForm['dsDiagnostico'] : '';
            $dsRelatorio = !empty($dadosForm['dsRelatorio']) ? $dadosForm['dsRelatorio'] : '';
            $cdCidadePropridade = !empty($dadosForm['select2cidadePropriedade']) ? $dadosForm['select2cidadePropriedade'] : '';
            $totalAnimais = !empty($dadosForm['totalAnimais']) ? $dadosForm['totalAnimais'] : 0;
            $animaisDoentes = !empty($dadosForm['qtdAnimaisDoentes']) ? $dadosForm['qtdAnimaisDoentes'] : 0;
            $animaisMortos = !empty($dadosForm['qtdAnimaisMortos']) ? $dadosForm['qtdAnimaisMortos'] : 0;

            // INPUTS DO ANIMAL
            $codigoAnimal = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';
            $alterouAnimal = !empty($dadosForm['alterouAnimal']) ? $dadosForm['alterouAnimal'] : '';
            $nomeAnimal = isset($dadosForm['animal']) ? $dadosForm['animal'] : '';
            $tipoAnimal = isset($dadosForm['select2tipoAnimal']) ? $dadosForm['select2tipoAnimal'] : '';
            $especieAnimal = isset($dadosForm['select2especieAnimal']) ? $dadosForm['select2especieAnimal'] : '';
            $racaAnimal = isset($dadosForm['select2racaAnimal']) ? $dadosForm['select2racaAnimal'] : '';
            $sexoAnimal = isset($dadosForm['dsSexo']) ? $dadosForm['dsSexo'] : '';
            $idadeAnimal = isset($dadosForm['idade']) ? $dadosForm['idade'] : '';
            $anoNascimentoAnimal = isset($dadosForm['anoNascimento']) ? $dadosForm['anoNascimento'] : '';

            // INPUTS DA PESSOA DONA DO ANIMAL
            $donoNaoDeclarado = isset($dadosForm['donoNaoDeclarado']) ? 'S' : 'N';
            $alterouDono = !empty($dadosForm['alterouDono']) ? $dadosForm['alterouDono'] : '';
            $nomeDono = isset($dadosForm['nmProprietario']) ? $dadosForm['nmProprietario'] : '';
            $nrTelefoneDono = isset($dadosForm['nrTelefoneProprietario']) ? $dadosForm['nrTelefoneProprietario'] : '';

            // INPUTS DA PESSOA VETERINÁRIA
            $codigoVeterinario = isset($dadosForm['cdVeterinarioRemetente']) ? $dadosForm['cdVeterinarioRemetente'] : '';
            $alterouVeterinario = !empty($dadosForm['alterouVeterinario']) ? $dadosForm['alterouVeterinario'] : '';
            $nomeVeterinario = isset($dadosForm['nmVeterinarioRemetente']) ? $dadosForm['nmVeterinarioRemetente'] : '';
            $crmvVeterinario = isset($dadosForm['crmvVeterinarioRemetente']) ? $dadosForm['crmvVeterinarioRemetente'] : '';
            $telefoneVeterinario = isset($dadosForm['nrTelVeterinarioRemetente']) ? $dadosForm['nrTelVeterinarioRemetente'] : '';
            $emailVeterinario = isset($dadosForm['dsEmailVeterinarioRemetente']) ? $dadosForm['dsEmailVeterinarioRemetente'] : '';
            $cdCidadeVeterinario = isset($dadosForm['select2cdCidadeVeterinario']) ? $dadosForm['select2cdCidadeVeterinario'] : '';

            if (empty($codigoAnimal)) throw new Exception("Houve um erro ao tentar processar a requisição <br><br> tente novamente mais tarde!");
            if (empty($codigoVeterinario)) throw new Exception("Selecione pelo menos um veterinário ao Atendimento para concluir o cadastro");

            $Conn = \App\Conn\Conn::getConn(true);

            $AnimalFicha = \App\Models\Animais::findById($codigoAnimal, $Conn);


            if ($alterouDono == 'S') {
                if ($donoNaoDeclarado == 'N') {
                    $Dono = \App\Models\Pessoas::findById($AnimalFicha->getDono1()->getCodigo(), $Conn);
                    $Dono->setNome($nomeDono);
                    $Dono->setTelefone($nrTelefoneDono);
                    $Dono->Update($Conn);

                    if (!$Dono->getResult()) {
                        throw new Exception($Dono->getMessage());
                    }
                }
            }

            if (($alterouDono == 'S' && $donoNaoDeclarado == 'S') || $alterouAnimal) {
                if ($alterouAnimal == 'S') {
                    $AnimalFicha->setNome($nomeAnimal);
                    // $AnimalFicha->setTipoAnimal($tipoAnimal);
                    $AnimalFicha->setEspecie($especieAnimal);
                    $AnimalFicha->setRaca($racaAnimal);
                    $AnimalFicha->setSexo($sexoAnimal);
                    // $AnimalFicha->setIdade($idadeAnimal);
                    // $AnimalFicha->setAnoNascimento($anoNascimentoAnimal);
                }

                if ($donoNaoDeclarado == 'S') {
                    $AnimalFicha->setDonoNaoDeclarado('S');
                    $AnimalFicha->setDono1('');
                }

                $AnimalFicha->Atualizar($Conn);

                if (!$AnimalFicha->getResult()) {
                    throw new Exception($AnimalFicha->getMessage());
                }
            }

            if (empty($codigoVeterinario)) {
                $VeterinarioFicha = new \App\Models\Pessoas($nomeVeterinario, $cdCidadeVeterinario, $telefoneVeterinario, '', $emailVeterinario, $crmvVeterinario, '', '', 'S', '', '');
                $VeterinarioFicha->Insert($Conn);

                if (!$VeterinarioFicha->getResult()) {
                    throw new Exception($VeterinarioFicha->getMessage());
                }
            } else {
                $VeterinarioFicha = \App\Models\Pessoas::findById($codigoVeterinario, $Conn);
                if ($alterouVeterinario == 'S') {
                    $VeterinarioFicha->setNome($nomeVeterinario);
                    $VeterinarioFicha->setCRMV($crmvVeterinario);
                    $VeterinarioFicha->setTelefone($telefoneVeterinario);
                    $VeterinarioFicha->setEmail($emailVeterinario);
                    $VeterinarioFicha->setCidade($cdCidadeVeterinario);

                    $VeterinarioFicha->Update();

                    if (!$VeterinarioFicha->getResult()) {
                        throw new Exception($VeterinarioFicha->getMessage());
                    }
                }
            }

            $Atendimento = new \App\Models\Atendimentos($data, $AnimalFicha->getCodigo(), $VeterinarioFicha->getCodigo(), $cdCidadePropridade, $totalAnimais, $animaisMortos, $animaisDoentes, $materialRecebido, $dsDiagnosticoPresuntivo, $flAvaliacaoTumoralComMargem, $dsEpidemiologia, $dsLesoesMacroscopicas, $dsLesoesHistologicas, $dsDiagnostico, $dsRelatorio, $codigo);
            if (empty($codigo)) {
                $Atendimento->Inserir($Conn);
            } else {
                $Atendimento->Atualizar($Conn);
            }

            if (!$Atendimento->getResult()) {
                throw new Exception($Atendimento->getMessage());
            }

            $Conn->commit();
            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $Atendimento->getCodigo()];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            if(isset($Conn)) $Conn->rollBack();
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function excluir(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            $codigo = !empty($dadosForm['cdFichaLPV']) ? $dadosForm['cdFichaLPV'] : '';

            if (empty($codigo)) {
                throw new Exception("Houve um erro ao processo a requisição<br>Tente novamente mais tarde");
            }

            $cad = \App\Models\Atendimentos::findById($codigo);
            if(empty($cad->getCodigo())) throw new Exception("Ops, parece que esse registro não existe mais na base de dados!");

            $Conn = \App\Conn\Conn::getConn(true);

            $imagensFicha = $cad->getImagesIds($Conn);

if (!empty($imagensFicha)) {
    $uploadDirectory = __DIR__ . '/../Assets/imagens/imagens_atendimento';
    
    foreach ($imagensFicha as $imagem) {
        $filePath = $uploadDirectory . DIRECTORY_SEPARATOR . $imagem;
        
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
            } else {
                throw new Exception("Erro ao excluir arquivo servidor ");
            }
        } else {
            throw new Exception("Arquivo não encontrado ");
        }
        
        // Exclua a entrada no banco de dados
        $result = \App\Models\Atendimentos::deleteImageById($imagem, $Conn);
        
        if (!$result) {
            throw new Exception("Erro ao excluir imagem banco");
        }
    }
}

            $cad->Excluir($Conn);


            if (!$cad->getResult()) {
                throw new Exception($cad->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => $imagensFicha];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function gerarCSVGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $datas = explode('|', !empty($grid['pesquisaDataAtendimento']) ? $grid['pesquisaDataAtendimento'] : '');
            $dataInicio = '';
            $dataFim = '';

            if (isset($datas[0]) && !empty($datas[0])) $dataInicio = $datas[0];

            if (isset($datas[1]) && !empty($datas[1])) $dataFim = $datas[1];

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['pesquisaCodigoAtendimento']) ? $grid['pesquisaCodigoAtendimento'] : '',
                "pesquisaDataInicio" => $dataInicio,
                "pesquisaDataFim" => $dataFim,
                "pesquisaNomeAnimal" => !empty($grid['pesquisaNomeAnimalAtendimento']) ? $grid['pesquisaNomeAnimalAtendimento'] : '',
                "pesquisaTipoAnimal" => !empty($grid['pesquisaNomeTipoAnimalAtendimento']) ? $grid['pesquisaNomeTipoAnimalAtendimento'] : '',
                "pesquisaEspecieAnimal" => !empty($grid['pesquisaEspecieAnimalAtendimento']) ? $grid['pesquisaEspecieAnimalAtendimento'] : '',
                "pesquisaRacaAnimal" => !empty($grid['pesquisaRacaAnimalAtendimento']) ? $grid['pesquisaRacaAnimalAtendimento'] : '',
                "pesquisaSexoAnimal" => !empty($grid['pesquisaSexoAnimalAtendimento']) ? $grid['pesquisaSexoAnimalAtendimento'] : '',
                "pesquisaTutor" => !empty($grid['pesquisaTutorAnimalAtendimento']) ? $grid['pesquisaTutorAnimalAtendimento'] : '',
                "pesquisaVeterinario" => !empty($grid['pesquisaVeterinarioAtendimento']) ? $grid['pesquisaVeterinarioAtendimento'] : '',
                "pesquisaMunicipio" => !empty($grid['pesquisaMunicipioOrigemAtendimento']) ? $grid['pesquisaMunicipioOrigemAtendimento'] : '',
                "pesquisaMaterial" => !empty($grid['pesquisaMaterialAtendimento']) ? $grid['pesquisaMaterialAtendimento'] : '',
                "pesquisaDiagnosticoPresuntivo" => !empty($grid['pesquisaDiagnosticoPresuntivoAtendimento']) ? $grid['pesquisaDiagnosticoPresuntivoAtendimento'] : '',
                "pesquisaAvaliacaoTumor" => !empty($grid['pesquisaAvalicaoTumoralAtendimento']) ? $grid['pesquisaAvalicaoTumoralAtendimento'] : '',
                "pesquisaEpidemiologia" => !empty($grid['pesquisaEpidemiologiaAtendimento']) ? $grid['pesquisaEpidemiologiaAtendimento'] : '',
                "pesquisaLessaoMacro" => !empty($grid['pesquisaLesoesMacrocospiasAtendimento']) ? $grid['pesquisaLesoesMacrocospiasAtendimento'] : '',
                "pesquisaLessaoHisto" => !empty($grid['pesquisaLesoesHistologicasAtendimento']) ? $grid['pesquisaLesoesHistologicasAtendimento'] : '',
                "pesquisaDiagnostico" => !empty($grid['pesquisaDiagnosticoAtendimento']) ? $grid['pesquisaDiagnosticoAtendimento'] : '',
                "pesquisaRelatorio" => !empty($grid['pesquisaRelatorioAtendimento']) ? $grid['pesquisaRelatorioAtendimento'] : '',
                "inicio" => '',
                "limit" => '',
                "orderBy" =>  '',
                "orderAscDesc" => ''
            ];

            $dadosSelect = \App\Models\Atendimentos::SelectGrid($parametrosBusca);
            if($dadosSelect == []) throw new Exception("Não é possível Exportar para CSV quando a pesquisa apresenta nenhum resultado");

            $response = $response
                ->withHeader('Content-Type', 'text/csv; charset=UTF-8')
                ->withHeader('Content-Disposition', 'attachment; filename="atendimentos.csv"')
                ->withHeader('Cache-Control', 'max-age=0');

            $arquivo = fopen('php://output', 'w');

            // Escrever o cabeçalho com BOM para UTF-8
            fputs($arquivo, "\xEF\xBB\xBF");

            $cabecalho = ['Código', 'Data', 'Nome do Animal', 'Tipo de Animal', 'Espécie', 'Raça', 'Sexo', 'Dono', 'Veterinário', 'Município Origem', 'Material Recebido', 'Diagnóstico Presuntivo', 'Avaliação Tumoral Margem', 'Epidemiologia e História Clínica', 'Lesões Macroscópias', 'Lesões Histológicas', 'Diagnóstico', 'Relatório'];
            fputcsv($arquivo, $cabecalho, ';');

            foreach ($dadosSelect as $dado) {
                // Convertendo cada campo individualmente para UTF-8
                $linha = array_map(function ($campo) {
                    return mb_convert_encoding($campo, 'UTF-8', 'auto');
                }, $dado);

                fputcsv($arquivo, $linha, ';');
            }

            fclose($arquivo);

            return $response;


            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
            $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
            return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
        }
    }

    public static function uploadGaleria(Request $request, Response $response) {
        try {
            $dadosForm = $request->getParsedBody();
            // Captura os arquivos enviados
            $arquivos = $request->getUploadedFiles();
            $imagem = isset($arquivos['imageAtendimento']) ? $arquivos['imageAtendimento'] : null;
            $cdAtendimento = !empty($dadosForm['cdAtendimento']) ? $dadosForm['cdAtendimento'] : '';

            if (!$imagem || $imagem->getError() !== UPLOAD_ERR_OK || empty($cdAtendimento)) {
                throw new \Exception("Houve um erro ao processar a requisição. Tente novamente mais tarde.");
            }

            // Diretório de upload
            $uploadDirectory = __DIR__ . '/../Assets/imagens/imagens_atendimento';

            // Gera um nome de arquivo único
            $filename = self::moveUploadedFile($uploadDirectory, $imagem);

            if(!file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename)) throw new Exception("Erro ao fazer upload da imagem");

            $Atendimento = \App\Models\Atendimentos::findById($cdAtendimento);
            $Atendimento->setImagem($filename);
            $Atendimento->InserirImagem();

            if(!$Atendimento->getResult()){
                throw new Exception($Atendimento->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            // Resposta de erro
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }

        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    private static function moveUploadedFile($directory, $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // Gera um nome de arquivo único
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public static function excluirGaleria(Request $request, Response $response) {
        try {
            $dadosForm = $request->getParsedBody();
            $filename = !empty($dadosForm['idImagem']) ? $dadosForm['idImagem'] : '';

            if ( empty($filename)) {
                throw new \Exception("Houve um erro ao processar a requisição. Tente novamente mais tarde.");
            }

            // Diretório de upload
            $uploadDirectory = __DIR__ . '/../Assets/imagens/imagens_atendimento';

            if(!file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename)) throw new Exception("Erro ao fazer exclusão da imagem");

            unlink($uploadDirectory . DIRECTORY_SEPARATOR . $filename);

            $Imagem = \App\Models\Atendimentos::deleteImageById($filename);

            if(!$filename){
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

