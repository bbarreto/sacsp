<?php

include("vendor/autoload.php");

$db = Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

$subprefeituras_keys = [
	"AD"=>"Subprefeitura de Cidade Ademar",
	"AF"=>"Subprefeitura de Aricanduva",
	"BT"=>"Subprefeitura do Butantã",
	"CL"=>"Subprefeitura do Campo Limpo",
	"CS"=>"Subprefeitura da Capela do Socorro",
	"CT"=>"Subprefeitura de Cidade Tiradentes",
	"CV"=>"Subprefeitura da Casa Verde",
	"EM"=>"Subprefeitura de Ermelino Matarazzo",
	"FO"=>"Subprefeitura da Freguesia/Brasilândia",
	"GU"=>"Subprefeitura de Guaianases",
	"IP"=>"Subprefeitura do Ipiranga",
	"IQ"=>"Subprefeitura de Itaquera",
	"IT"=>"Subprefeitura do Itaim Paulista",
	"JA"=>"Subprefeitura do Jabaquara",
	"JT"=>"Subprefeitura do Jaçanã/Tremembé",
	"LA"=>"Subprefeitura da Lapa",
	"MB"=>"Subprefeitura de M´Boi Mirim",
	"MG"=>"Subprefeitura da Vila Maria/Vila Guilherme",
	"MO"=>"Subprefeitura da Mooca",
	"MP"=>"Subprefeitura de São Miguel Paulista",
	"NL"=>"Não Localizada",
	"PA"=>"Subprefeitura de Parelheiros",
	"PE"=>"Subprefeitura da Penha",
	"PI"=>"Subprefeitura de Pinheiros",
	"PJ"=>"Subprefeitura de Pirituba/Jaraguá",
	"PR"=>"Subprefeitura de Perus",
	"SA"=>"Subprefeitura de Santo Amaro",
	"SB"=>"Subprefeitura de Sapopemba",
	"SE"=>"Subprefeitura da Sé",
	"SM"=>"Subprefeitura de São Mateus",
	"ST"=>"Subprefeitura de Santana/Tucuruvi",
	"VM"=>"Subprefeitura da Vila Mariana",
	"VP"=>"Subprefeitura da Vila Prudente"
];