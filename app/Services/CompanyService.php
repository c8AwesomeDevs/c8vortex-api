<?php

namespace App\Services;

use App\Contracts\CompanyInterface;
use App\Models\Company;
use App\User;

class CompanyService implements CompanyInterface
{
    public function getCompanies() {
        $companies = Company::orderBy('company_name', 'ASC');

        return $companies->get();
    }

    // public function isCompanyNameRegistered($companyName)
    // {
    //     // Use Eloquent ORM to check if any company with the provided company name exists in the companies table
    //     $registered = Company::where('company_name', $companyName)->exists();

    //     return $registered;
    // }

    
    public function getIdByCompanyName($companyName)
    {
        // Use Eloquent ORM to query the database and get the company ID by name
        $company = Company::where('company_name', $companyName)->first();

        // If the company exists, return its ID; otherwise, return null
        return $company;
    }

    public function getCompanyByDomain($email) {
        $domain = explode('@', $email)[1];
        $company = Company::where('domain', $domain);

        if(is_object($company->first())) {
            return $company->first();
        }
        
        $company = Company::create([
            'domain' => $domain
        ]);

        return $company;
    }

    public function getCompanyDetails($id) {
        $company = Company::find($id);

        return $company;
    }

    // public function isCompanyNameAssociatedWithUserEmail($companyName, $email)
    // {
    //     // Use Eloquent ORM to check if any user with the provided company name and email exists in the users table
    //     $associatedWithEmail = User::where('company_id', function ($query) use ($companyName) {
    //         $query->select('id')
    //               ->from('companies')
    //               ->where('company_name', $companyName)
    //               ->first(); // Use first() to fetch the company's ID
    //     })
    //     ->where('email', $email)
    //     ->exists();
    
    //     return $associatedWithEmail;
    // }

    public function registerCompany($data){
        $new_company = Company::create($data);
        return $new_company;
    }

    
    public function updateCompany($id, $data)
    {
        $company = Company::where('id', $id)->update($data);

        return $company;
    }

    public function deleteCompany($id){

        $deletecompany = Company::where('id', $id)->delete();

        return $deletecompany;
    }
}