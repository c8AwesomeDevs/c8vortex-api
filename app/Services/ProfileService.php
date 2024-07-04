<?php

namespace App\Services;

use App\Contracts\ProfileInterface;
use App\User;
use App\Models\Company;
class ProfileService implements ProfileInterface
{
    public function update($data) {
        
        $company = Company::find($data['id']);
        $company->company_name = $data['company_name'];
        $company->country = $data['country'];
        $company->industry = $data['industry'];
        $company->updated = 1;
        $company->save();

        return $company;
    }
}