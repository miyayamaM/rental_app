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
                <div class="p-6 bg-white border-b border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3">
                    @foreach($items as $item)
                    <div class="max-w-sm rounded overflow-hidden shadow-lg">
                        <div class="px-6 py-4">
                            <span class="font-bold text-xl mb-2 px-3">
                                <a class="hover:text-gray-400" href="{{ route('item.show', ['id' => $item->id]) }}" dusk="show_link_{{ $item->id }}">
                                {{ $item->name }}
                                </a>
                            </span>
                            @if($item->isRentable())
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">貸出可</span>
                            @else
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">貸出中</span>
                            @endif
                        </div>
                        <div class="px-6 pt-4 pb-2">
                            <span class="inline-flex items-center justify-center"><a class=" px-2 py-1 text-xs font-bold leading-none text-gray-700 bg-gray-200 hover:text-gray-400 rounded" href="{{ route('reservation.new', ['id' => $item->id]) }}" dusk="reservation_link_{{ $item->id }}">予約する</a></span>
                            @if($item->isRentable())
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs leading-none text-grey-700 bg-grey-800"><a class="hover:text-gray-400" href="{{ route('item.edit', ['id' => $item->id]) }}" dusk="edit_link_{{ $item->id }}">編集する</a></span>
                                <span class="inline-flex items-center justify-center">
                                    <a class="hover:text-gray-400" href="{{ route('item.edit', ['id' => $item->id]) }}" dusk="edit_link_{{ $item->id }}">
                                        <form method="post" action="{{ route('item.destroy', ['id' => $item->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class=" px-2 py-1 text-xs leading-none text-grey-700 bg-grey-800" type="submit" onclick="return confirm('Are you sure?')" dusk="delete_link_{{ $item->id }}">削除する</button>
                                        </form>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


