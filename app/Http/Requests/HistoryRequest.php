<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
class HistoryRequest extends FormRequest
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
            'history' => 'required',
            'scores' => 'required',
            'exam_id' => 'required|numeric',
            'user_id' => 'required|numeric',
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
            'history.required' => 'Lịch sử bắt buộc nhập!',
            'scores.required' => 'Điểm số bắt buộc nhập!',

            'exam_id.required' => 'Exam_id bắt buộc nhập!',
            'exam_id.numeric' => 'Exam_id không hợp lệ!',

            'user_id.required' => 'User_id buộc nhập!',
            'user_id.numeric' => 'User_id không hợp lệ!',

        ];
    }
}
