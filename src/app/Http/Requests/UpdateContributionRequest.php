<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('contribution')) ?? false;
    }

    public function rules(): array
    {
        return [
            'game_id' => [
                'required',
                'integer',
                Rule::exists('games', 'id')->where('is_published', true),
            ],
            'chapter_id' => [
                'required',
                'integer',
                Rule::exists('chapters', 'id')->where('game_id', $this->integer('game_id')),
            ],
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['required', 'string', 'min:30', 'max:1500'],
        ];
    }
}
