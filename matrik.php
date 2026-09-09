<!DOCTYPE html>
<html lang="en">
  <?php
require "layout/head.php";
require "include/conn.php";

// Get criteria dynamically
$sql_crit = 'SELECT id_criteria, criteria, attribute FROM saw_criterias ORDER BY id_criteria';
$result_crit = $db->query($sql_crit);
$criterias = array();
while ($row_c = $result_crit->fetch_object()) {
    $criterias[] = $row_c;
}
$n_crit = count($criterias);
?>

  <body>
    <div id="app">
      <?php require "layout/sidebar.php";?>
      <div id="main">
        <header class="mb-3">
          <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
          </a>
        </header>
        <div class="page-heading">
          <h3>Matrik</h3>
        </div>
        <div class="page-content">
          <section class="row">
            <div class="col-12">
              <div class="card">

                <div class="card-header">
                  <h4 class="card-title">Matriks Keputusan (X) &amp; Ternormalisasi (R)</h4>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <p class="card-text">Melakukan perhitungan normalisasi untuk mendapatkan matriks nilai ternormalisasi (R), dengan ketentuan :
Untuk normalisai nilai, jika faktor/attribute kriteria bertipe cost maka digunakan rumusan:
Rij = ( min{Xij} / Xij)
sedangkan jika faktor/attribute kriteria bertipe benefit maka digunakan rumusan:
Rij = ( Xij/max{Xij} )</p>
                  </div>
                  <button type="button" class="btn btn-outline-success btn-sm m-2" data-bs-toggle="modal"
                                        data-bs-target="#inlineForm">
                                        Isi Nilai Alternatif
                                    </button>
                  <div class="table-responsive">
                  <table class="table table-striped mb-0">
    <caption>
        Matrik Keputusan(X)
    </caption>
    <tr>
        <th rowspan='2'>Alternatif</th>
        <th colspan='<?= $n_crit + 1 ?>'>Kriteria</th>
    </tr>
    <tr>
        <?php foreach ($criterias as $idx => $c): ?>
        <?php if ($idx == $n_crit - 1): ?>
        <th colspan="2">C<?= $c->id_criteria ?></th>
        <?php else: ?>
        <th>C<?= $c->id_criteria ?></th>
        <?php endif; ?>
        <?php endforeach; ?>
    </tr>
    <?php
// Build dynamic SELECT
$select_parts = array();
foreach ($criterias as $c) {
    $id = $c->id_criteria;
    $select_parts[] = "SUM(IF(a.id_criteria={$id},a.value,0)) AS C{$id}";
}
$select_str = implode(',', $select_parts);

$sql = "SELECT
          a.id_alternative,
          b.name,
          {$select_str}
        FROM
          saw_evaluations a
          JOIN saw_alternatives b USING(id_alternative)
        GROUP BY a.id_alternative
        ORDER BY a.id_alternative";
$result = $db->query($sql);
$X = array();
foreach ($criterias as $c) {
    $X[$c->id_criteria] = array();
}

while ($row = $result->fetch_object()) {
    foreach ($criterias as $c) {
        $col = 'C' . $c->id_criteria;
        array_push($X[$c->id_criteria], round($row->$col, 2));
    }
    echo "<tr class='center'>
            <th>A<sub>{$row->id_alternative}</sub> {$row->name}</th>";
    foreach ($criterias as $c) {
        $col = 'C' . $c->id_criteria;
        echo "<td>" . round($row->$col, 2) . "</td>";
    }
    echo "<td>
            <a href='keputusan-hapus.php?id={$row->id_alternative}' class='btn btn-danger btn-sm'>Hapus</a>
            </td>
          </tr>\n";
}
$result->free();

?>
</table>

<table class="table table-striped mb-0">
    <caption>
        Matrik Ternormalisasi (R)
    </caption>
    <tr>
        <th rowspan='2'>Alternatif</th>
        <th colspan='<?= $n_crit ?>'>Kriteria</th>
    </tr>
    <tr>
        <?php foreach ($criterias as $c): ?>
        <th>C<?= $c->id_criteria ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
// Build normalization query dynamically
$norm_parts = array();
foreach ($criterias as $c) {
    $id = $c->id_criteria;
    if (!empty($X[$id])) {
        $maxVal = max($X[$id]);
        $minVal = min($X[$id]);
        if ($maxVal == 0) $maxVal = 1;
        if ($minVal == 0) $minVal = 1;
        $norm_parts[] = "SUM(IF(a.id_criteria={$id},IF(b.attribute='benefit',a.value/{$maxVal},{$minVal}/a.value),0)) AS C{$id}";
    }
}
$norm_str = implode(',', $norm_parts);

if (!empty($norm_str)) {
    $sql = "SELECT
              a.id_alternative,
              {$norm_str}
            FROM
              saw_evaluations a
              JOIN saw_criterias b USING(id_criteria)
            GROUP BY a.id_alternative
            ORDER BY a.id_alternative";
    $result = $db->query($sql);
    $R = array();
    while ($row = $result->fetch_object()) {
        $R[$row->id_alternative] = array();
        echo "<tr class='center'>
                <th>A{$row->id_alternative}</th>";
        foreach ($criterias as $c) {
            $col = 'C' . $c->id_criteria;
            $R[$row->id_alternative][] = round($row->$col, 4);
            echo "<td>" . round($row->$col, 4) . "</td>";
        }
        echo "</tr>\n";
    }
}
?>
</table>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
        <?php require "layout/footer.php";?>
      </div>
    </div>

    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Isi Nilai Kandidat </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <form action="matrik-simpan.php" method="POST">
                        <div class="modal-body">
                            <label>Name: </label>
                            <div class="form-group">
                            <select class="form-control form-select" name="id_alternative">
                            <?php
$sql = 'SELECT id_alternative,name FROM saw_alternatives';
$result = $db->query($sql);
while ($row = $result->fetch_object()) {
    echo '<option value="' . $row->id_alternative . '">' . $row->name . '</option>';
}
$result->free();
?>
                                          </select>
                            </div>
                        </div>
                        <div class="modal-body">
                            <label>Criteria: </label>
                            <div class="form-group">
                            <select class="form-control form-select" name="id_criteria">
                            <?php
$sql = 'SELECT * FROM saw_criterias ORDER BY id_criteria';
$result = $db->query($sql);
while ($row = $result->fetch_object()) {
    echo '<option value="' . $row->id_criteria . '">' . $row->criteria . '</option>';
}
$result->free();
?>
                                          </select>
                            </div>
                        </div>
                        <div class="modal-body">
                            <label>Value: </label>
                            <div class="form-group">
                                <input type="text" name="value" placeholder="value..." class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" name="submit" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php require "layout/js.php";?>
  </body>

</html>