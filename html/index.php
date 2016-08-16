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

if (isset($_GET['inicio']) && count(explode("/", $_GET['inicio']))==3):
    $inicio = strtotime($_GET['inicio'].' 00:00:00');
else:
    $inicio = strtotime($data_minima['hits']['hits'][0]['_source']['timestamp']);
endif;

if (isset($_GET['fim']) && count(explode("/", $_GET['fim']))==3):
    $fim = strtotime($_GET['fim'].' 00:00:00');
else:
    $fim = time();
endif;

$agregacoes = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'size' => 1,
    'body' => [
    	"sort"=> [
    		[ "timestamp"=>"desc" ]
    	],
		"aggs"=> [

            "intervalo"=> [
                "date_range"=> [
                    "field"=> "timestamp",
                    "format"=> "MM-yyy",
                    "ranges"=> [
                        [ "from"=> $inicio*1000, "to"=> $fim*1000 ] 
                    ]
                ],
                "aggs"=>[
                    "subprefeitura"=>["terms"=>["field"=>"subprefeitura.raw","size"=>10]],
                    "canal"=>["terms"=>["field"=>"canal.raw","size"=>10]],
                    "assunto"=>["terms"=>["field"=>"assunto.raw","size"=>10]],
                    "orgao"=>["terms"=>["field"=>"orgao.raw","size"=>10]]
                ]
            ]

		],

    ]
]);


include("template/home.phtml");