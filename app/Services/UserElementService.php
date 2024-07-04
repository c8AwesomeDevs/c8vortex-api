<?php

namespace App\Services;

use App\Models\UserElement;

class UserElementService
{
    public function get($user_id)
    {
        $elements = UserElement::where('user_id', $user_id);

        return $elements->get();
    }

    public function getElementIds($user_id)
    {
        $elements = UserElement::where('user_id', $user_id)->select('element_id');

        return $elements->get();
    }

    public function add($user_id, $element_id)
    {
        $element = UserElement::create([
            'user_id' => $user_id,
            'element_id' => $element_id
        ]);

        return $element;
    }

    public function delete($user_id, $element_id)
    {
        $element = UserElement::where('user_id', $user_id)->where('element_id', $element_id)->delete();

        return $element;
    }

    public function deleteUserelement($user_id){
        
        $deleteuserelement = UserElement::where('user_id', $user_id)->delete();

        return $deleteuserelement;
    }
}
