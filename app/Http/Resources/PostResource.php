<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Digunakan untuk mengubah data dari Model menjadi format JSON dengan cepat dan mudah
class PostResource extends JsonResource
{
    // define properti
    public $status;
    public $message;
    public $resource; //data yang akan dikirimkan dalam respon JSON

    // construct
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
