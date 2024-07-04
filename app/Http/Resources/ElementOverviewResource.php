<?php

namespace App\Http\Resources;

use App\Traits\NormalCheckerTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ElementOverviewResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    use NormalCheckerTrait;

    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'parent_id' => (int) $this->parent_id,
            'name' => $this->name,
            'path' => $this->path,
            'description' => $this->description,
            'has_child' => (int) $this->has_child,
            'template_created' => $this->template_created,
            'latest_data' => $this->latest_attribute_value,
            'children' => ElementOverviewResource::collection($this->children),
            'status' => is_object($this->latest_attribute_value) ? $this->getStatus($this->latest_attribute_value) : null
        ];
    }
}
