<?php
// Get criteria info
$sql_crit = 'SELECT id_criteria, attribute FROM saw_criterias ORDER BY id_criteria';
$result_crit = $db->query($sql_crit);
$criterias_r = array();
while ($row_c = $result_crit->fetch_object()) {
    $criterias_r[] = $row_c;
}

// Build dynamic query for values
$select_parts = array();
foreach ($criterias_r as $c) {
    $id = $c->id_criteria;
    $select_parts[] = "SUM(IF(a.id_criteria={$id},a.value,0)) AS C{$id}";
}
$select_str = implode(',', $select_parts);

$sql = "SELECT a.id_alternative, b.name, {$select_str}
        FROM saw_evaluations a
        JOIN saw_alternatives b USING(id_alternative)
        GROUP BY a.id_alternative
        ORDER BY a.id_alternative";
$result = $db->query($sql);

$X = array();
foreach ($criterias_r as $c) {
    $X[$c->id_criteria] = array();
}

while ($row = $result->fetch_object()) {
    foreach ($criterias_r as $c) {
        $col = 'C' . $c->id_criteria;
        array_push($X[$c->id_criteria], round($row->$col, 2));
    }
}
$result->free();

// Build normalization query
$norm_parts = array();
foreach ($criterias_r as $c) {
    $id = $c->id_criteria;
    $maxVal = max($X[$id]);
    $minVal = min($X[$id]);
    if ($maxVal == 0) $maxVal = 1; // prevent division by zero
    if ($minVal == 0) $minVal = 1; // prevent division by zero
    $norm_parts[] = "SUM(IF(a.id_criteria={$id},IF(b.attribute='benefit',a.value/{$maxVal},{$minVal}/a.value),0)) AS C{$id}";
}
$norm_str = implode(',', $norm_parts);

$sql = "SELECT a.id_alternative, {$norm_str}
        FROM saw_evaluations a
        JOIN saw_criterias b USING(id_criteria)
        GROUP BY a.id_alternative
        ORDER BY a.id_alternative";
$result = $db->query($sql);
$R = array();
while ($row = $result->fetch_object()) {
    $r_row = array();
    foreach ($criterias_r as $c) {
        $col = 'C' . $c->id_criteria;
        $r_row[] = round($row->$col, 4);
    }
    $R[$row->id_alternative] = $r_row;
}
