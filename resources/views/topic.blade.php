@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="panel">
            <a href="" class="btn btn-primary">New reply</a>
            <a href="/forum" class="btn btn-primary">Back</a>
        </div>

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{$title}}
                </div>
                <div class="panel-body">
                    {{$body}}
                </div>
                <div class="panel-footer">
                    Author: {{$author}},
                    posted on {{$time}}
                </div>
                <div class="container">
                    Tags: {{$tags}},
                    Category {{$category}}
                </div>
                <div class="container">
                    Visited: {{$visits}}
                </div>
            </div>
        </div>
    </div>



@endsection
