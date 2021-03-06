<x-app-layout>
    <x-slot name="title">
        予約照会
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user_name. 'が予約している物品' }}
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
                                <th class="px-7 py-3">貸出予定日</th>
                                <th class="px-7 py-3">返却予定日</th>
                                <th class="px-7 py-3">返却する</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservation_items as $item)
                            <tr>
                                <td class="border px-7 py-3"><a class="hover:text-gray-400" href="{{ route('item.show', ['id' => $item->id]) }}">{{ $item->name }}</a></td>
                                <td class="border px-7 py-3">{{ $item->pivot->start_date }}</td>
                                <td class="border px-7 py-3">{{ $item->pivot->end_date }}</td>
                                <td class="border px-7 py-3">
                                    <form method="post" action="/reservations/{{ $item->pivot->id }}}">
                                            @csrf
                                            @method('DELETE')
                                            <button dusk="reservation_cancel_link_{{ $item->id }}">キャンセル</button>
                                    </form>
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
