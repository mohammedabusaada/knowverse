@props([
    'type',   // post | comment | user
    'id'
])

@auth
<button
    type="button"
    title="Report"
    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700"
    @click.stop="
        window.dispatchEvent(
            new CustomEvent('open-modal', {
                detail: 'report-{{ $type }}-{{ $id }}'
            })
        )
    "
>
    🚩 Report
</button>

<x-modal name="report-{{ $type }}-{{ $id }}" maxWidth="md">
    <form method="POST" action="{{ route('reports.store') }}" class="p-6 space-y-4">
        @csrf

        <input type="hidden" name="target_type" value="{{ $type }}">
        <input type="hidden" name="target_id" value="{{ $id }}">

        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            Report {{ ucfirst($type) }}
        </h2>

        <select name="reason_type" required
    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
    @foreach(\App\Enums\ReportReason::cases() as $reason)
        <option value="{{ $reason->value }}">
            {{ $reason->label() }}
        </option>
    @endforeach
</select>

<x-textarea
    name="reason"
    rows="3"
    placeholder="Additional details (optional)"
/>


        <div class="flex justify-end gap-2 pt-2">
            <x-button
                type="button"
                @click="
                    window.dispatchEvent(
                        new CustomEvent('close-modal', {
                            detail: 'report-{{ $type }}-{{ $id }}'
                        })
                    )
                "
                class="bg-gray-300 dark:bg-gray-600 text-black dark:text-white"
            >
                Cancel
            </x-button>

            <x-button class="bg-red-600 hover:bg-red-700 text-white">
                Submit Report
            </x-button>
        </div>
    </form>
</x-modal>
@endauth
