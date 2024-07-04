<?php
namespace App\Contracts;

interface TransformerInterface
{
    public function getTransformersDetails($element_id);
    public function save($data);
}