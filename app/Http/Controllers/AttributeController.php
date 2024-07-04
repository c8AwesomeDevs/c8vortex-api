<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttributeService;
use App\Services\ElementService;
use App\Services\CompanyService;

class AttributeController extends Controller
{
    public function getAttributes($id, AttributeService $attributeService) {
        $attributes = $attributeService->get($id);

        return response()->json($attributes);
    }

    public function createAttributes($id,Request $request, AttributeService $attributeService, CompanyService $companyService) {
        $company = $companyService->getCompanyDetails($request->company_id);
        $attributeService->createAllGases($id, $company->name);
        
        return $attributeService->get($id);
    }

    public function getAttributeDetails($id, AttributeService $attributeService) {
        return $attributeService->getDetails($id);
    }
}
