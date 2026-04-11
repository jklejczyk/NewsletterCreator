<!DOCTYPE html>
<html lang="pl">
<body>
    <p>Witaj {{ $subscriber->name }},</p>

    <p>kliknij w link poniżej, aby potwierdzić zapis do newslettera:</p>

    <p><a href="{{ $confirmationUrl }}">Potwierdź zapis</a></p>
</body>
</html>
