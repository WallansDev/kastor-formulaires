
<h3>Confirmation</h3>
<ul>
    <li>Nom: {{ $data['name'] }}</li>
    <li>Email: {{ $data['email'] }}</li>
    <li>Âge: {{ $data['age'] }}</li>
    <li>Ville: {{ $data['city'] }}</li>
</ul>

<form method="POST" action="{{ route('form.submit') }}">
    @csrf
    <button type="submit">Envoyer</button>
</form>
