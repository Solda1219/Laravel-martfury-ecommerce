<?php

namespace Theme\Martfury\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RvMedia;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'url'   => $this->url,
            'image' => RvMedia::getImageUrl($this->image, null, false, RvMedia::getDefaultImage()),
        ];
    }
}
