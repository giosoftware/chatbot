<?php
$json = '{
    "sys_input_total_semantic_weight": {
        "value": "1.05"
    },
    "sys_unknown_words_percentage": {
        "value": "0"
    },
    "sys_unanswered_consecutive": {
        "value": "2"
    },
    "sys_input_total_semantic_weight_range": {
        "value": "low"
    },
    "sys_max_result_score": {
        "value": "low"
    }
}';
$result = json_decode ($json);
print_r($result);
echo $result->sys_unanswered_consecutive->value;

