@props(['disabled' => false])

<input @disabled($disabled)
       {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; box-sizing: border-box;">
