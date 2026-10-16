@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-blue-700']) }}>
    {{ $value ?? $slot }}
</label>
