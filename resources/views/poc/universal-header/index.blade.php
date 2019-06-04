@extends('layouts.empty')

@section('head')

<script>
  var _uh = _uh || [];
  _uh.push(['AllitaHost', 'https://pcinspectdev.ohiohome.org']);
  _uh.push(['Logo', 'https://static.wixstatic.com/media/64bb8d_0ca6465192ae42b89d419bbadaa42a05~mv2.png/v1/fill/w_171,h_169,al_c,usm_0.66_1.00_0.01/64bb8d_0ca6465192ae42b89d419bbadaa42a05~mv2.png']);
  _uh.push(['Css', '/poc_files/universal-header/universal-header.css']);
  _uh.push(['LeftItems', '<button>HELP!</button>']);
  _uh.push(['RightItems', '<button>🔔</button>']);
  (function() {
    var uh = document.createElement('script'); uh.type = 'text/javascript'; uh.async = true;
    //uh.src = 'https://devco.ohiohome.org/AuthorityOnlineALT/Unified/UnifiedHeader.aspx';
    uh.src = "{{config('app.url')}}/poc/universal-header/hosted.js{{ asset_version() }}";
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uh, s);
  })();
</script>

@endsection
