<?php
namespace App\Contracts;

Interface UserInterface {
     public function saveUser($data);
    public function getUser($email);
    public function registerUser($data);
    public function getFullDetails($id);
    public function getCompanyUsers($company_id);
    public function updateUser($id, $data);
}