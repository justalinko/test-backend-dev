<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class seedEwallets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ewallets')->truncate();

        for($i=0;$i<=10;$i++){

        DB::table('ewallets')->insert([
            'user_id' => rand(1,2),
            'balance' => rand(100,99999),
            'withdrawal' => json_encode([Str::random(2) => [ 'bank' => strtoupper(Str::random(3)) , 'account_number' => rand(9999,999999) ] ] ),
        ]);

        echo "OK $i \n";
        }
    }
}
