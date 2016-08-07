<?php

include '../config.php';

$query = $db->search([
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'search_type' => 'count',
    'body' => [
        "query" => [
        "term" => [
                'assunto.raw'=>$_GET['assunto']
            ]
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
            ],
            "graphSubprefeitura" => [
                "terms" => [
                    "field" => "subprefeitura.raw",
                    "size" => 50
                ]
            ]
		],

    ]
]);

$graficos = [];
foreach($query['aggregations'] as $grafico=>$el):
    $graficos[$grafico]['keys'] = [];
    $graficos[$grafico]['values'] = [];
    foreach($el['buckets'] as $item):
        if (isset($item['key_as_string'])):
            $graficos[$grafico]['keys'][] = '"'.$item['key_as_string'].'"';
        else:
            $graficos[$grafico]['keys'][] = '"'.$item['key'].'"';
        endif;
        $graficos[$grafico]['values'][] = $item['doc_count'];
    endforeach;
endforeach;

include 'template/detalhe.phtml';