<!DOCTYPE html>
<html lang="en">
  <?php
  require "layout/head.php";
  require "include/conn.php";
  require "W.php"; // Memuat vektor bobot W
  require "R.php"; // Memuat matriks ternormalisasi R
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
          <h3>Nilai Preferensi &amp; Ranking</h3>
        </div>
        <div class="page-content">
          <section class="row">
            <div class="col-12">
              <div class="card">

                <div class="card-header">
                  <h4 class="card-title">Tabel Nilai Preferensi &amp; Ranking</h4>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <p class="card-text">
                      Nilai preferensi (P) merupakan hasil akhir perankingan. Nilai ini diperoleh dari perkalian matriks normalisasi (R) dengan vektor bobot (W) kriteria hasil perhitungan AHP. 
                      Calon mentor dengan nilai preferensi tertinggi merupakan rekomendasi terbaik.
                    </p>
                  </div>
                  <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 100px;">Ranking</th>
                          <th>Nama Alternatif (Calon Mentor)</th>
                          <th style="width: 250px;">Skor Visual</th>
                          <th class="text-end" style="width: 180px;">Nilai Preferensi (V)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // Get alternative names
                        $sql_names = 'SELECT id_alternative, name FROM saw_alternatives';
                        $result_names = $db->query($sql_names);
                        $names = array();
                        while ($row_n = $result_names->fetch_object()) {
                            $names[$row_n->id_alternative] = $row_n->name;
                        }

                        // Calculate preference values (P)
                        $P = array();
                        $m = count($W);

                        foreach ($R as $i => $r) {
                            $P[$i] = 0;
                            for ($j = 0; $j < $m; $j++) {
                                $P[$i] += $r[$j] * $W[$j];
                            }
                        }

                        // Sort P array descending
                        arsort($P);

                        // Display ranked results with names
                        $rank = 0;
                        $bestName = '';
                        $bestVal = 0;
                        
                        foreach ($P as $id => $val) {
                            $rank++;
                            $altName = isset($names[$id]) ? $names[$id] : "Alternatif {$id}";
                            if ($rank == 1) {
                                $bestName = $altName;
                                $bestVal = $val;
                            }
                            
                            // Visual score percentage for progress bar
                            $percentage = round($val * 100, 1);
                            
                            // Rank Badge
                            if ($rank == 1) {
                                $rankBadge = '<span style="font-size: 1.5rem;">🥇</span>';
                                $rowClass = 'table-success-light';
                            } elseif ($rank == 2) {
                                $rankBadge = '<span style="font-size: 1.5rem;">🥈</span>';
                                $rowClass = '';
                            } elseif ($rank == 3) {
                                $rankBadge = '<span style="font-size: 1.5rem;">🥉</span>';
                                $rowClass = '';
                            } else {
                                $rankBadge = '<span class="badge bg-secondary">' . $rank . '</span>';
                                $rowClass = '';
                            }
                            
                            echo "<tr class='{$rowClass}'>
                                    <td class='text-center align-middle'>{$rankBadge}</td>
                                    <td class='align-middle font-bold'>{$altName}</td>
                                    <td class='align-middle'>
                                      <div class='progress progress-primary' style='height: 8px;'>
                                        <div class='progress-bar' role='progressbar' style='width: {$percentage}%' aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'></div>
                                      </div>
                                      <small class='text-muted'>{$percentage}%</small>
                                    </td>
                                    <td class='text-end align-middle font-bold text-primary' style='font-size: 1.05rem;'>" . round($val, 4) . "</td>
                                  </tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>

                  <?php if (!empty($P)): ?>
                  <div class="card-body">
                    <div class="alert alert-success d-flex align-items-center" role="alert" style="border-left: 5px solid var(--success) !important; background-color: var(--success-light); color: #065f46;">
                      <div class="me-3" style="font-size: 2.2rem;">🏆</div>
                      <div>
                        <h5 class="alert-heading font-bold mb-1" style="color: #065f46;">Rekomendasi Keputusan</h5>
                        <p class="mb-0">
                          Berdasarkan hasil kalkulasi pembobotan AHP dan perankingan SAW, calon mentor terbaik yang direkomendasikan adalah 
                          <strong><?= $bestName ?></strong> dengan skor preferensi akhir sebesar <strong><?= round($bestVal, 4) ?></strong>.
                        </p>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>

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
