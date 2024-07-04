<?php

namespace App\Contracts;

interface ElementInterface
{
    public function get($id);
    public function getElementDetails($id);
    public function add($data);
    public function update($id, $data);
    public function deleteElementsByPath($path);
    public function elementExists($name, $parent_id);
}