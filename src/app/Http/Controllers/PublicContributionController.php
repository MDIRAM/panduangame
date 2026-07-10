<?php

namespace App\Http\Controllers;

use App\Models\WalkthroughContribution;
use Illuminate\View\View;

class PublicContributionController extends Controller
{
    public function show(WalkthroughContribution $contribution): View
    {
        abort_unless(
            $contribution->status === WalkthroughContribution::STATUS_PUBLISHED,
            404,
        );

        return view('contributions.show', [
            'contribution' => $contribution->load(['author', 'chapter', 'game', 'steps']),
        ]);
    }
}
