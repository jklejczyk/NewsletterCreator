<!DOCTYPE html>
<html lang="pl">
<body>
<p>Witaj {{ $subscriber->name }},</p>

<p>oto Twój codzienny przegląd artykułów:</p>

@foreach($articles as $article)
    <h3>{{ $article->title }}</h3>
    <p>{{ $article->summary }}</p>
    <p><a href="{{ $article->url }}">Czytaj
            więcej</a></p>
    <br/>
@endforeach

</body>
</html>
