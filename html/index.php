<?php

include("../config.php");

$agregacoes = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'size' => 0,
    'body' => [
		"aggs"=> [
			"subprefeitura"=>["terms"=>["field"=>"subprefeitura"]],
			"canal"=>["terms"=>["field"=>"canal"]],
			"assunto"=>["terms"=>["field"=>"assunto"]],
			"orgao"=>["terms"=>["field"=>"orgao"]]
		],

    ]
]);

include("template/home.phtml");