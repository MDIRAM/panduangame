<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContributionStepRequest;
use App\Http\Requests\StoreContributionRequest;
use App\Http\Requests\UpdateContributionRequest;
use App\Http\Resources\ContributionStepResource;
use App\Http\Resources\PrivateContributionResource;
use App\Models\ContributionStep;
use App\Models\WalkthroughContribution;
use App\Support\RichText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyContributionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', WalkthroughContribution::class);

        $contributions = $request->user()
            ->walkthroughContributions()
            ->with(['game', 'chapter'])
            ->withCount('steps')
            ->latest()
            ->paginate(12);

        return PrivateContributionResource::collection($contributions);
    }

    public function store(StoreContributionRequest $request): PrivateContributionResource
    {
        $contribution = $request->user()->walkthroughContributions()->create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug($request->string('title')->toString()),
            'status' => WalkthroughContribution::STATUS_DRAFT,
        ]);

        return new PrivateContributionResource(
            $contribution->load(['game', 'chapter'])->loadCount('steps'),
        );
    }

    public function show(WalkthroughContribution $contribution): PrivateContributionResource
    {
        $this->authorize('view', $contribution);

        return new PrivateContributionResource(
            $contribution->load(['game', 'chapter', 'steps'])->loadCount('steps'),
        );
    }

    public function update(
        UpdateContributionRequest $request,
        WalkthroughContribution $contribution,
    ): PrivateContributionResource {
        $contribution->update([
            ...$request->validated(),
            'status' => WalkthroughContribution::STATUS_DRAFT,
            'moderation_notes' => null,
            'submitted_at' => null,
        ]);

        return new PrivateContributionResource(
            $contribution->load(['game', 'chapter', 'steps'])->loadCount('steps'),
        );
    }

    public function destroy(WalkthroughContribution $contribution): JsonResponse
    {
        $this->authorize('delete', $contribution);

        $contribution->delete();

        return response()->json(['message' => 'Draft walkthrough dihapus.']);
    }

    public function submit(WalkthroughContribution $contribution): PrivateContributionResource|JsonResponse
    {
        $this->authorize('submit', $contribution);

        if (! $contribution->steps()->exists()) {
            return response()->json([
                'message' => 'Tambahkan minimal satu langkah sebelum mengirim walkthrough.',
            ], 422);
        }

        $contribution->update([
            'status' => WalkthroughContribution::STATUS_PENDING,
            'submitted_at' => now(),
            'moderation_notes' => null,
        ]);

        return new PrivateContributionResource(
            $contribution->load(['game', 'chapter', 'steps'])->loadCount('steps'),
        );
    }

    public function storeStep(
        ContributionStepRequest $request,
        WalkthroughContribution $contribution,
    ): ContributionStepResource {
        $this->authorize('update', $contribution);

        $data = $this->stepData($request);
        $step = $contribution->steps()->create($data);

        return new ContributionStepResource($step);
    }

    public function updateStep(
        ContributionStepRequest $request,
        ContributionStep $step,
    ): ContributionStepResource {
        $this->authorize('update', $step);

        $data = $this->stepData($request, $step);
        $step->update($data);

        return new ContributionStepResource($step->fresh());
    }

    public function destroyStep(ContributionStep $step): JsonResponse
    {
        $this->authorize('delete', $step);

        if ($step->image_path) {
            Storage::disk('public')->delete($step->image_path);
        }

        $step->delete();

        return response()->json(['message' => 'Langkah walkthrough dihapus.']);
    }

    private function stepData(ContributionStepRequest $request, ?ContributionStep $step = null): array
    {
        $data = $request->safe()->except('image');
        $data['content'] = RichText::sanitize($data['content']);

        if ($request->hasFile('image')) {
            if ($step?->image_path) {
                Storage::disk('public')->delete($step->image_path);
            }

            $data['image_path'] = $request->file('image')->store(
                'contributions/' . $request->user()->id,
                'public',
            );
        }

        return $data;
    }

    private function uniqueSlug(string $title): string
    {
        do {
            $slug = Str::slug($title) . '-' . Str::lower(Str::random(6));
        } while (WalkthroughContribution::where('slug', $slug)->exists());

        return $slug;
    }
}
