namespace {{$namespace}};

{{$use}}

class {{$className}} extends {{$extends}}
{
    public function __construct(
        private readonly Service $service
    ){}

    @if(in_array('page',$methods))

    @endif

    @if(in_array('create',$methods))

    @endif

    @if(in_array('save',$methods))

    @endif

    @if(in_array('delete',$methods))

    @endif
}