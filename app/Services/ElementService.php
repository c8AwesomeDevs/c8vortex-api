<?php

namespace App\Services;

use App\Contracts\ElementInterface;
use App\Models\Element;
use App\Models\UserElement;
use DB;

class ElementService implements ElementInterface
{
    public function get($id)
    {
        $elements = Element::where('company_id', $id)->where('template_created', 1);

        return $elements->get();
    }

    public function deleteAllAssets($company_id){
        $deleteallAssets = Element::where('company_id', $company_id)->delete();

        return $deleteallAssets;
    }

    public function getElementPath($id, $path)
    {
        $element = Element::find($id);
        $parent = Element::find($element->parent_id);

        if (!is_object($parent)) {
            return implode('\\', array_reverse($path));
        } else {
            $path[] = $parent->name;
            return $this->getElementPath($parent->id, $path);
        }
    }

    public function getElementDetails($id)
    {
        $element = Element::find($id);
        return $element;
    }

    public function generateHierarchy($company_id, $user_id)
    {
        // this will ONLY work under the assumption the the user will always use the hierarchy like
        // sites -> substations -> transformer

     
        // get assigned assets to the user -> transformers
        $assigned_assets = $this->getElementsAssignedToUserArray($user_id);

        // get parents of transformers -> substations (specific)
        $substations = Element::select('parent_id')->whereIn('id', $assigned_assets)->distinct()->get()
            ->map(function ($e) {
                return $e['parent_id'];
            })->toArray();

        // get parents of substations -> sites (specific)
        $sites = Element::select('parent_id')->whereIn('id', $substations)->distinct()->get()
            ->map(function ($e) {
                return $e['parent_id'];
            })->toArray();

        // use the specific sites to only generate specific trees
        $elements = Element::where('company_id', $company_id)->whereIn('id', $sites)->get();

        $hierarchy = [];
        foreach ($elements as $e) {
            $element = $e;
            $element['children'] = $this->getChildren($e->id, $assigned_assets);
            $hierarchy[] = $element;
        }

        return $hierarchy;
    }
    

    public function getChildren($id, $assigned_assets, $children = [])
    {
        $elements = Element::where('parent_id', $id)->get();

        foreach ($elements as $e) {
            $element = $e;
            // this is a recursive function
            $element['children'] = $this->getChildren($e->id, $assigned_assets, []);
            if ($element["template_created"] == false) {
                // this lets substation elements pass
                $children[] = $element;
            }
            if ($element["template_created"] == true && in_array($element["id"], $assigned_assets)) {
                // this filters out transformer elements that are not assigned to the user
                $children[] = $element;
            };
        }

        return $children;
    }

    public function generateHierarchyAll($company_id)
    {
        $elements = Element::where('company_id', $company_id)->whereNull('parent_id')->get();

        $hierarchy = [];
        foreach ($elements as $e) {
            $element = $e;
            $element['children'] = $this->getChildrenAll($e->id);
            $hierarchy[] = $element;
        }

        return $hierarchy;
    }

    public function getChildrenAll($id, $children = [])
    {
        $elements = Element::where('parent_id', $id)->get();

        foreach ($elements as $e) {
            $element = $e;
            // this is a recursive function
            $element['children'] = $this->getChildrenAll($e->id, []);
            $children[] = $element;
        }

        return $children;
    }

    public function getElementsByCompany($company_id)
    {
        // root element does not have parent
        $rootCount = Element::where('company_id', $company_id)->whereNull('parent_id')->count();
        // substation element have parent and have false for template_created
        $subCount = Element::where('company_id', $company_id)->whereNotNull('parent_id')->where('template_created', false)->count();
        // transformer element have true for template_created
        $tfmrCount = Element::where('company_id', $company_id)->where('template_created', true)->count();

        return ["root" => $rootCount, "sub" => $subCount, "tfmr" => $tfmrCount];
    }

    public function add($data)
    {
        $element = Element::create($data);
        return $element;
    }

    public function update($id, $data)
    {
        $update = Element::where('id', $id)->update($data);
        return $update;
    }

    public function deleteElementsByPath($path)
    {
        $element = Element::where('path', 'LIKE', $path . '%')->delete();

        return true;
    }

    public function elementExists($name, $parent_id)
    {
        $element = Element::where('name', $name)->where('parent_id', $parent_id)->count();

        return $element;
    }

    public function getElementsAssignedToUserArray($user_id)
    {
        $assigned_assets = UserElement::where("user_id", $user_id)->get()
            ->map(function ($e) {
                return $e['element_id'];
            })->toArray();

        return $assigned_assets;
    }
}
