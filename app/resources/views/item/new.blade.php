<x-app-layout>
    <x-slot name="title">
        新規物品登録
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規物品登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-20 bg-white border-b border-gray-200">
                    <form method="POST" action="/items">
                        <div class="md:flex md:items-center mb-6">
                            @csrf
                            <div class="md:w-1/5">
                                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                                    物品名
                                </label>
                            </div>          
                            <div class="md:w-4/5">
                                @error('name')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="text" name="name">
                            </div>
                        </div>
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/5"></div>
                            <div class="md:w-4/5">
                                <button class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded my-4" type="submit">
                                    登録する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>