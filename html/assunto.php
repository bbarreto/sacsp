<?php

include '../config.php';

if (!isset($_GET['assunto'])):
    header("HTTP/1.0 404 Not Found");die;
endif;

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

if (isset($_GET['inicio']) && count(explode("-", $_GET['inicio']))==3):
    $inicio = strtotime($_GET['inicio'].' 00:00:00');
else:
    $inicio = strtotime($data_minima['hits']['hits'][0]['_source']['timestamp']);
endif;

if (isset($_GET['fim']) && count(explode("-", $_GET['fim']))==3):
    $fim = strtotime($_GET['fim'].' 00:00:00');
else:
    $fim = time();
endif;

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

            "intervalo"=> [
                "date_range"=> [
                    "field"=> "timestamp",
                    "format"=> "MM-yyy",
                    "ranges"=> [
                        [ "from"=> $inicio*1000, "to"=> ($fim+24*60*60)*1000 ] 
                    ]
                ],
                "aggs"=>[
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

		],

    ]
]);


$graficos = [];

foreach(['graphDays', 'graphSubprefeitura'] as $grafico):

    $graficos[$grafico]['keys'] = [];
    $graficos[$grafico]['values'] = [];
    foreach($query['aggregations']['intervalo']['buckets'][0][$grafico]['buckets'] as $item):
        if (isset($item['key_as_string'])):
            $graficos[$grafico]['keys'][] = '"'.$item['key_as_string'].'"';
        else:
            $graficos[$grafico]['keys'][] = '"'.$item['key'].'"';
        endif;
        $graficos[$grafico]['values'][] = $item['doc_count'];
    endforeach;
endforeach;

if (isset($_GET['output']) && $_GET['output']=='kml'):
    $max = max($graficos['graphSubprefeitura']['values']);
    include 'template/assunto_kml.phtml';
else:
    include 'template/assunto.phtml';
endif;