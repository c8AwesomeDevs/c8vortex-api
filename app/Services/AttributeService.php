<?php

namespace App\Services;

use App\Contracts\AttributeInterface;
use App\Models\Attribute;
use App\Services\ElementService;

class AttributeService extends ElementService
{
    public function getGasses() {
        $gasses = [
            [
                'name' =>'C2H2',
                'description' => 'Acetylene Level'
            ],
            [
                'name' =>'C2H4',
                'description' => 'Ethylene Level'
            ],
            [
                'name' =>'CH4',
                'description' => 'Methane Level'
            ],
            [
                'name' =>'C2H6',
                'description' => 'Ethane Level'
            ],
            [
                'name' =>'H2',
                'description' => 'Hydrogen Level'
            ],
            [
                'name' =>'O2',
                'description' => 'Oxygen Level'
            ],
            [
                'name' =>'N2',
                'description' => 'Nitrogen Level'
            ],
            [
                'name' =>'CO',
                'description' => 'Carbon Monoxide Level'
            ],
            [
                'name' =>'CO2',
                'description' => 'Carbon Dioxide Level'
            ]
        ];

        return $gasses;
    }

    public function get($id) {
        $attributes = Attribute::where('element_id', $id);

        return $attributes->get();
    }

    public function getDetails($id) {
        $attribute = Attribute::find($id);
        
        return $attribute;
    }
    public function create($attribute) {
        $attribute = Attribute::create($attribute);

        return $attribute;
    }

    public function update($id, $data) {
        $attribute = Attribute::update($data)->where('id', $id);

        return $attribute;
    }

    public function createAllGases($id, $company) {
        $element = Parent::getElementDetails($id);
        foreach($this->getGasses() as $gas) {
            $attribute_path = $element->path . '\\' . $gas['name'];
            $stream_name = $company . '_' . str_replace('\\', '_', $attribute_path);
            $attribute = [
                'element_id' => $id,
                'name' => $gas['name'],
                'description' => $gas['description'],
                'stream_name' => $stream_name,
                'path' => $attribute_path
            ];
            $this->create($attribute);
        }

        $update = Parent::update($id, ['template_created' => 1]);

        return $this->get($id);
    }

    public function deleteAttributesByElementPath($path) {
        $attribute = Attribute::where('path', 'like', $path . '%')->delete();

        return true;
    }
}