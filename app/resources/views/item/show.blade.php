<x-app-layout>
    <x-slot name="title">
        物品詳細
    </x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3">状況：</div>
                        <div class="col-span-3">貸出者：</div>
                        <div class="col-span-3">返却予定日：</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>