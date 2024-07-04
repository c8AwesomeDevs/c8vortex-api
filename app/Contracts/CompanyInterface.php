<?php
namespace App\Contracts;

interface CompanyInterface 
{
    public function registerCompany($data);
    public function getCompanies();
    public function getCompanyByDomain($domain);
    public function getCompanyDetails($id);
}