<?php
// Template: Surat Keterangan Kelahiran
?>
<h3 align="center">SURAT KETERANGAN KELAHIRAN</h3>
<p align="center"><b>Nomor: <?= htmlspecialchars($surat['nomor_surat'] ?? ''); ?></b></p>
<hr>

<p>Yang bertanda tangan di bawah ini menerangkan bahwa telah lahir seorang anak:</p>

<table cellpadding="4">
    <tr>
        <td width="160">Nama Bayi</td>
        <td>: <?= htmlspecialchars($surat['nama_bayi'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Jenis Kelamin</td>
        <td>: <?= htmlspecialchars($surat['jk_bayi'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Tempat Lahir</td>
        <td>: <?= htmlspecialchars($surat['tempat_lahir_bayi'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Tanggal Lahir</td>
        <td>: <?= htmlspecialchars($surat['tanggal_lahir_bayi'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Jam Lahir</td>
        <td>: <?= htmlspecialchars($surat['jam_lahir_bayi'] ?? ''); ?></td>
    </tr>
</table>

<br>
<p>Data Orang Tua:</p>

<table cellpadding="4">
    <tr>
        <td width="160">Nama Ayah</td>
        <td>: <?= htmlspecialchars($surat['nama_ayah'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Nama Ibu</td>
        <td>: <?= htmlspecialchars($surat['nama_ibu'] ?? ''); ?></td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>: <?= nl2br(htmlspecialchars($surat['alamat'] ?? '')); ?></td>
    </tr>
</table>

<br>

<p>
    Surat ini dibuat untuk keperluan:
    <br>
    <strong><?= htmlspecialchars($surat['keperluan'] ?? ''); ?></strong>
</p>

<br><br><br>
<table width="100%">
    <tr>
        <td width="60%"></td>
        <td width="40%" align="center">
            Natar, <?= date('d-m-Y'); ?><br>
            Kepala Desa<br><br><br><br>
            <b><?= htmlspecialchars($_SESSION['sesi_nama'] ?? ''); ?></b>
        </td>
    </tr>
</table>