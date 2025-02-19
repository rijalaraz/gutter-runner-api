@component('mail::message')

{{ trans('demande.message.greeting', [
    'titre' => $client->titre()->first()->name,
    'nom' => $client->nom,
]) }}

{{ trans('demande.message.confirm', [
    'nom_complet_assigne' => $assignee ? sprintf('%s %s', $assignee->firstname, $assignee->lastname) : $company->nom,
]) }}

{{ trans('demande.message.video', [
    'nombre_video_coches' => $demande->product_presentations()->count(),
]) }}

<ul>
@foreach ($videos as $video)
    <li><a href="{{ $video->url }}" target="_blank">{{ $video->name }}</a></li>
@endforeach
</ul>

{{ trans('demande.message.salutation') }}

{{ trans('demande.message.sender', [
    'company_name' => $company->nom,
]) }} <br />
{{ $company->address()->first()->postal_address }} <br />
{{ $company->telephone_sans_frais }} / {{ $company->telephone }} <br />
<a href="mailto:{{ $company->courriel_principal }}">{{ $company->courriel_principal }}</a>

@endcomponent