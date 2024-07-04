<?php

namespace App\Services;

use App\Models\Element;
use App\Models\Transformer;
use App\Models\Company;
use App\Contracts\TransformerInterface;

class TransformerService implements TransformerInterface

{
    public static function getTransformerElements($id)
    {
        $elements = Element::where('parent_id', $id);
        return $elements->get();
    }

    public static function getTransformerElementsByPath($path, $company_id, $assigned)
    {
        $elements = Element::where('path', 'LIKE', $path . '%')
            ->where('template_created', 1)
            ->where('company_id', $company_id) // Add this line to filter by company_id
            ->whereIN('id', $assigned)
            ->orderBy('path', 'ASC');

        return $elements->get();
    }

    public static function deleteAllTransformers($company_id){

        $deleteAllTranformers = Transformer::where('company_id', $company_id)->delete();

        return $deleteAllTranformers;
    }
    

    // public static function getTransformerElementsByPath($path) {
    //     $parts = explode("\\", $path);
    //     if (count($parts) >= 3) {
    //         $path2 = $parts[1];
    //         $elements = Element::where('path', 'LIKE', ('%\\' . $path2  . '\\%') . '%' )
    //             ->orderBy('path', 'ASC')
    //             ->where('template_created', 1);
    //         return $elements->get();
    //     } else {
    //         return [];
    //     }
    // }
    public function getTransformersDetails($element_id)
    {
        $transformer_details = Transformer::where('element_id', $element_id);

        return $transformer_details->get();
    }

    public function save($data)
    {
        $new_transformers = Transformer::create($data);

        return $new_transformers;
    }

    public function update($id, $data)
    {
        $updated = Transformer::where('element_id', $id)->first()->update($data);

        return $updated;
    }
}
