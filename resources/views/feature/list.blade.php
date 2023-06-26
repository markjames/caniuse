<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CSS Support Grid</title>


    <style type="text/css">
    body {
      font-family: sans-serif;
      font-size: 1em;
      padding: 1em;
    }
    menu,ul,li,dl,dt,dd {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    dt {
      font-weight: bold;
    }
    dd {
      padding-bottom: 0;
      margin-bottom: 0em;
      font-size: 0.85;
      color: rgba(0,0,0,0.8);
    }
    dd + dd {
      padding-bottom: 1em;
      border-bottom: 1px solid rgba(0,0,0,0.2);
      margin-bottom: 1em;
    }
    .support-dot {
      color: rgb(200,200,200);
    }
    .support-dot.support-dot--yes{
      color: rgb(60, 219, 126);
    }
    .support-dot.support-dot--partial {
      color: rgb(231, 178, 109);
    }
    .support-dot.support-dot--no {
      color: rgb(240, 153, 153);
    }

    .support-dot[data-browser='samsung'],
    .support-dot[data-browser='opera'],
    .support-dot[data-browser='ie'] {
      display: none;
    }
    </style>
</head>

<body>
    <h1>Nav</h1>
    <nav>
        <menu>
            @foreach ($categorygroups as $groupname => $categories)
                <li>
                    <h3>{{ $groupname }}</h3>
                    <ul>
                        @foreach ($categories as $categoryname => $navfeatures)
                            <li>
                                <h4>{{ $categoryname }}</h4>
                                <dl>
                                    @foreach ($navfeatures as $feature)
                                        <dt><a href="{{ $feature->documentationUrl() }}">{{ $feature->title }}</a></dt>
                                        <dd>
                                          @foreach ($feature->supportValues() as $browser => $support )
                                          <span class="support-dot support-dot--{{ $support }}" data-browser="{{$browser}}" data-support="{{$support}}" aria-label="Supported on {{ $browser }}? {{ $support }}" title="{{ $browser }}: {{ $support }}">⬤</span>
                                          @endforeach
                                        </dd>
                                        <dd>{{$feature->description}}</dd>
                                    @endforeach
                                </dl>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </menu>
    </nav>

    <h2>Features</h2>
    <ul>
        @forelse ($features as $feature)
            <li>{{ $feature->title }}</li>
        @endforeach
    </ul>

</body>

</html>
