<?php
// Include your database connection file
include 'koneksi.php';

if (isset($_POST['pasien_id'])) {
    $pasienId = $_POST['pasien_id'];

    // Fetch details for the selected patient
    $detailsQuery = "SELECT p.nama AS nama_pasien, tp.tgl_periksa, tp.catatan, tp.biaya_periksa, d.nama AS nama_dokter, dp.keluhan, o.nama_obat, dpk.id_periksa
                FROM pasien p
                JOIN daftar_poli dp ON p.id = dp.id_pasien
                JOIN periksa tp ON dp.id = tp.id_daftar_poli
                JOIN dokter d ON tp.id= d.id
                JOIN detail_periksa dpk ON tp.id = dpk.id_periksa
                JOIN obat o ON dpk.id_obat = o.id
                WHERE p.id = $pasienId;";

    $detailsResult = $mysqli->query($detailsQuery);

    if ($detailsResult) {
        $detailsData = $detailsResult->fetch_all(MYSQLI_ASSOC);

        // Create an associative array to store values based on id_periksa
        $resultArray = array();

        foreach ($detailsData as $detailsRow) {
            $idPeriksa = $detailsRow['id_periksa'];

            // If id_periksa is not in the result array, initialize an array for it
            if (!isset($resultArray[$idPeriksa])) {
                $resultArray[$idPeriksa] = $detailsRow;
                $resultArray[$idPeriksa]['nama_obat'] = array($detailsRow['nama_obat']);
            } else {
                // If id_periksa is already in the result array, concatenate the nama_obat values
                $resultArray[$idPeriksa]['nama_obat'][] = $detailsRow['nama_obat'];
            }
        }

        // Display details in a table
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama Pasien</th>';
        echo '<th>Tanggal Periksa</th>';
        echo '<th>Catatan</th>';
        echo '<th>Biaya Periksa</th>';
        echo '<th>Nama Dokter</th>';
        echo '<th>Keluhan</th>';
        echo '<th>Nama Obat</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($resultArray as $resultRow) {
            echo '<tr>';
            echo '<td>' . $resultRow['nama_pasien'] . '</td>';
            echo '<td>' . $resultRow['tgl_periksa'] . '</td>';
            echo '<td>' . $resultRow['catatan'] . '</td>';
            echo '<td>' . $resultRow['biaya_periksa'] . '</td>';
            echo '<td>' . $resultRow['nama_dokter'] . '</td>';
            echo '<td>' . $resultRow['keluhan'] . '</td>';
            echo '<td>' . implode(", ", $resultRow['nama_obat']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'Error fetching details.';
    }
} else {
    echo 'Invalid request.';
}
