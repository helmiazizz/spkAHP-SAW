<?php
require "include/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get criteria IDs
    $sql_crit = 'SELECT id_criteria FROM saw_criterias ORDER BY id_criteria';
    $result_crit = $db->query($sql_crit);
    $criteriaIds = array();
    while ($row_c = $result_crit->fetch_object()) {
        $criteriaIds[] = $row_c->id_criteria;
    }
    $n = count($criteriaIds);

    // Delete all existing comparisons
    $db->query('DELETE FROM ahp_comparisons');

    // Insert new comparison values
    $ahp = $_POST['ahp'];

    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $idI = $criteriaIds[$i];
            $idJ = $criteriaIds[$j];

            if ($idI == $idJ) {
                // Diagonal = 1
                $val = 1;
                $stmt = $db->prepare("INSERT INTO ahp_comparisons (id_criteria_row, id_criteria_col, value) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $idI, $idJ, $val);
                $stmt->execute();
                $stmt->close();
            } elseif ($idI < $idJ) {
                // Upper triangle - from form
                $val = floatval($ahp[$idI][$idJ]);
                $stmt = $db->prepare("INSERT INTO ahp_comparisons (id_criteria_row, id_criteria_col, value) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $idI, $idJ, $val);
                $stmt->execute();
                $stmt->close();

                // Lower triangle - reciprocal
                $reciprocal = 1 / $val;
                $stmt = $db->prepare("INSERT INTO ahp_comparisons (id_criteria_row, id_criteria_col, value) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $idJ, $idI, $reciprocal);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // ========== Calculate AHP Weights ==========
    // 1. Build matrix from DB
    $sql_vals = 'SELECT id_criteria_row, id_criteria_col, value FROM ahp_comparisons';
    $result_vals = $db->query($sql_vals);
    $matrixVals = array();
    while ($rv = $result_vals->fetch_object()) {
        $matrixVals[$rv->id_criteria_row][$rv->id_criteria_col] = floatval($rv->value);
    }

    // Build numeric matrix
    $matrix = array();
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $idI = $criteriaIds[$i];
            $idJ = $criteriaIds[$j];
            $matrix[$i][$j] = isset($matrixVals[$idI][$idJ]) ? $matrixVals[$idI][$idJ] : 1;
        }
    }

    // 2. Column sums
    $colSum = array();
    for ($j = 0; $j < $n; $j++) {
        $colSum[$j] = 0;
        for ($i = 0; $i < $n; $i++) {
            $colSum[$j] += $matrix[$i][$j];
        }
    }

    // 3. Normalized matrix
    $normalized = array();
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $normalized[$i][$j] = $matrix[$i][$j] / $colSum[$j];
        }
    }

    // 4. Priority weights (row averages)
    $weights = array();
    for ($i = 0; $i < $n; $i++) {
        $rowSum = 0;
        for ($j = 0; $j < $n; $j++) {
            $rowSum += $normalized[$i][$j];
        }
        $weights[$i] = $rowSum / $n;
    }

    // 5. Update saw_criterias with new weights
    for ($i = 0; $i < $n; $i++) {
        $idCrit = $criteriaIds[$i];
        $w = round($weights[$i], 4);
        $stmt = $db->prepare("UPDATE saw_criterias SET weight = ? WHERE id_criteria = ?");
        $stmt->bind_param("di", $w, $idCrit);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to AHP page
    header('location:ahp.php');
    exit;
}
