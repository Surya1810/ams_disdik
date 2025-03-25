<?php
function formatRupiah($value, $prefix = null)
{
    $prefix = $prefix ? $prefix : 'Rp';
    $nominal = $value;
    return $prefix . number_format($nominal, 0, ',', '.');
}
