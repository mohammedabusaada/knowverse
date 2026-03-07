@props([
    'type',   // post | comment | user
    'id'
])

@auth
{{-- The Dropdown Item Button --}}
<button
    type="button"
    class="block w-full text-left px-4 py-2 text-sm font-serif text-accent-warm hover:bg-aged transition-colors"
    @click.stop="
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'report-{{ $type }}-{{ $id }}' }))
    "
>
    Report {{ ucfirst($type) }}
</button>

{{-- The Report Modal (Teleported to body to avoid z-index and overflow issues inside dropdowns) --}}
<template x-teleport="body">
    <x-modal name="report-{{ $type }}-{{ $id }}" maxWidth="md">
        <form method="POST" action="{{ route('reports.store') }}" class="p-6 bg-paper border border-rule">
            @csrf

            <input type="hidden" name="target_type" value="{{ $type }}">
            <input type="hidden" name="target_id" value="{{ $id }}">

            <h2 class="text-xl font-heading font-bold text-accent-warm mb-2">
                Report {{ ucfirst($type) }}
            </h2>
            
            <p class="font-serif text-[15px] italic text-muted mb-6 leading-relaxed">
                Our moderation team reviews all reports to maintain a scholarly and respectful environment.
            </p>

            <div class="space-y-5">
                {{-- Reason Select --}}
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Primary Reason</label>
                    <select name="reason_type" required
                        class="w-full px-3 py-2 border border-rule bg-transparent text-ink font-serif text-sm focus:ring-0 focus:border-ink transition-colors appearance-none rounded-none cursor-pointer">
                        <option value="" disabled selected>-- Select a violation --</option>
                        @foreach(\App\Enums\ReportReason::cases() as $reason)
                            <option value="{{ $reason->value }}">
                                {{ $reason->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Additional Details --}}
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Additional Context (Optional)</label>
                    <textarea
                        name="reason"
                        rows="3"
                        placeholder="Provide specific details to assist our review process..."
                        class="w-full px-3 py-2 border border-rule bg-transparent text-ink font-serif text-sm placeholder:text-muted/50 placeholder:italic focus:ring-0 focus:border-ink transition-colors resize-y"
                    ></textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-rule">
                <button
                    type="button"
                    @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'report-{{ $type }}-{{ $id }}' }))"
                    class="px-6 py-2 font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors"
                >
                    Cancel
                </button>

                <button type="submit" class="px-6 py-2 bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm">
                    Submit Report
                </button>
            </div>
        </form>
    </x-modal>
</template>
@endauth