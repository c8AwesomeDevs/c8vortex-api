<?php

namespace App\Http\Resources;

use App\Traits\NormalCheckerTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class TransformerResource extends JsonResource
{
    use NormalCheckerTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,  
            'company_id' => $this->company_id,
            'name' => $this->name,
            'description' => $this->description,
            'path' => $this->path,
            'latest_value' => $this->latest_attribute_value,
            'template_created' => $this->template_created,
            'status' => $this->latest_attribute_value ? $this->getStatus($this->latest_attribute_value) : ['normal' => true, 'errors' => []]
        ];
    }
}
