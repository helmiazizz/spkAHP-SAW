<!DOCTYPE html>
<html lang="en">
    <?php 
    require "layout/head.php";
    require "include/conn.php";

    // Fetch total alternatives
    $res_alt = $db->query("SELECT COUNT(*) AS cnt FROM saw_alternatives");
    $row_alt = $res_alt->fetch_object();
    $total_alt = $row_alt->cnt;

    // Fetch total criteria
    $res_crit = $db->query("SELECT COUNT(*) AS cnt FROM saw_criterias");
    $row_crit = $res_crit->fetch_object();
    $total_crit = $row_crit->cnt;

    // Fetch Best Mentor recommendations
    // 1. Load weights W
    $sql_w = "SELECT weight FROM saw_criterias ORDER BY id_criteria";
    $res_w = $db->query($sql_w);
    $W = array();
    while ($row = $res_w->fetch_object()) {
        $W[] = $row->weight;
    }
    
    // 2. Load values for normalization
    $sql_crit_info = 'SELECT id_criteria, attribute FROM saw_criterias ORDER BY id_criteria';
    $res_crit_info = $db->query($sql_crit_info);
    $criterias_info = array();
    while ($row_c = $res_crit_info->fetch_object()) {
        $criterias_info[] = $row_c;
    }

    $select_parts = array();
    foreach ($criterias_info as $c) {
        $id = $c->id_criteria;
        $select_parts[] = "SUM(IF(a.id_criteria={$id},a.value,0)) AS C{$id}";
    }
    $select_str = implode(',', $select_parts);

    $sql_vals = "SELECT a.id_alternative, b.name, {$select_str}
                FROM saw_evaluations a
                JOIN saw_alternatives b USING(id_alternative)
                GROUP BY a.id_alternative";
    $res_vals = $db->query($sql_vals);
    
    $X = array();
    foreach ($criterias_info as $c) {
        $X[$c->id_criteria] = array();
    }
    $names = array();
    while ($row = $res_vals->fetch_object()) {
        $names[$row->id_alternative] = $row->name;
        foreach ($criterias_info as $c) {
            $col = 'C' . $c->id_criteria;
            $X[$c->id_criteria][] = $row->$col;
        }
    }

    // Calculate normalizations and preference
    $best_mentor = "Belum Dihitung";
    $best_score = 0;

    if (!empty($X)) {
        $norm_parts = array();
        foreach ($criterias_info as $c) {
            $id = $c->id_criteria;
            $maxVal = max($X[$id]) ? max($X[$id]) : 1;
            $minVal = min($X[$id]) ? min($X[$id]) : 1;
            $norm_parts[] = "SUM(IF(a.id_criteria={$id},IF(b.attribute='benefit',a.value/{$maxVal},{$minVal}/a.value),0)) AS C{$id}";
        }
        $norm_str = implode(',', $norm_parts);

        $sql_norm = "SELECT a.id_alternative, {$norm_str}
                    FROM saw_evaluations a
                    JOIN saw_criterias b USING(id_criteria)
                    GROUP BY a.id_alternative";
        $res_norm = $db->query($sql_norm);
        
        $P = array();
        while ($row = $res_norm->fetch_object()) {
            $id = $row->id_alternative;
            $P[$id] = 0;
            for ($j = 0; $j < count($W); $j++) {
                $col = 'C' . $criterias_info[$j]->id_criteria;
                $P[$id] += $row->$col * $W[$j];
            }
        }
        
        if (!empty($P)) {
            arsort($P);
            $best_id = key($P);
            $best_score = current($P);
            $best_mentor = isset($names[$best_id]) ? $names[$best_id] : "Alternatif {$best_id}";
        }
    }
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
                    <h3>Dashboard</h3>
                </div>
                <div class="page-content">
                    
                    <!-- Stats Section -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="summary-widget">
                                <div>
                                    <h6 class="text-muted font-semibold mb-1">Total Calon Mentor</h6>
                                    <h3 class="mb-0 font-extrabold"><?= $total_alt ?></h3>
                                </div>
                                <div class="summary-icon primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="summary-widget">
                                <div>
                                    <h6 class="text-muted font-semibold mb-1">Jumlah Kriteria SPK</h6>
                                    <h3 class="mb-0 font-extrabold"><?= $total_crit ?></h3>
                                </div>
                                <div class="summary-icon info">
                                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="summary-widget">
                                <div>
                                    <h6 class="text-muted font-semibold mb-1">Rekomendasi Terbaik</h6>
                                    <h3 class="mb-0 font-extrabold text-success" style="font-size: 1.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                        <?= $best_mentor ?>
                                    </h3>
                                </div>
                                <div class="summary-icon success">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <section class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span class="badge bg-success me-3">AHP + SAW</span>
                                    <h4>Sistem Pendukung Keputusan Pemilihan Mentor Program Magang Internal</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <h5 class="mb-3 d-flex align-items-center" style="color: var(--primary);">
                                                    <i class="bi bi-calculator-fill me-2"></i> Metode AHP (Analytic Hierarchy Process)
                                                </h5>
                                                <p class="card-text text-justify">
                                                    Metode AHP adalah metode pengambilan keputusan multi-kriteria yang mengorganisir 
                                                    faktor-faktor kompleks ke dalam struktur hierarki. Dengan menggunakan perbandingan berpasangan, 
                                                    AHP menentukan bobot prioritas relatif antar kriteria secara objektif. 
                                                    Di sini, AHP digunakan untuk mencari <strong>bobot presisi</strong> dari kriteria pemilihan mentor.
                                                </p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <h5 class="mb-3 d-flex align-items-center" style="color: var(--secondary);">
                                                    <i class="bi bi-bar-chart-line-fill me-2"></i> Metode SAW (Simple Additive Weighting)
                                                </h5>
                                                <p class="card-text text-justify">
                                                    Metode SAW merupakan metode penjumlahan terbobot dari rating kinerja pada setiap alternatif di semua kriteria. 
                                                    Metode ini membutuhkan proses normalisasi matriks keputusan (X) ke suatu skala yang sebanding. 
                                                    Di sini, SAW digunakan untuk melakukan <strong>perankingan akhir</strong> dari calon mentor berdasarkan bobot AHP.
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <hr class="my-4">
                                        
                                        <h5 class="mb-3"><i class="bi bi-patch-check-fill text-success me-2"></i> Langkah-langkah Integrasi AHP & SAW:</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">1</span>
                                                        <span>Menentukan kriteria mentor (C1-C3: EPT, IPK, Jurusan).</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">2</span>
                                                        <span>Membuat matriks perbandingan berpasangan kriteria menggunakan skala Saaty (AHP).</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">3</span>
                                                        <span>Menghitung bobot prioritas kriteria dan memastikan Consistency Ratio (CR) &le; 0.1 (AHP).</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">4</span>
                                                        <span>Mengisi data nilai performa kecocokan masing-masing calon mentor di menu Matrik.</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">5</span>
                                                        <span>Normalisasi matriks kecocokan (R) berdasarkan atribut kriteria (benefit/cost) (SAW).</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent d-flex align-items-start border-0 ps-0">
                                                        <span class="badge bg-primary me-2">6</span>
                                                        <span>Menghitung nilai preferensi perkalian bobot W dengan matriks R untuk mendapatkan ranking terbaik (SAW).</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
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