<?php

include("../config.php");

$query = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'search_type' => 'count',
    'body' => [
        "query" => [
        "term" => [ "assunto" => $_GET['assunto'] ],
        ],
    	"sort"=> [
    		[ "timestamp"=>"asc" ]
    	],
		"aggs"=> [
            "graphDays" => [
    			"date_histogram" => [
                    "field" => "timestamp",
                    "interval" => "hour"
                ]
            ]
		],

    ]
]);

include("template/detalhe.phtml");