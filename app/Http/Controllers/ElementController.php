<?php

namespace App\Http\Controllers;

use App\Http\Resources\ElementOverviewResource;
use App\Http\Resources\TransformerResource;
use App\Models\Element;
use App\Services\ADHService;
use Illuminate\Http\Request;
use App\Services\ElementService;
use App\Services\AttributeService;
use App\Services\AttributeValueService;
use App\Services\CompanyService;
use App\Services\TransformerService;
use App\Services\CommentsService;
use App\Services\UserElementService;
use App\Services\UserService;

class ElementController extends Controller
{
    public function getElements(Request $request, ElementService $elementService)
    {
        $elements = $elementService->get($request->company_id);
        return response()->json($elements);
    }

    public function getHierarchy(Request $request, ElementService $elementService, UserService $userservice)
    {
        $userisadmin = $userservice->isUserAnAdmin($request->user_id);

        if($userisadmin == true){
            return response()->json($elements = $elementService->generateHierarchyAll($request->company_id));
        }else{
            return response()->json($elementService->generateHierarchy($request->company_id, $request->user_id));
        }

       
    }

    public function getElementDetails(
        $id,
        Request $request,
        ElementService $elementService,
        AttributeService $attributeService,
        AttributeValueService $attributeValueService,
        CommentsService $commentsService,
        TransformerService $transformerService,
        ADHService $adhService,
    ) {

      
        $element_details = $elementService->getElementDetails($id);
        $transformer_details = $transformerService->getTransformersDetails($id);
        $comments = $commentsService->getComments($id);
        $assigned = $elementService->getElementsAssignedToUserArray($request->user_id);
        $transformers = $transformerService->getTransformerElementsByPath($element_details->path, $element_details->company_id, $assigned);
        $adh_config = $adhService->getAdh($element_details->company_id);
        return response()->json(
            [
                'details' => $element_details,
                'attributes' => $attributeService->get($id),
                'attribute_values' => $attributeValueService->getAttributeValues($id, null, null, 'DESC'),
                'transformers' => TransformerResource::collection($transformers),
                'transformer_details' => $transformer_details,
                'comment' => $comments,
                'adh_config' => $adh_config,
            ]
        );
    }
    public function updateElementDetails($id, Request $request, ElementService $elementService)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            // 'path' => $request->path
        ];
        return response()->json($elementService->update($id, $data));
    }

    public function addElementDetails(Request $request, ElementService $elementService, AttributeService $attributeService, CompanyService $companyService, UserElementService $userElementService)
    {

        // if ($elementService->elementExists($request->name, $request->parent_id)) {
        //     return response()->json([
        //         'message' => $request->name . ' already exists'
        //     ], 400);
        // }
        $elems = $elementService->getElementsByCompany($request->company_id); // company_id is read from the bearer token
        $limits = $companyService->getCompanyDetails($request->company_id); // company details.. inside here is the limits
        if (!isset($request->parent_id)) {
            if ($elems["root"] == $limits['max_root']) return response()->json("You've reached the limit for 'Site' elements", 401);
        }
        if (isset($request->parent_id) && !isset($request->template_created)) {
            if ($elems["sub"] == $limits['max_sub']) return response()->json("You've reached the limit for 'Substation' elements", 401);
        }
        if ($request->template_created == true) {
            if ($elems["tfmr"] == $limits['max_tfmr']) return response()->json("You've reached the limit for 'Transformer' elements", 401);
        }

        //Create Element
        $new_element = $elementService->add($request->all());

        //Update Element Path
        $update_path = $elementService->update($new_element->id, [
            'path' => $this->getElementPath($new_element->id, $elementService)
        ]);

        //Create Attribute
        if ($request->has('template_created') && $request->template_created) {
            $company = $companyService->getCompanyDetails($request->company_id);
            $attributeService->createAllGases($new_element->id, $company->name);

            $userElementService->add($request->user_id, $new_element->id);
        }

        $elements = $elementService->generateHierarchyAll($request->company_id);

        return response()->json($elements);
    }

    public function getElementPath($id, ElementService $elementService)
    {
        $path = [$elementService->getElementDetails($id)->name];
        return $elementService->getElementPath($id, $path);
    }

    // public function deleteElement($id, Request $request, AttributeService $attributeService)
    // {
    //     $element = $attributeService->getElementDetails($id);
    //     $element_delete = $attributeService->deleteElementsByPath($element->path);
    //     $attributes_delete = $attributeService->deleteAttributesByElementPath($element->path);

    //     return response()->json($attributeService->generateHierarchy($request->company_id));
    // }

    // public function getTransformersOverview($id, ElementService $elementService)
    // {
    //     $element = Element::find($id);
    //     $elements = TransformerService::getTransformerElementsByPath($element->path);

    //     return TransformerResource::collection($elements);
    // }
}
