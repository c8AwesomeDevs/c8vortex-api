<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class H2ScanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('buckets')->insert([
            'org_id' => "6acbe12e221164c8",
            'influx_bucket_id' => '',
            'influx_bucket_name' => 'Vortex',
            'influx_host' => 'https://influxdb2-c8.ngrok.dev',
            'token_read' => 'B5sEbL2F8xigJJbQ4dzaphHZRuyQZDck0TfdiwIwVjDI_ooiCI2jSDAA0NmxELyBO1sNIOq8p4-mojgKLuxtHw==',
            'token_write' => 'HDOgaEz563sFee0YXt2M-7Kzygx-HT-iV8PK41MDdfH020vGU-rkLBrDmW7evFev-802Rsjm2E_L7aikKdqTgw==',
            'company_id' => '1',
        ]);

        DB::table('sensor_types')->insert([
            [
                'type' => 'Dissolve Gas',
            ],
            [
                'type' => 'H2Scan',
            ],
        ]);

        // DB::table('sensors')->insert([
        //     [
        //         'transformer_id' => "1",
        //         'bucket_id' => '1',
        //         'sensor_type_id' => '1',
        //         '_measurement' => 'multigasSensor',
        //     ],
        //     [
        //         'transformer_id' => "1",
        //         'bucket_id' => '1',
        //         'sensor_type_id' => '2',
        //         '_measurement' => 'singlegasSensor',
        //     ],
        // ]);

        // DB::table('filters')->insert([
        //     [
        //         'key' => "asset",
        //         'value' => 'Transformer',
        //         'sensor_id' => '1',
        //     ],
        //     [
        //         'key' => "brand",
        //         'value' => 'GE',
        //         'sensor_id' => '1',
        //     ],
        //     [
        //         'key' => "customer",
        //         'value' => 'Customer X',
        //         'sensor_id' => '1',
        //     ],
        //     [
        //         'key' => "model",
        //         'value' => 'MS3000',
        //         'sensor_id' => '1',
        //     ],
        //     [
        //         'key' => "rating",
        //         'value' => '110 kV',
        //         'sensor_id' => '1',
        //     ],
        // ]);
        // DB::table('filters')->insert([
        //     [
        //         'key' => "Brand",
        //         'value' => 'H2Scan',
        //         'sensor_id' => '2',
        //     ],
        //     [
        //         'key' => "DissolvedGasCalibrationDate",
        //         'value' => '0/0/2000',
        //         'sensor_id' => '2',
        //     ],
        //     [
        //         'key' => "FactoryCalibrationDate",
        //         'value' => '14/9/2023',
        //         'sensor_id' => '2',
        //     ],
        //     [
        //         'key' => "ManufacturingDate",
        //         'value' => '14/9/2023',
        //         'sensor_id' => '2',
        //     ],
        //     [
        //         'key' => "Model",
        //         'value' => 'Gen5',
        //         'sensor_id' => '2',
        //     ],
        // ]);

        // DB::table('tags')->insert([
        //     [
        //         'tag_name' => "acetylene",
        //         'sensor_id' => '1',
        //         '_field' => 'Acetylene',
        //     ],
        //     [
        //         'tag_name' => "carbon_dioxide",
        //         'sensor_id' => '1',
        //         '_field' => 'Carbon Dioxide',
        //     ],
        //     [
        //         'tag_name' => "carbon_monoxide",
        //         'sensor_id' => '1',
        //         '_field' => 'Carbon Monoxide',
        //     ],
        //     [
        //         'tag_name' => "ethane",
        //         'sensor_id' => '1',
        //         '_field' => 'Ethane',
        //     ],
        //     [
        //         'tag_name' => "ethylene",
        //         'sensor_id' => '1',
        //         '_field' => 'Ethylene',
        //     ],
        //     [
        //         'tag_name' => "hydrogen",
        //         'sensor_id' => '1',
        //         '_field' => 'Hydrogen',
        //     ],
        //     [
        //         'tag_name' => "methane",
        //         'sensor_id' => '1',
        //         '_field' => 'Methane',
        //     ],
        // ]);
        // DB::table('tags')->insert([
        //     [
        //         'tag_name' => "DeltaDay",
        //         'sensor_id' => '2',
        //         '_field' => 'DeltaDay',
        //     ],
        //     [
        //         'tag_name' => "DeltaWeek",
        //         'sensor_id' => '2',
        //         '_field' => 'DeltaWeek',
        //     ],
        //     [
        //         'tag_name' => "DeltaMonth",
        //         'sensor_id' => '2',
        //         '_field' => 'DeltaMonth',
        //     ],
        //     [
        //         'tag_name' => "PCBTemperature",
        //         'sensor_id' => '2',
        //         '_field' => 'PCBTemperature',
        //     ],
        //     [
        //         'tag_name' => "Hydrogen",
        //         'sensor_id' => '2',
        //         '_field' => 'Hydrogen',
        //     ],
        //     [
        //         'tag_name' => "OilTemperature",
        //         'sensor_id' => '2',
        //         '_field' => 'OilTemperature',
        //     ],
        //     [
        //         'tag_name' => "DataAvailable",
        //         'sensor_id' => '2',
        //         '_field' => 'DataAvailable',
        //     ],
        //     [
        //         'tag_name' => "SensorState",
        //         'sensor_id' => '2',
        //         '_field' => 'SensorState',
        //     ],
        //     [
        //         'tag_name' => "UnitReady",
        //         'sensor_id' => '2',
        //         '_field' => 'UnitReady',
        //     ],
        //     [
        //         'tag_name' => "BatteryBackupError",
        //         'sensor_id' => '2',
        //         '_field' => 'BatteryBackupError',
        //     ],
        //     [
        //         'tag_name' => "ConfigurationDataNotValid",
        //         'sensor_id' => '2',
        //         '_field' => 'ConfigurationDataNotValid',
        //     ],
        //     [
        //         'tag_name' => "Error",
        //         'sensor_id' => '2',
        //         '_field' => 'Error',
        //     ],
        //     [
        //         'tag_name' => "HeaterFault",
        //         'sensor_id' => '2',
        //         '_field' => 'HeaterFault',
        //     ],
        //     [
        //         'tag_name' => "HydrogenSensorFault",
        //         'sensor_id' => '2',
        //         '_field' => 'HydrogenSensorFault',
        //     ],
        //     [
        //         'tag_name' => "PCBTempOver105C",
        //         'sensor_id' => '2',
        //         '_field' => 'PCBTempOver105C',
        //     ],
        //     [
        //         'tag_name' => "RequiredDataNA",
        //         'sensor_id' => '2',
        //         '_field' => 'RequiredDataNA',
        //     ],
        //     [
        //         'tag_name' => "TemperatureSensorFault",
        //         'sensor_id' => '2',
        //         '_field' => 'TemperatureSensorFault',
        //     ],
        // ]);
    }
}
