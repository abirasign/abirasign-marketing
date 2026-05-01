<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    // ── Helper: get current active version ────────────────────────────────
    private function currentVersion(string $type)
    {
        return DB::table('policy_versions')
            ->where('type', $type)
            ->where('effective_date', '<=', now()->toDateString())
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->first();
    }

    // ── Helper: get all versions for a type ───────────────────────────────
    private function allVersions(string $type)
    {
        return DB::table('policy_versions')
            ->where('type', $type)
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->get();
    }

    // ── Current TOS ───────────────────────────────────────────────────────
    public function terms()
    {
        $policy = $this->currentVersion('tos');
        return view('legal.terms', compact('policy'));
    }

    // ── Current PP ────────────────────────────────────────────────────────
    public function privacy()
    {
        $policy = $this->currentVersion('pp');
        return view('legal.privacy', compact('policy'));
    }

    // ── Versioned TOS ─────────────────────────────────────────────────────
    public function termsVersion(string $version)
    {
        $policy = DB::table('policy_versions')
            ->where('type', 'tos')
            ->where('version', $version)
            ->first();

        if (!$policy) abort(404);
        return view('legal.terms', compact('policy'));
    }

    // ── Versioned PP ──────────────────────────────────────────────────────
    public function privacyVersion(string $version)
    {
        $policy = DB::table('policy_versions')
            ->where('type', 'pp')
            ->where('version', $version)
            ->first();

        if (!$policy) abort(404);
        return view('legal.privacy', compact('policy'));
    }

    // ── TOS Archive ───────────────────────────────────────────────────────
    public function termsArchive()
    {
        $versions = $this->allVersions('tos');
        $current  = $this->currentVersion('tos');
        $type     = 'tos';
        return view('legal.archive', compact('versions', 'current', 'type'));
    }

    // ── PP Archive ────────────────────────────────────────────────────────
    public function privacyArchive()
    {
        $versions = $this->allVersions('pp');
        $current  = $this->currentVersion('pp');
        $type     = 'pp';
        return view('legal.archive', compact('versions', 'current', 'type'));
    }
}
