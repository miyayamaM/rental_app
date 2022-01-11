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
                        @foreach($reservation_users as $reservation_user)
                            <tbody>
                                <td class="border px-7 py-3">
                                   {{ $reservation_user->name }}
                                </td>
                                <td class="border px-7 py-3">
                                   {{ $reservation_user->pivot->start_date }}
                                </td>
                                <td class="border px-7 py-3">
                                    {{ $reservation_user->pivot->end_date }}
                                </td>
                            </tbody>
                        @endforeach
                    </table>
                </div>

                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="/reservations/items">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <div class="md:flex md:items-center mb-6">
                            <div class="col-span-3">
                                <label class=" block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-1" for="inline-full-name">
                                    貸出開始日
                                </label>
                            </div>
                            <div class="col-span-3">
                                @error('start_date')
                                <p class="text-red-500">{{ $message }}</p>
                                @enderror
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="date" name="start_date" id="start_date">
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="col-span-3">
                                <label class=" block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-1" for="inline-full-name">
                                    返却予定日
                                </label>
                            </div>
                            <div class="col-span-3">
                                @error('end_date')
                                <p class="text-red-500">{{ $message }}</p>
                                @enderror
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="date" name="end_date" id="end_date">
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="col-span-3"></div>
                            <div class="col-span-3">
                                <button class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded my-4" type="submit">
                                    予約する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
