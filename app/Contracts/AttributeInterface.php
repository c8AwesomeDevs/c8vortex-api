<?php
namespace App\Contracts;

interface AttributeInterface 
{
    public function getGasses();
    public function createAllGases($id, $company);
    public function get($id);
    public function getDetails($id);
    public function create($attribute);
    public function update($id, $attrbute);
}