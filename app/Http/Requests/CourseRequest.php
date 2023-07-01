<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;


class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'slug' => 'required|unique:courses',
            'code' => 'required|unique:courses',
            'status' => 'required|boolean',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'statusCode' => 400,
            'message' => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'title.required' => 'Khóa học bắt buộc nhập!',
            'slug.required' => 'Slug bắt buộc nhập!',
            'slug.unique' => 'Slug đã tồn tại!',

            'code.required' => 'Mã khóa học bắt buộc nhập!',
            'code.unique' => 'Mã khóa học đã tồn tại!',

            'status.required' => 'Kiểu dữ liệu không hợp lệ!',
            'status.boolean' => 'Kiểu dữ liệu không hợp lệ!',

        ];
    }
}
