@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Edit Todo</h1>

                <form action="{{ route('todos.update', $todo) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex">
                        <input type="text" name="title" value="{{ $todo->title }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection