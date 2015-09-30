<?php

include("config.php");

$solicitacao_id = 12814652;

while ($solicitacao_id>0):

	//consulta impressao
	$impressao = call('http://sac.prefeitura.sp.gov.br/Consulta_Numero.asp', 'GET', [
		'txtSolNum'=>$solicitacao_id
	]);

	$impressao = utf8_encode($impressao);
	$impressao = html_entity_decode($impressao);

	$impressao = strip_tags($impressao, '<p><a><b>');
	$impressao = preg_replace('/\s{2,}/', ' ', $impressao);
	$impressao = preg_replace('/\s{2,}/', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);
	$impressao = preg_replace('/\p{Z}{2,}|\p{Z}{2,}/u', ' ', $impressao);

	preg_match_all('@<b>(.*?)</b>@', $impressao, $chaves);
	preg_match_all('@</b>(.*?)<b>@', $impressao, $valores);
	preg_match_all('@<p align="left">(.*?)</p>@', $impressao, $providencias);

	array_walk($chaves[1], function ($item, $key) {
		$chaves[1][$key] = trim($item);
	});

	array_walk($valores[1], function ($item, $key) {
		$valores[1][$key] = trim($item);
	});

	$depara = [
		'Nº do SAC:'=>'id',
		'Data de Cadastro no SAC:'=>'momento',
		'Canal de Entrada:'=>'canal',
		'Endereço:'=>'endereco',
		'Ref.:'=>'referencia',
		'Bairro:'=>'bairro',
		'CEP:'=>'cep',
		'Pag. Guia:'=>'pag_guia',
		'Setor e Quadra:'=>'setor_quadra',
		'Subprefeitura:'=>'subprefeitura',
		'Assunto:'=>'assunto',
		'Especificação:'=>'especificacao',
		'Orgão Responsável:'=>'orgao',
		'Situação da Solicitação:'=>'situacao',
		'Data da Conclusão:'=>'conclusao'
	];

	$resultado = [];
	foreach($chaves[1] as $key=>$name):
		if (isset($valores[1][$key]) && trim($valores[1][$key])!=""):

			if (isset($depara[$name])):
				$name = $depara[$name];
			endif;

			$valor = trim($valores[1][$key]);

			if ($name=='momento' || $name=='conclusao'):
				$valor = explode(" ", $valor);
				if (count($valor)>1):
					$valor = implode("-", array_reverse(explode("/", $valor[0])))." ".$valor[1].":00";
				else:
					$valor = implode("-", array_reverse(explode("/", $valor[0])));
				endif;
				$resultado[$name] = strtotime($valor);
			elseif ($name=='orgao'):
				$valor = explode(" - ", $valor);
				$resultado['supervisao'] = $valor[count($valor)-1];
				unset($valor[count($valor)-1]);
				$resultado['orgao'] = implode(" - ", $valor);
			else:
				$resultado[$name] = $valor;
			endif;

			
		endif;
	endforeach;

	if (!isset($resultado['id'])):
		$solicitacao_id++;
		continue;
	endif;

	if (count($providencias)>1):
		$resultado['providencias'] = trim($providencias[1][0]);
	endif;

	$solicitacao = [
		"_id"=>isset($resultado['id'])?$resultado['id']:null, 
		"_timestamp"=>isset($resultado['momento'])?date("Y-m-d\TH:i:s", $resultado['momento']):null, 
		"canal"=>isset($resultado['canal'])?$resultado['canal']:null, 
		"endereco"=>isset($resultado['endereco'])?$resultado['endereco']:null, 
		"referencia"=>isset($resultado['referencia'])?$resultado['referencia']:null, 
		"bairro"=>isset($resultado['bairro'])?$resultado['bairro']:null, 
		"cep"=>isset($resultado['cep'])?$resultado['cep']:null, 
		"pag_guia"=>isset($resultado['pag_guia'])?$resultado['pag_guia']:null, 
		"setor_quadra"=>isset($resultado['setor_quadra'])?$resultado['setor_quadra']:null, 
		"subprefeitura"=>isset($resultado['subprefeitura'])?$resultado['subprefeitura']:null, 
		"assunto"=>isset($resultado['assunto'])?$resultado['assunto']:null, 
		"especificacao"=>isset($resultado['especificacao'])?$resultado['especificacao']:null, 
		"supervisao"=>isset($resultado['supervisao'])?$resultado['supervisao']:null, 
		"orgao"=>isset($resultado['orgao'])?$resultado['orgao']:null, 
		"situacao"=>isset($resultado['situacao'])?$resultado['situacao']:null, 
		"conclusao"=>isset($resultado['conclusao'])?date("Y-m-d\TH:i:s", $resultado['conclusao']):null, 
		"providencias"=>isset($resultado['providencias'])?$resultado['providencias']:null
	];

	$response = $db->index([
		'index'=>'solicitacoes',
		'type'=>'solicitacao',
		'id'=>$resultado['id'],
		'body'=>$solicitacao
	]);

	print_r($response);

	$solicitacao_id++;

endwhile;


function call($url, $method='GET', $args=null, $headers=array(), $data=null) {

	/** Formata os cabeçalhos */
	$strHeaders = "";
	foreach($headers as $k=>$v):
		$strHeaders .= $k.": ".$v."\r\n";
	endforeach;

	/** Cria o array para montagem do contexto */
	$opts = array(
	  'http'=>array(
	    'method'=>strtoupper($method),
	    'header'=>$strHeaders,
	    'timeout'=>(float) 5.0
	  ),
	  "ssl"=>array(
	  	'ciphers'=>'RC4-SHA'
	  )
	);

	if ($data!==null) {
		$opts['http']['content'] = $data;
	}

	/** Formata os argumentos para POST ou GET */
	if ($args!==null):
		if (strtolower($method)=="post"):
			if (is_array($args)):
				$opts['http']['content'] = http_build_query($args);
			else:
				$opts['http']['content'] = $args;
			endif;
		else:
			if (is_array($args)):
				$url .= "?".http_build_query($args);
			else:
				$url .= "?".$args;
			endif;
		endif;
	endif;

	/** Cria o contexto para a requisição */
	$context = stream_context_create($opts);

	/** Faz a chamada da URL com o contexto */
	try {
		$file = file_get_contents($url, false, $context);
		return $file;
	} catch (Exception $e) {
		/** Se não executou, retorna erro */
		return false;
	}

}