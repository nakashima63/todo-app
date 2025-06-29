@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Todo List</h1>

                <form action="{{ route('todos.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="flex">
                        <input type="text" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Add a new todo">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">Add</button>
                    </div>
                </form>

                <ul>
                    @foreach ($todos as $todo)
                        <li class="flex items-center justify-between py-2 border-b">
                            <div class="flex items-center">
                                <form action="{{ route('todos.update', $todo) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="completed" value="0">
                                <input type="checkbox" name="completed" {{ $todo->completed ? 'checked' : '' }} onchange="this.form.submit()" class="mr-2">
                                <input type="hidden" name="title" value="{{$todo->title}}">
                                </form>
                                <a href="{{ route('todos.edit', $todo) }}" class="{{ $todo->completed ? 'line-through text-gray-500' : '' }}">{{ $todo->title }}</a>
                            </div>
                            <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection