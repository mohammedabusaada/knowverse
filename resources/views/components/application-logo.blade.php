{{-- This image shows in Light Mode, hides in Dark Mode --}}
<img src="{{ asset('logo-light.png') }}" 
     alt="{{ config('app.name') }} Logo" 
     {{ $attributes->merge(['class' => 'dark:hidden object-contain']) }} 
/>

{{-- This image hides in Light Mode, shows in Dark Mode --}}
<img src="{{ asset('logo-dark.png') }}" 
     alt="{{ config('app.name') }} Logo" 
     {{ $attributes->merge(['class' => 'hidden dark:block object-contain']) }} 
/>