@extends('layouts.main')

@section('content')
    <section class="bg-blue-700 p-8 text-white lg:p-12">
        <h2 class="text-3xl font-bold lg:text-5xl">Consumer Protection Law System</h2>
        <p class="mt-4 max-w-3xl text-lg text-blue-100">
            Your comprehensive platform for understanding and navigating consumer protection laws.
            Get instant legal assistance, browse regulations, and learn from real cases.
        </p>
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('chat.index') }}" class="rounded-lg bg-white px-6 py-3 font-semibold text-blue-700">Start Chatting</a>
            <a href="#" class="rounded-lg border border-blue-200 px-6 py-3 font-semibold text-white">Browse Laws</a>
        </div>
    </section>

    <section class="p-8 lg:p-12">
        <h3 class="text-3xl font-bold">Platform Features</h3>
        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <h4 class="text-2xl font-semibold">Legal Assistant Chatbot</h4>
                <p class="mt-2 text-slate-600">Get instant answers to consumer protection law questions.</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <h4 class="text-2xl font-semibold">Data Browsing</h4>
                <p class="mt-2 text-slate-600">Browse comprehensive consumer protection regulations and laws.</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <h4 class="text-2xl font-semibold">Case Studies</h4>
                <p class="mt-2 text-slate-600">Learn from real-world consumer protection cases.</p>
            </div>
        </div>
    </section>
@endsection
