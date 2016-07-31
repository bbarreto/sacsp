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
			"subprefeitura"=>["terms"=>["field"=>"subprefeitura.raw","size"=>10]],
			"canal"=>["terms"=>["field"=>"canal.raw","size"=>10]],
			"assunto"=>["terms"=>["field"=>"assunto.raw","size"=>10]],
			"orgao"=>["terms"=>["field"=>"orgao.raw","size"=>10]]
		],

    ]
]);

include("template/home.phtml");