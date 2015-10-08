<?php

include("../config.php");

$data_minima = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'size' => 1,
    'body' => [
    	"sort"=> [
    		[ "timestamp"=>"asc" ]
    	]
    ]
]);

$agregacoes = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'size' => 1,
    'body' => [
    	"sort"=> [
    		[ "timestamp"=>"desc" ]
    	],
		"aggs"=> [
			"subprefeitura"=>["terms"=>["field"=>"subprefeitura"]],
			"canal"=>["terms"=>["field"=>"canal"]],
			"assunto"=>["terms"=>["field"=>"assunto"]],
			"orgao"=>["terms"=>["field"=>"orgao"]]
		],

    ]
]);

include("template/home.phtml");