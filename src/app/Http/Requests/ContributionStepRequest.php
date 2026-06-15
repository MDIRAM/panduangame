<?php

namespace App\Http\Requests;

use App\Models\ContributionStep;
use App\Models\WalkthroughContribution;
use Illuminate\Foundation\Http\FormRequest;

class ContributionStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        $step = $this->route('step');

        if ($step instanceof ContributionStep) {
            return $this->user()?->can('update', $step) ?? false;
        }

        $contribution = $this->route('contribution');

        return $contribution instanceof WalkthroughContribution
            && ($this->user()?->can('update', $contribution) ?? false);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'min:20', 'max:5000'],
            'order' => ['required', 'integer', 'min:1', 'max:999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
