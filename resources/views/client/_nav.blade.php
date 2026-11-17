<a href="{{ route('client.dashboard') }}" @class(['active' => request()->routeIs('client.dashboard')])>Accueil</a>
<a href="{{ route('client.requests.index') }}" @class(['active' => request()->routeIs('client.requests.*')])>Mes demandes</a>
<a href="{{ route('client.quotes.index') }}" @class(['active' => request()->routeIs('client.quotes.*')])>Mes devis</a>
<a href="{{ route('client.projects.index') }}" @class(['active' => request()->routeIs('client.projects.*')])>Mes projets</a>