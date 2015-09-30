<?php

include("../config.php");

$params = [
    'index' => 'solicitacoes',
    'type' => 'solicitacao',
    'size' => 0,
    'body' => [

		"aggs"=> [
			"group_by_subprefeitura"=> [
				"terms"=> [
					"field"=> "subprefeitura"
				]
			]
		],

    ]
];

$response = $db->search($params);
print '<meta charset="utf-8"><pre>';
print_r($response);