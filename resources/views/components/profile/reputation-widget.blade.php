<div class="bg-paper border border-rule rounded-sm p-8 shadow-sm relative overflow-hidden">
    {{-- Aesthetic Watermark Background --}}
    <div class="absolute top-0 right-0 opacity-[0.03] -mr-8 -mt-8 pointer-events-none select-none">
        <span class="text-[120px] font-serif">§</span>
    </div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-1">
                Current Standing
            </p>
            {{-- Resolves academic title dynamically from the User model --}}
            <h2 class="font-heading text-4xl font-bold {{ $user->getStandingColor() }}">
                {{ $user->getAcademicStanding() }}
            </h2>
        </div>

        <div class="flex gap-10">
            <div class="text-center md:text-right">
                <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-1">Total Endorsements</p>
                <p class="text-3xl font-bold text-ink">{{ number_format($user->reputation_points) }}</p>
            </div>
        </div>
    </div>
</div>