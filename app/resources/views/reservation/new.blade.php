<x-app-layout>
    <x-slot name="title">
        予約登録
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
                <p>現在の予約状況</p>
                    <table>
                        <thead>
                            <tr>
                                <th class="px-7 py-3">ユーザー</th>
                                <th class="px-7 py-3">貸出予定日</th>
                                <th class="px-7 py-3">返却予定日</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservation_users as $reservation_user)
                                <td class="border px-7 py-3">
                                   {{ $reservation_user->name }}
                                </td>
                                <td class="border px-7 py-3">
                                   {{ $reservation_user->pivot->start_date }}
                                </td>
                                <td class="border px-7 py-3">
                                    {{ $reservation_user->pivot->end_date }}
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
