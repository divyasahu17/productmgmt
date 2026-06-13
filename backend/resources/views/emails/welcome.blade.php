<x-mail::message>
# Welcome to ProductMgmt Marketplace!

Hi {{ $user->name }},

Thank you for registering with us. We are thrilled to have you!

Explore our premium curated collections and find exactly what you need.

<x-mail::button :url="config('app.frontend_url') ?? 'http://localhost:5173/'">
Start Shopping
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
