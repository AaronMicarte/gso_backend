<?php
$str = '[{"item_index":0,"receiver_name":"Aaron Micarte","position_office":"Programmer","quantity":2}]';
$sanitized = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
echo "Sanitized:\n" . $sanitized . "\n";
echo "Decode original:\n";
var_dump(json_decode($str, true));
echo "Decode sanitized:\n";
var_dump(json_decode($sanitized, true));
echo "Decode html_entity_decode:\n";
var_dump(json_decode(html_entity_decode($sanitized, ENT_QUOTES, 'UTF-8'), true));
