<?php

include("vendor/autoload.php");

$db = Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();