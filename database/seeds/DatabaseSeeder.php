<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        DB::table('users')->insert([
            'company_id'=> 1,
            'name'=>'Calibr8',
            'phone_number'=> '09772604113',
            'account_type'=>'google',
            'account_level'=>'company_admin',
            'email' => 'c8vortex.calibr8@gmail.com',
            'account_status' => 'active',
        ]);
       

        DB::table('companies')->insert([
            'company_name'=> 'C8',
            'country' => 'Philippines',
            'domain'=>'gmail.com',
            'hear_aboutus'=> 'Advertising',
            'industry'=>'Manufacturing Corporations',
            'updated'=> 0,
            'max_root' => -1,
            'max_sub' => -1,
            'max_tfmr' => -1,
            'max_datapoints' => -1
        ]);

        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 days'));
        $reference_id = uniqid();
        DB::table('subscriptions')->insert([
            'user_id' => 1,
            'company_id' => 1,
            'subscription_type' => "demo",
            'subtotal' => 0,
            'total' => 0,
            'currency' => 'USD',
            'payment_status' => 'paid',
            'activation_date' => $start,
            'expiration_date' => $end,
            'reference_id' => $reference_id,
            'activated' => 1,

        ]);

        DB::table('checks')->insert([
            'transformer_id' => 1,
            'check_name' => 'CUBSIEDN703',
        ]);
        // DB::table('users')->insert([
        //     'company_id'=> 2,
        //     'name'=>'Jr Honor',
        //     'phone_number'=> '09772604113',
        //     'account_type'=>'google',
        //     'account_level'=>'company_admin',
        //     'email' => 'jerryhonorjr@gmail.com',
        //     'account_status' => 'active',
        // ]);
       

        // DB::table('companies')->insert([
        //     'company_name'=> 'Calibr8',
        //     'country' => 'Philippines',
        //     'domain'=>'gmail.com',
        //     'hear_aboutus'=> 'Advertising',
        //     'industry'=>'Manufacturing Corporations',
        //     'updated'=> 0
        // ]);


        // DB::table('companies')->insert([
        //     'name'=> 'calibr8',
        //     'domain'=>'gmail.com',
        //     'country_code'=> 'PH',
        //     'industry'=>'Manufacturing Corporations',
        //     'updated'=> 1
        // ]);


        // DB::table('elements')->insert([
        //     'company_id' => 2,
        //     'name' => 'Site 1',
        //     'description' => '',
        //     'path' => 'Site 1',
        //     'has_child' => 1
        // ]);

        // DB::table('elements')->insert([
        //     'company_id' => 2,
        //     'parent_id' => 1,
        //     'name' => 'Substation 1',
        //     'description' => '',
        //     'path' => 'Site 1\\Substation 1',
        //     'has_child' => 1
        // ]);
        // DB::table('elements')->insert([
        //     'company_id' => 2,
        //     'parent_id' => 2,
        //     'name' => 'Transformer 1',
        //     'description' => '',
        //     'path' => 'Site 1\\Substation 1\\Transformer 1',
        //     'has_child' => 1
        // ]);
        // DB::table('elements')->insert([
        //     'company_id' => 2,
        //     'parent_id' => 2,
        //     'name' => 'Transformer 2',
        //     'description' => '',
        //     'path' => 'Site 1\\Substation 1\\Transformer 2',
        //     'has_child' => 1
        // ]);

        // DB::table('elements')->insert([
        //     'company_id' => 1,
        //     'name' => 'Site 2',
        //     'description' => '',
        //     'path' => 'Site 2',
        //     'has_child' => 1
        // ]);

        // DB::table('elements')->insert([
        //     'company_id' => 1,
        //     'parent_id' => 5,
        //     'name' => 'Substation 1',
        //     'description' => '',
        //     'path' => 'Site 2\\Substation 1',
        //     'has_child' => 1
        // ]);
        // DB::table('elements')->insert([
        //     'company_id' => 1,
        //     'parent_id' => 6,
        //     'name' => 'Transformer 3',
        //     'description' => '',
        //     'path' => 'Site 2\\Substation 1\\Transformer 3',
        //     'has_child' => 1
        // ]);
        // DB::table('elements')->insert([
        //     'company_id' => 1,
        //     'parent_id' => 6,
        //     'name' => 'Transformer 4',
        //     'description' => '',
        //     'path' => 'Site 2\\Substation 1\\Transformer 4',
        //     'has_child' => 1
        // ]);

        // DB::table('companies')->insert([
        //     'name' => 'Gmail',
        //     'domain' => 'gmail.com',
        //     'state' => 'Philippines',
        //     'industry' => 'System Integrator'
        // ]);

        // DB::table('companies')->insert([
        //     'name' => 'Calibr8 Systems Inc',
        //     'domain' => 'calibr8systems.com',
        //     'state' => 'Philippines',
        //     'industry' => 'System Integrator'
        // ]);

        // DB::table('attribute_values')->insert([
        //     'element_id' => 3,
        //     'timestamp' => '2022-10-22 01:00:00',
        //     'acetylene' => rand(1, 100),
        //     'ethylene' => rand(1, 100),
        //     'methane' => rand(1, 100),
        //     'ethane' => rand(1, 100),
        //     'hydrogen' => rand(1, 100),
        //     'oxygen' => rand(1, 100),
        //     'carbon_monoxide' => rand(1, 100),
        //     'carbon_dioxide' => rand(1, 100),
        //     'tdcg' => rand(1, 100),
        //     't1' => 't1 result',
        //     't4' => 't4 result',
        //     't5' => 't5 result',
        //     'p1' => 'p1 result',
        //     'p2' => 'p2 result',
        //     'iec_ratio' => 'IEC Ratio Test',
        //     'dornenberg' => 'Dornenberg Ratio Test',
        //     'rogers_ratio' => 'Rogers Ratio Test',
        //     'nei' => 'NEI Result Test',
        // ]);

        // DB::table('attribute_values')->insert([
        //     'element_id' => 4,
        //     'timestamp' => '2022-10-22 01:00:00',
        //     'acetylene' => rand(1, 100),
        //     'ethylene' => rand(1, 100),
        //     'ethane' => rand(1, 100),
        //     'methane' => rand(1, 100),
        //     'hydrogen' => rand(1, 100),
        //     'oxygen' => rand(1, 100),
        //     'carbon_monoxide' => rand(1, 100),
        //     'carbon_dioxide' => rand(1, 100),
        //     'tdcg' => rand(1, 100),
        //     't1' => 't1 result',
        //     't4' => 't4 result',
        //     't5' => 't5 result',
        //     'p1' => 'p1 result',
        //     'p2' => 'p2 result',
        //     'iec_ratio' => 'IEC Ratio Test',
        //     'dornenberg' => 'Dornenberg Ratio Test',
        //     'rogers_ratio' => 'Rogers Ratio Test',
        //     'nei' => 'NEI Result Test',
        // ]);
    }
}
