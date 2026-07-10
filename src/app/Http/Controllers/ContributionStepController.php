<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributionStepRequest;
use App\Models\ContributionStep;
use App\Models\WalkthroughContribution;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContributionStepController extends Controller
{
    public function store(
        ContributionStepRequest $request,
        WalkthroughContribution $contribution,
    ): RedirectResponse {
        $this->authorize('update', $contribution);

        $data = $request->safe()->except('image');
        $data['content'] = RichText::sanitize($data['content']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store(
                'contributions/' . $request->user()->id,
                'public',
            );
        }

        $contribution->steps()->create($data);

        return back()->with('success', 'Langkah walkthrough berhasil ditambahkan.');
    }

    public function edit(ContributionStep $step): View
    {
        $this->authorize('update', $step);

        return view('contributions.steps.edit', [
            'step' => $step->load('contribution.game'),
        ]);
    }

    public function update(
        ContributionStepRequest $request,
        ContributionStep $step,
    ): RedirectResponse {
        $this->authorize('update', $step);

        $data = $request->safe()->except('image');
        $data['content'] = RichText::sanitize($data['content']);

        if ($request->hasFile('image')) {
            $this->deleteImage($step);
            $data['image_path'] = $request->file('image')->store(
                'contributions/' . $request->user()->id,
                'public',
            );
        }

        $step->update($data);

        return redirect()
            ->route('contributions.edit', $step->contribution)
            ->with('success', 'Langkah walkthrough berhasil diperbarui.');
    }

    public function destroy(ContributionStep $step): RedirectResponse
    {
        $this->authorize('delete', $step);

        $contribution = $step->contribution;
        $this->deleteImage($step);
        $step->delete();

        return redirect()
            ->route('contributions.edit', $contribution)
            ->with('success', 'Langkah walkthrough dihapus.');
    }

    private function deleteImage(ContributionStep $step): void
    {
        if ($step->image_path) {
            Storage::disk('public')->delete($step->image_path);
        }
    }
}
