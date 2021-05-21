@foreach ($array as $comment)
    <div class="panel panel-default"  style="margin-left: {{$comment['margin_left']}}px">
        <div class="panel-heading">By <b> {{ $comment['username'] }} </b> on <i>{{ $comment['created_at'] }}</i></div>
        <div class="panel-body">{{ $comment['comment'] }}</div>
        <div class="panel-footer" align="right">
        @if ($comment['layer'] < 3)
            <button type="button" class="btn btn-default reply" id="{{ $comment['id'] }}">Reply</button>
        @endif
        </div>
        
    </div>
    @if (count ($comment['children']) > 0)
        @foreach ($comment['children'] as $array)
            @include('comment.comment', $array)
        @endforeach
    @endif
@endforeach