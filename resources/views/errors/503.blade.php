@extends('layouts.app')

@section('title', 'Maintenance — AbiraSign')

@section('content')
<div style="
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 1.5rem;
    background: var(--bg-page);
">
    <div style="text-align: center; max-width: 480px; width: 100%;">
        <div style="font-size: 48px; margin-bottom: 1.25rem;">🔧</div>
        <div style="font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">We'll be right back</div>
        <div style="font-size: 15px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 2.5rem;">
            AbiraSign is undergoing scheduled maintenance. We apologize for the interruption and will be back online shortly.
        </div>
        <span style="
            display: inline-block;
            background: #E0F2FE;
            color: var(--teal);
            border-radius: 9999px;
            padding: 6px 18px;
            font-size: 13px;
            font-weight: 500;
        ">Maintenance in progress</span>
    </div>
</div>
@endsection
