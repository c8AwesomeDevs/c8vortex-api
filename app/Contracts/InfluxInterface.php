<?php

namespace App\Contracts;

interface InfluxInterface
{
    public function clean_results($tags, $datas);
    public function getevents($check_name);
    public function saveEvents($events);
    public function getCheckname($transformer_id);
    public function getcurrentEvent($check_name);
}
