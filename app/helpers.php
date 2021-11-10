<?php
/** 
 * 
 * Sebuah kapal memiliki bagian lambung Kanan, Kiri, dan Tengah. Setiap kontainer yang akan
dimuat ke atas kapal memiliki nomer kontainer dengan 7 (tujuh) numeric. Petugas menaruh
posisi kontainer di atas kapal dengan kriteria tertentu, sebagai berikut:
*/

function isPrime($number)
{
    // 1 is not prime
    if ($number == 1) {
        return false;
    }
    // 2 is the only even prime number
    if ($number == 2) {
        return true;
    }

    $x = sqrt($number);
    $x = floor($x);
    for ($i = 2; $i <= $x; ++$i) {
        if ($number % $i == 0) {
            break;
        }
    }

    if ($x == $i - 1) {
        return true;
    } else {
        return false;
    }
}

/** 
 * 
 * (a) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
3 digit awal dihapus, maka tetap menjadi bilangan prima;
*/
function isCenter($num)
{

    /** make sure, 0 is false  */
    /** pastikan angka tidak ada angka 0 dan pastikan bilangan prima */
    if (strpos($num, 0) === false && isPrime($num)) {

        /** ketentuan : 3 digit awal di hapus maka tetap menjadi bilangan prima */
        /** digit ke 4 - end pastikan bilangan prima 
         */
        if (isPrime(substr($num,3,count(str_split($num))))) {
            return true;
        }
    }

    /** default false */
    return false;
}

/**
 * (a) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
3 (tiga) digit awal dihapus, 2 digit terakhir menjadi bilangan
prima yang berurutan angkanya;
 */
function isLeft($num)
{
    /** make sure, 0 is false  */
    /** pastikan angka tidak ada angka 0 */
    if (strpos($num, 0) === false && isPrime($num)) {

        /** karena 2 digit terakhir harus berurutan maka
         * kita menggunakan strrev() untuk membalikan angka 
         * akhir ke awal dan awal ke akhir.
         * 
         * memudahkan untuk pengindex-an array.
         */
        $last = strrev($num);
        $split = str_split($last);

        /** 2 digit terakhir berurutan 
         * maka karena tadi di balik.
         * jadi index digit terakhir adalah [0]
         * dan 2 digit terakhir adalah [1]
         */

        $urut = $split[1];
        $last_digit = $urut + 1; // berurutan index[1] di + 1

        // pastikan berurutan dan bilangan prima
        if ($last_digit == $split[0] && isPrime($last_digit) && isPrime($split[0])) {
            return true;
        }
    }

    /** default false */
    return false;
}


/** 
 * (a) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
3 (tiga) digit awal dihapus, 3 digit paling akhir merupakan
bilangan yang sama
*/
function isRight($num)
{
    /** make sure, 0 is false  */
    /** pastikan angka tidak ada angka 0 */
    if (strpos($num, 0) === false && isPrime($num)) {

        /** karena 3 digit terakhir harus berurutan maka
         * kita menggunakan strrev() untuk membalikan angka 
         * akhir ke awal dan awal ke akhir.
         * 
         * memudahkan untuk pengindex-an array.
         */
        $last = strrev($num);
        $split = str_split($last);

        /** 3 digit terakhir merupakan bilangan yang sama 
         * maka karena tadi di balik.
         * jadi index digit terakhir adalah [0]
         * dan 2 digit terakhir adalah [1]
         */

        if ($split[0] == $split[1] && $split[0] == $split[2]) {
            return true;
        }
    }

    /** default false */
    return false;
}

function isDead($num)
{
    if (strpos($num, 0) === true || !isPrime($num)) {
        return true;
    } else {
        return false;
    }
}

function detectAll($num)
{
    if (isDead($num)) {
        return 'DEAD';
    } elseif (isRight($num)) {
        return 'RIGHT';
    } elseif (isLeft($num)) {
        return 'LEFT';
    } elseif (isCenter($num)) {
        return 'CENTRAL';
    } else {
        return 'UNKNOWN';
    }
}
