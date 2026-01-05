<?php

namespace App\Http\Requests\Reservation;

use App\Models\Flat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReservationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
//    { $flatId = $this->input('flat_id');
//
//        $flat = Flat::find($flatId);
        return [
            'start_time' => ['required', 'date', 'after:today'],
            'end_time' => ['required', 'after:start_time'],
//            'price' => ['required', 'decimal:0,2'],
            // وسوف عيوني هي خليت ال  flat id اجبارية مشان حمد ينجبر يرجعلي ياها ما ينسا هي كل الفكرة
            'flat_id' => ['required', 'exists:flats,id'],//لازم يكون موجود بجدول flats ضمن عمود id
//            'status' => ['required', 'in:available']//القيمة حصرا بدها تكون available اذا رجع sold or booked وقتها مالح يقدر يحجز
              'price' => ['required', 'decimal:0,2', 'min:1'],

        ];

    }

    ///////////////andrew was here/////////////////
    /// طيب مشان ما تنسا ال لارافيل وقت اعمل قواعد التحقق من validaton بتنشأ object للقواعد فهاد ال object بيتمرق افتراضي
    /// بالتوابع يلي بعدها يعني انا فيني ما حط ضمن تابع withValidator باراميترز بس هون وقتها مالح اقدر استفيد من التوابع
    ///  الاضافية تبع الشروط فيني سمي الباراميترز غير اسماء
        protected function withValidator($validator)
    {
        ////////////////كمان تابع after شي مشان ابني قواعد اضافية بس مو اكتر
        $validator->after(function ($validator) {
            $flatId = $this->input('flat_id');
            $flat   = Flat::find($flatId);


            if ($flat && $flat->available_date !== null) {
                if ($this->start_time < $flat->available_date) {
                    $validator->errors()->add('start_time', 'Start time must be after or equal to flat available_date.');
                }

                if ($this->end_time < $flat->available_date) {
                    $validator->errors()->add('end_time', 'End time must be after or equal to flat available_date.');
                }
            }

        });
    }



}
