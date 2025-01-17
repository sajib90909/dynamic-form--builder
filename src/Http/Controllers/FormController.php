<?php

namespace AlAmin\Form\Http\Controllers;

use AlAmin\Form\Models\Form;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function getFrom(Form $form, mixed $key) : \Illuminate\Http\JsonResponse
    {
        $keyName = config('form.get_by_key_name');

        $data = $form->setTable(config('form.table_name'))->where($keyName, $key)->first();

        if (empty($data)) {
            return response()->json(['data' => []], 200);
        }

        return response()->json(['data' => $this->responseBuilder($data)], 200);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateFrom(Request $request, Form $form) : \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'slug'      => 'required|string:250',
            'cache_key' => 'required|string:250',
            'data'      => 'nullable|json',
            'source'    => 'required|string:250',
            'form_id'   => 'required|int',
        ]);

        $data = $form->setTable(config('form.table_name'))->updateOrCreate([
            'form_id' => $request->get('form_id'),
            'source'  => $request->get('source'),
        ], [
            'slug'      => $request->get('slug'),
            'cache_key' => $request->get('cache_key'),
            'data'      => $request->get("data"),
            'source'    => $request->get('source'),
        ]);

        return response()->json(['data' => $this->responseBuilder($data)], 201);
    }

    private function responseBuilder(Form $data) : array
    {
        return [
            'source'     => $data->source,
            'form_id'    => $data->form_id,
            'slug'       => $data->slug,
            'cache_key'  => $data->cache_key,
            'updated_at' => $data->updated_at,
            'data'       => json_decode($data->data),
        ];
    }
}