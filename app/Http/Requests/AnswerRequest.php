<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
class AnswerRequest extends FormRequest
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
            'result' => 'required|boolean',
            'question_id' => 'required|numeric',

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
            'title.required' => 'Câu trả lời bắt buộc nhập!',
            'result.required' => 'Kết quả bắt buộc nhập!',
            'result.boolean' => 'Kết quả không hợp lệ!',

            'question_id.required' => 'Id câu hỏi bắt buộc nhập!',
            'question_id.numeric' => 'Id câu hỏi không hợp lệ!',
        ];
    }
}
