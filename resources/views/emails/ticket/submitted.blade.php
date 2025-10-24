<x-mail::message>
# Dear Emmanuel,

Your ticket has been submitted successfully.

<x-mail::table>
    
</x-mail::table>

<x-mail::button :url="{{ $url }}">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
