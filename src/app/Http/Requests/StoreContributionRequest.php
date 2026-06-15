<?php

namespace App\Http\Requests;

use App\Models\WalkthroughContribution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', WalkthroughContribution::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'game_id' => [
                'required',
                'integer',
                Rule::exists('games', 'id')->where('is_published', true),
            ],
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['required', 'string', 'min:30', 'max:1500'],
        ];
    }
}
