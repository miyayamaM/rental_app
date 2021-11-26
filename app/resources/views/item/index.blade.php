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
                                <th class="px-7 py-3">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td class="border px-7 py-3"><a class="hover:text-gray-400" href="{{ route('item.show', ['id' => $item->id]) }}">{{ $item->name }}</a></td>
                                @if($item->isRentable())
                                    <td class="border px-7 py-3">
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">貸出可</span>
                                    </td>
                                    <td class="border px-7 py-3">予約する</td>
                                    <td class="border px-7 py-3"><a class="hover:text-gray-400" href="{{ route('item.edit', ['id' => $item->id]) }}" dusk="edit_link_{{ $item->id }}">編集する</a></td>
                                    <td class="border px-7 py-3">
                                        <form method="post" action="{{ route('item.destroy', ['id' => $item->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')" dusk="delete_link_{{ $item->id }}">削除する</button>
                                        </form>
                                    </td>
                                @else
                                    <td class="border px-7 py-3">
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">貸出中</span>
                                    </td>
                                    <td class="border px-7 py-3">予約する</td>
                                    <td class="border px-7 py-3"></td>
                                    <td class="border px-7 py-3"></td>
                                @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>