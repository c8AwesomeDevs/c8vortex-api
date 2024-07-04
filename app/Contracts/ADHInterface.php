<?php
namespace App\Contracts;

interface ADHInterface
{
  public function getAdh($company_id);
  public function addAdh($data);
}