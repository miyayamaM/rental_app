<x-app-layout>
    <x-slot name="title">
        物品一覧
    </x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('物品一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table>
                        <thead>
                            <tr>
                                <th class="px-7 py-3">物品名</th>
                                <th class="px-7 py-3">貸出状況</th>
                                <th class="px-7 py-3">予約</th>
                                <th class="px-7 py-3">編集</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td class="border px-7 py-3"><a href="{{ url('/items', $item->id) }}">{{ $item->name }}</a></td>
                                <td class="border px-7 py-3">貸出可</td>
                                <td class="border px-7 py-3">予約する</td>
                                <td class="border px-7 py-3">編集する</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>