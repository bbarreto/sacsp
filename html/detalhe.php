<?php

include("../config.php");

$query = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'search_type' => 'count',
    'body' => [
        "query" => [
        "term" => $_GET,
        ],
    	"sort"=> [
    		[ "timestamp"=>"asc" ]
    	],
		"aggs"=> [
            "graphDays" => [
    			"date_histogram" => [
                    "field" => "timestamp",
                    "interval" => "day",
                    "format" => "dd/MM/YYYY"
                ]
            ]
		],

    ]
]);

$keys = [];
$values = [];
foreach(array_reverse($query['aggregations']['graphDays']['buckets']) as $item):
    $keys[] = '"'.$item['key_as_string'].'"';
    $values[] = $item['doc_count'];
endforeach;

include("template/detalhe.phtml");