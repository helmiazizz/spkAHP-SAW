<!DOCTYPE html>
<html lang="en">
  <?php
require "layout/head.php";
require "include/conn.php";
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
          <h3>Bobot &amp; Kriteria</h3>
        </div>
        <div class="page-content">
          <section class="row">
            <div class="col-12">
              <div class="card">

                <div class="card-header">
                  <h4 class="card-title">Tabel Bobot Kriteria (AHP)</h4>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <p class="card-text">
                      Bobot kriteria dihitung menggunakan metode AHP (Analytic Hierarchy Process).
                      Untuk mengubah bobot, silakan ubah matriks perbandingan berpasangan di halaman
                      <a href="ahp.php">AHP</a>.
                    </p>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-striped mb-0">
                    <caption>
                        Tabel Kriteria C<sub>i</sub> dengan Bobot AHP
                    </caption>
                    <tr>
                      <th>No</th>
                      <th>Simbol</th>
                      <th>Kriteria</th>
                      <th>Bobot (Desimal)</th>
                      <th>Bobot (%)</th>
                      <th>Atribut</th>
                    </tr>
                    <?php
$sql = 'SELECT id_criteria,criteria,weight,attribute FROM saw_criterias ORDER BY id_criteria';
$result = $db->query($sql);
$i = 0;
while ($row = $result->fetch_object()) {
    $weightDecimal = number_format($row->weight, 4);
    $weightPercent = number_format($row->weight * 100, 2) . '%';
    echo "<tr>
        <td class='right'>" . (++$i) . "</td>
        <td class='center'>C{$i}</td>
        <td>{$row->criteria}</td>
        <td>{$weightDecimal}</td>
        <td>{$weightPercent}</td>
        <td>{$row->attribute}</td>
      </tr>\n";
}
$result->free();
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
    <?php require "layout/js.php";?>
  </body>

</html>