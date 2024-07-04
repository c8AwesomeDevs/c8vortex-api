<?php
namespace App\Contracts;

interface AttributeValueInterface
{
    public function getAttributeValues($element_id, $start, $end, $order);
    public function save($data);
}